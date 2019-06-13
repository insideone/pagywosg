
# SteamAuthenticationBundle
A Symfony4 Bundle that provides authentication via Steam for your application.

## Installation & Configuration

Simpley require the bundle via Composer and add. Use the given flex recipe during the install process.
`composer require knojector/steam-authentication-bundle`

----------
In your `.env`  file a new entry for your Steam API key was generated. You can obtain your Steam API key here: https://steamcommunity.com/dev/apikey

**login_route** The route the user is redirected to after Steam Login

**login_redirect** The route the user is redirected to if the login was successfull

**user_class** Classname of your User Entity

----------
Make sure your User Entity extends from the `Knojector\SteamAuthenticationBundle\User\AbstractSteamUser` class
```php
<?php

namespace App\Entity;

use Knojector\SteamAuthenticationBundle\User\AbstractSteamUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;

/**
 * @author Knojector <dev@404-labs.xyz>
 *
 * @ORM\Entity()
 */
class User extends AbstractSteamUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->roles = [];
    }
    
    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = [];
        foreach ($this->roles as $role) {
            $roles[] = new Role($role);
        }

        return $roles;
    }
}
```


----------

Finally you just have to configure your firewall. A working example might look like this
```yaml
security:
    providers:
        steam_user_provider:
            id: Knojector\SteamAuthenticationBundle\Security\User\SteamUserProvider
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            anonymous: ~
            pattern: ^/
            provider: steam_user_provider
            steam: true
            logout:
                path:   /logout
                target: /

```

----------

To display the "Login via Steam" button simply include this snippet in your template
```twig
{% include '@KnojectorSteamAuthentication/login.html.twig' %}
```
