<?php

namespace App\Framework\Controller;

use App\Entity\User;
use App\Enum\ErrorType;
use App\Framework\Exceptions\JsonSchemaValidationException;
use App\Framework\Exceptions\NotImplementedException;
use App\Framework\Exceptions\ReferenceLoadException;
use App\Security\Role;
use App\Service\PermissionTeller;
use App\Service\ReferenceLoader;
use App\Validation\ErrorFormatter;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Exception;
use Opis\JsonSchema\Exception\SchemaNotFoundException;
use Opis\JsonSchema\Loaders\File;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class BaseController extends AbstractController
{
    /** @var SerializerInterface */
    protected $serializer;

    /** @var File */
    protected $schemaLoader;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var ReferenceLoader */
    protected $referenceLoader;

    /** @var ErrorFormatter */
    protected $validationErrorFormatter;

    /** @var PermissionTeller */
    protected $permissionTeller;

    public function setSchemaLoader(File $schemaLoader)
    {
        $this->schemaLoader = $schemaLoader;
        return $this;
    }

    public function setValidationErrorFormatter(ErrorFormatter $errorFormatter)
    {
        $this->validationErrorFormatter = $errorFormatter;
        return $this;
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        return $this;
    }

    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        return $this;
    }

    public function setReferenceLoader(ReferenceLoader $referenceLoader)
    {
        $this->referenceLoader = $referenceLoader;
        return $this;
    }

    public function setPermissionTeller(PermissionTeller $permissionTeller)
    {
        $this->permissionTeller = $permissionTeller;
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        return parent::getUser();
    }

    /**
     * @throws NotImplementedException
     */
    protected function getServedEntity()
    {
        throw new NotImplementedException;
    }

    /**
     * @param string $class
     * @param array $serializerContext
     * @return object
     * @throws NotImplementedException
     * @throws ORMException
     * @throws JsonSchemaValidationException
     * @throws ReferenceLoadException
     */
    protected function getValidatedEntity($serializerContext = [], string $class = null)
    {
        if (!$class) {
            $class = $this->getServedEntity();
        }

        if (!$class) {
            throw new Exception("Can't serve validation for unknown class");
        }

        $validation = $this->validate();
        if ($validation->hasErrors()) {
            throw new JsonSchemaValidationException($validation);
        }

        $request = $this->get('request_stack')->getCurrentRequest();
        $routeName = $request->attributes->get('_route');

        $entity = $this->serializer->deserialize(
            $request->getContent(),
            $class,
            'json',
            $serializerContext + ['groups' => ["import-{$routeName}", "import"]]
        );

        return $this->loadReferences($entity);
    }

    /**
     * @param object $entity
     * @return object
     * @throws ORMException
     * @throws ReferenceLoadException
     */
    protected function loadReferences(object $entity)
    {
        return $this->referenceLoader->serve($entity);
    }

    /** @noinspection PhpDocRedundantThrowsInspection */
    /**
     * @param object $event
     * @throws ForeignKeyConstraintViolationException
     * @throws UniqueConstraintViolationException
     */
    protected function saveEntity(object $event)
    {
        $this->em->persist($event);
        $this->em->flush();
    }

    protected function removeEntity(object $entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * @return ValidationResult|null
     * @throws SchemaNotFoundException
     */
    public function validate()
    {
        $request = $this->get('request_stack')->getCurrentRequest();

        $route = $request->attributes->get('_route');

        $schemaId = "{$route}.json";
        $schema = $this->schemaLoader->loadSchema($schemaId);
        if (!$schema) {
            throw new SchemaNotFoundException($schemaId);
        }

        return (new Validator)->schemaValidation(
            json_decode(json_encode($request->request->all())),
            $schema,
            -1,
            $this->schemaLoader
        );
    }

    protected function response($data = [], $context = [], $httpCode = Response::HTTP_OK)
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $routeName = $request->attributes->get('_route');

        $response = new JsonResponse('', $httpCode);

        $defaultCallbacks = [
            'steamId' => function ($steamId) {
                // making id string, cause otherwise id can be changed to a different id
                // due to them(ids) being very big int numbers.
                return (string)$steamId;
            },
            'roles' => function ($roles) {
                return array_map(function ($role) {
                    return $role instanceof Role ? (string)$role : $role;
                }, $roles);
            },
        ];

        /** @noinspection PhpTemplateMissingInspection */
        return $this->render(
            'json',
            [
                '_context' => [
                    'callbacks' => ($context['callbacks'] ?? []) + $defaultCallbacks
                ] + $context + [
                    'groups' => ['export', "export-{$routeName}"],
                    'datetime_format' => 'Y-m-d',
                ],
            ] + $data,
            $response
        );
    }

    protected function forbiddenResponse($reason)
    {
        return $this->errorsResponse([[
            'type' => ErrorType::FORBIDDEN,
            'message' => "You aren't allowed to do that because {$reason}",
        ]], [], Response::HTTP_FORBIDDEN);
    }

    protected function notFoundResponse($name)
    {
        $name = lcfirst($name);
        return $this->errorsResponse([[
            'type' => ErrorType::NOT_FOUND,
            'message' => "Requested {$name} is not found"
        ]], [], Response::HTTP_NOT_FOUND);
    }

    protected function errorResponse($message)
    {
        return $this->errorsResponse([[
            'message' => $message,
        ]]);
    }

    protected function exceptionResponse(Exception $e)
    {
        if ($e instanceof JsonSchemaValidationException) {
            return $this->errorsResponse(['errors' => $this->validationErrorFormatter->formatResult($e->getValidation())]);
        } else {
            return $this->errorsResponse([[
                'type' => 'exception',
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]]);
        }
    }

    protected function errorsResponse($errors, $context = [], $httpCode = Response::HTTP_OK)
    {
        return $this->response([
            'errors' => $errors,
        ], $context, $httpCode);
    }

    protected function isGranted($operation, $subject = null, $checkSubOperations = true): bool
    {
        return $this->permissionTeller->isGranted($operation, $subject, $checkSubOperations);
    }
}
