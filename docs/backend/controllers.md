# Controllers

Controllers must be extended from `App\Framework\Controller\BaseController`.

If controller action accepts data, it must be verified and deserialized through `getValidatedEntity` method. More about [validation](validation.md).

Actions must respect HTTP methods such as:

* POST - for entity creation
* PUT - for entity editing that can be repeated without any new results
* DELETE - for entity deletion
* GET - for entity(ies) reading
