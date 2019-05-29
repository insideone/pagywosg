# Security

Security system is based on [fxp/security-bundle package](https://github.com/fxpio/fxp-security-bundle/tree/1.1)

You can check if user is granted an operation with this code (in controller):
```php
<?php
if ($this->isGranted(EntityPermission::UPDATE_ANY, $entity)) {
    // user can do that
}
```

* EntityPermission - replace with existed entity class from `src/Security/Permission` folder or create a new one based on `CrudOwnHostedPermissions` class
* $entity - object that is checked for access. Optional

All operations must be postfixed with these modifiers:

* ANY - means any object
* OWN - means an object that the user owns
* HOSTED - means an object that user doesn't own, but the user owns its parent object

Thus:

* calling for checking *_OWN operation will also execute check for *_HOSTED and *_ANY operations. Calling for *_HOSTED will check *_ANY operation

Also:

* Sometimes it's not enough to add permission to database. If you add permission for new entity, then you should also add this entity to `permissions` in `fxp_security.yaml`. Otherwise all isGranted check for this entity will be false positive