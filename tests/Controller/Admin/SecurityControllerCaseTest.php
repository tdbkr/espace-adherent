<?php

namespace Tests\App\Controller\Admin;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\App\AbstractAdminWebTestCase;
use Tests\App\Controller\ControllerTestTrait;

#[Group('functional')]
#[Group('security')]
class SecurityControllerCaseTest extends AbstractAdminWebTestCase
{
    use ControllerTestTrait;

    public function testAuthenticationIsSuccessful(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/login');

        $this->assertResponseStatusCode(Response::HTTP_OK, $this->client->getResponse());
        $this->assertCount(0, $crawler->filter('#auth-error'));

        $this->client->submit($crawler->selectButton('Connexion')->form([
            '_username' => 'admin@en-marche-dev.fr',
            '_password' => 'admin',
        ]));

        $this->assertResponseStatusCode(Response::HTTP_FOUND, $this->client->getResponse());
        $this->assertClientIsRedirectedTo('/dashboard', $this->client, true);

        $this->client->followRedirect();
        $this->assertResponseStatusCode(Response::HTTP_OK, $this->client->getResponse());
    }

    public function testAuthenticationIfSecretCode(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/login');

        $this->assertResponseStatusCode(Response::HTTP_OK, $this->client->getResponse());
        $this->assertCount(0, $crawler->filter('#auth-error'));

        $this->client->submit($crawler->selectButton('Connexion')->form([
            '_username' => 'titouan.galopin@en-marche.fr',
            '_password' => 'secret!12345',
        ]));

        $this->assertResponseStatusCode(Response::HTTP_FOUND, $this->client->getResponse());
        $this->assertClientIsRedirectedTo('/dashboard', $this->client, true);

        $this->client->followRedirect();
        $this->assertClientIsRedirectedTo('/login/2fa', $this->client, true);

        $crawler = $this->client->followRedirect();

        $this->assertResponseStatusCode(Response::HTTP_OK, $this->client->getResponse());
        $this->assertCount(1, $crawler->filter('#_auth_code'));

        $this->client->submit($crawler->selectButton('Je me connecte')->form([
            '_auth_code' => '123456',
        ]));
        $this->assertResponseStatusCode(Response::HTTP_FOUND, $this->client->getResponse());
        $this->assertClientIsRedirectedTo('/login/2fa', $this->client, true);

        $this->client->followRedirect();
        $this->assertResponseStatusCode(Response::HTTP_OK, $this->client->getResponse());
        $this->assertCount(1, $crawler->filter('#_auth_code'));
    }

    public function testAuthenticationFailedWhenAdminIsNotActivated(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/login');

        $this->client->submit($crawler->selectButton('Connexion')->form([
            '_username' => 'jean.dupond@en-marche.fr',
            '_password' => 'secret!12345',
        ]));

        $this->client->followRedirect();

        $this->assertStringContainsString(
            'adresse email et le mot de passe que vous avez saisis ne correspondent pas.',
            $this->client->getResponse()->getContent()
        );
    }

    #[DataProvider('provideInvalidCredentials')]
    public function testLoginCheckFails(string $username, string $password): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/login');

        $this->assertResponseStatusCode(Response::HTTP_OK, $this->client->getResponse());
        $this->assertCount(0, $crawler->filter('#auth-error'));

        $this->client->submit($crawler->selectButton('Connexion')->form([
            '_username' => $username,
            '_password' => $password,
        ]));

        $this->assertResponseStatusCode(Response::HTTP_FOUND, $this->client->getResponse());
        $this->assertClientIsRedirectedTo('/login', $this->client, true);

        $crawler = $this->client->followRedirect();

        $this->assertResponseStatusCode(Response::HTTP_OK, $this->client->getResponse());
        $this->assertCount(1, $error = $crawler->filter('#auth-error'));
        $this->assertSame('L\'adresse email et le mot de passe que vous avez saisis ne correspondent pas.', trim($error->text()));
    }

    public static function provideInvalidCredentials(): array
    {
        return [
            'Valid username, invalid password' => [
                'titouan.galopin@en-marche.fr',
                'foo-bar-pass',
            ],
            'Invalid username, valid password' => [
                'carl999@example.fr',
                'secret!12345',
            ],
            'Invalid username, invalid password' => [
                'carl999@example.fr',
                'foo-bar-pass',
            ],
        ];
    }
}
