@app
@renaissance_user
Feature:
    In order to protect the APP
    I should be blocked if I try to login too many time

    Scenario: Known user is blocked after 5 attempts
        Given I am on "/connexion"

        When I fill in the following:
            | _username | luciole1989@spambox.fr |
            | _password | wrongPassword          |
        And I press "Me connecter"
        Then I should see "L'adresse email et le mot de passe que vous avez saisis ne correspondent pas."

        When I fill in the following:
            | _username | lUciole1989@spambox.fr |
            | _password | wrongPassword          |
        And I press "Me connecter"
        Then I should see "L'adresse email et le mot de passe que vous avez saisis ne correspondent pas."

        When I fill in the following:
            | _username | Luciole1989@spambox.fr |
            | _password | wrongPassword          |
        And I press "Me connecter"
        Then I should see "L'adresse email et le mot de passe que vous avez saisis ne correspondent pas."

        When I fill in the following:
            | _username | luciole1989@spambox.fr |
            | _password | wrongPassword          |
        And I press "Me connecter"
        Then I should see "L'adresse email et le mot de passe que vous avez saisis ne correspondent pas."

        When I fill in the following:
            | _username | luciole1989@spambox.fr |
            | _password | wrongPassword          |
        And I press "Me connecter"
        Then I should see "L'adresse email et le mot de passe que vous avez saisis ne correspondent pas."

        # Refuse login with good credential
        When I fill in the following:
            | _username | luciole1989@spambox.fr |
            | _password | EnMarche2017           |
        And I press "Me connecter"
        Then I should see "Vous avez effectué 5 tentatives de connexion erronées. Veuillez attendre 1 minute avant de réessayer."

    Scenario: Unknown user is blocked after 5 attempts
        Given I am on "/connexion"

        When I fill in the following:
            | _username | unkown_not_in_em_db@spambox.fr |
            | _password | wrongPassword                  |
        And I press "Me connecter"
        Then I should see "L'adresse email et le mot de passe que vous avez saisis ne correspondent pas."

        When I fill in the following:
            | _username | unkown_not_in_em_db@spambox.fr |
            | _password | wrongPassword                  |
        And I press "Me connecter"
        Then I should see "L'adresse email et le mot de passe que vous avez saisis ne correspondent pas."

        When I fill in the following:
            | _username | unkown_not_in_em_db@spambox.fr |
            | _password | wrongPassword                  |
        And I press "Me connecter"
        Then I should see "L'adresse email et le mot de passe que vous avez saisis ne correspondent pas."

        When I fill in the following:
            | _username | unkown_not_in_em_db@spambox.fr |
            | _password | wrongPassword                  |
        And I press "Me connecter"
        Then I should see "L'adresse email et le mot de passe que vous avez saisis ne correspondent pas."

        When I fill in the following:
            | _username | unkown_not_in_em_db@spambox.fr |
            | _password | wrongPassword                  |
        And I press "Me connecter"
        Then I should see "L'adresse email et le mot de passe que vous avez saisis ne correspondent pas."

        # Refuse login
        When I fill in the following:
            | _username | unkown_not_in_em_db@spambox.fr |
            | _password | wrongPassword                  |
        And I press "Me connecter"
        Then I should see "Vous avez effectué 5 tentatives de connexion erronées. Veuillez attendre 1 minute avant de réessayer."
