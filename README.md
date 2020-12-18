# Asset Integrity Management System

## Project setup:
- setup docker override configuration
    - copy `docker-compose.override.yml.dist` to `docker-compose.override.yml`
    - it is by default running through Traefik on a domain `aims.loc`
        - if you want to use this setup, add `127.0.0.1 aims.loc` to your `hosts` file (`/etc/hosts`)
- run `make dev`
    - or `sudo make dev` if you have a non-standard setup on your machine
    
## Namespaces
Namespaces are setup according to the internal DDD policy and is set up through `composer.json` and it's autoload functionality.

When adding a new domain, don't forget to register it in services. 
    
## Useful setups
CI is running on GitLab and checks the following:
- Static analysis
    - with PHPStan on level 8
- Code style fixes
    - with PHP CS Fixer
- Tests
    - all tests written with PHPUnit

To make developing faster and simpler, we have prepared some dev utils that will ease this process:
- git hooks
    - PHPStan will block commit with message
    - PHP CS Fixer will format your committed code before commit
    - commit message and branch name will be validated according to [our rules](https://gitlab.com/asynclabs.co/knowledge/-/blob/master/Development/Git-flow.md)
    - **setup:** `make setup_hooks`
    - **cleanup:** `make clear_hooks`
- checkups for dev process
    - PHPStan: `make stan`
    - PHP CS Fixer: `make csfix`
    - tests: `make tests`
- External useful bundles included:
    - [Gedmo - SoftDeletable behaviour](https://github.com/Atlantic18/DoctrineExtensions/blob/v2.4.x/doc/softdeleteable.md#traits)

## Git flow
We are following commit message setup and branch naming according to [our rules](https://gitlab.com/asynclabs.co/knowledge/-/blob/master/Development/Git-flow.md).

These are being enforce with git hooks, as we mentioned earlier. 

## Adding a new domain
There are several steps to adding a new domain:
1. Create new directory in `src`
    - Create `{domain-name}-services.yml` in that directory
        - add connection to that file in `config/services.yaml`:
            ```
            - { resource: "../src/{DomainName}/{domain-name}-services.yml" }
            ```
    - Create layer directories
1. Add namespace to `composer.json`'s `autoload` section:
    ```
    "{DomainName}\\": "src/{DomainName}",
    ``` 
1. If you're adding Controllers:
    - define resource section in `yml` file:
    ```
    {DomainName}\Infrastructure\Http\Controller\:
      resource: "Infrastructure/Http/Controller/*"
      tags: ["controller.service_arguments"]
    ``` 
    - add controller definition to `config/routes/annotations.yaml`:
    ```
    {domain-name}:
      resource: '../../src/{DomainName}/Infrastructure/Http/Controller'
      type: annotation
    ```
1. If you're adding Twig templates:
    - create directory `src/{DomainName}/Infrastructure/Templates`
    - add alias for the domain in `config/packages/twig.yaml`:
    ```
    'src/{DomainName}/Infrastructure/Templates': '{DomainName}'
    ```
## JWT Authorization keys generation

1. Navigate to your project root directory and run the following command to create a  new directory to hold the keys:
    ```shell script
    mkdir config/jwt
    ```

2. Next, copy this line to create a private key, but change its path to the var/jwt directory:
    ```
    openssl genrsa -out config/jwt/private.pem -aes256 4096
    ```
    This will ask you for a password so give it one (e.g. `aims`). It adds another layer of security in case somebody gets your private key.

3. Last but not least, copy this final line:
    ```
    openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
    ```
    Type in the password you just set. This creates a public key. It'll be used to verify that a JWT hasn't been tampered with. It's not private, but you probably won't need to share it, unless someone else - or some other app - needs to also verify that a JWT we created is valid. 

4. Open your .env file and update JWT parameters:
    ```dotenv
    ###> lexik/jwt-authentication-bundle ###
    JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
    JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
    JWT_PASSPHRASE=aims
    ###< lexik/jwt-authentication-bundle ###
    ```
   > **NOTICE:** `JWT_PASSPHRASE` parameter should be set to password you previously entered during keys generation.

For additional adjustments and functionalities, check the [bundle documentation](https://github.com/lexik/LexikJWTAuthenticationBundle).
