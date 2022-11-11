<?php

namespace App\Controller;

use App\Application\Project\ContentBundle\Attributes\ARR;
use App\Application\Project\ContentBundle\Controller\DefaultAbstractController;
use App\Entity\Diagram;
use App\Application\Project\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


##[IsGranted('IS_AUTHENTICATED_FULLY')]
#[OA\Tag(name: 'Diagram')]
#[ARR(groupName: 'Diagrama', description: 'Permissões Api do modulo Diagrama')]
#[Route('/api/diagram', name: 'api_diagram_')]
class DiagramApiController extends DefaultAbstractController
{

    private function getRepository(): string
    {
        return Diagram::class;
    }

    /** @throws ExceptionInterface */
    #[OA\Response(
        response: 200,
        description: 'Return data list',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'int'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'structure', type: 'string'),
            ],
            type: 'object'
        )
    )]
    #[ARR(routerName: 'listAction', role: "ROLE_API_DIAGRAM_LIST", title: 'Listar')]
    #[Route('', name: 'list', methods: ['GET'])]
    public function listAction(ManagerRegistry $doctrine): Response
    {
        $this->validateAccess("ROLE_API_DIAGRAM_LIST");

        $user = $this->getUser();

        $objectData = $doctrine->getRepository( $this->getRepository() )->findBy(['user' => $user]);

        $serializer = new Serializer([new ObjectNormalizer()]);
        $response = $serializer->normalize($objectData, null, [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'name',
                'description',
                'structure',
            ],
            AbstractNormalizer::IGNORED_ATTRIBUTES => [
                'user',
                'application'
            ]
        ]);

        return $this->json($response);
    }

    #[OA\RequestBody(
        description: 'Json Payload',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', nullable: false),
                new OA\Property(property: 'description', type: 'string', nullable: false),
                new OA\Property(property: 'structure',  type: 'string', nullable: false),
            ],
            type: 'object'
        )
    )]
    #[Route('', name: 'create', methods: ['POST'])]
    #[ARR(routerName: 'createAction', role: "ROLE_API_DIAGRAM_CREATE", title: 'Criar')]
    public function createAction(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->validateAccess("ROLE_API_DIAGRAM_CREATE");

        $user = $this->getUser();

        $entityManager = $doctrine->getManager();

        $parameters = [
            'name'         => [ 'type' => 'string', 'required' => true, 'nullable' => false ],
            'description'  => [ 'type' => 'string', 'required' => false, 'nullable' => true ],
            'structure'    => [ 'type' => 'string', 'required' => false, 'nullable' => true ],
        ];

        $baseStructure = json_encode( $this->createBaseStructure() );

        $requestBody = json_decode($request->getContent());

        if($this->validateJsonRequestBody($requestBody, $parameters))
            return $this->validateJsonRequestBody($requestBody, $parameters);


        $data = new Diagram();

        if(property_exists($requestBody, 'name'))
            $data->setName($requestBody->name);

        if(property_exists($requestBody, 'description'))
            $data->setDescription($requestBody->description);

        //if(property_exists($requestBody, 'structure'))
            $data->setStructure($baseStructure);


        $data->setUser($user);

        $entityManager->persist($data);
        $entityManager->flush();

        return $this->json([
            'status' => true,
            'message' => 'Created new { Diagram } successfully with id ' . $data->getId(),
        ]);
    }

    /** @throws ExceptionInterface */
    #[OA\Response(
        response: 200,
        description: 'Return data',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'int'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'active', type: 'boolean'),
                new OA\Property(property: 'logo', type: 'object'),
            ],
            type: 'object'
        )
    )]
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[ARR(routerName: 'showAction', role: "ROLE_API_DIAGRAM_SHOW", title: 'Visualizar')]
    public function showAction(ManagerRegistry $doctrine, int $id): Response
    {
        $this->validateAccess("ROLE_API_DIAGRAM_SHOW");
        $user = $this->getUser();

        $objectData = $doctrine->getRepository($this->getRepository())->findOneBy(['id' => $id ,'user' => $user]);

        if (!$objectData)
            return $this->json([
                'status' => false,
                'message' => "Diagrama não encontrado.",
            ], 404);

        $serializer = new Serializer([new ObjectNormalizer()]);
        $response = $serializer->normalize($objectData, null, [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'name',
                'description',
                'structure',
            ],
            AbstractNormalizer::IGNORED_ATTRIBUTES => [
                'logo'
            ]
        ]);

        //$baseStructure = json_encode( $this->createBaseStructure() );

        //$response['structure'] = $baseStructure;

        return $this->json($response);
    }


    #[OA\RequestBody(
        description: 'Json Payload',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', nullable: false),
                new OA\Property(property: 'description', type: 'string', nullable: false),
                new OA\Property(property: 'structure',  type: 'string', nullable: false),
            ],
            type: 'object'
        )
    )]
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[ARR(routerName: 'editAction', role: "ROLE_API_DIAGRAM_EDIT", title: 'Editar')]
    public function editAction(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();

        $parameters = [
            'name'         => [ 'type' => 'string', 'required' => false, 'nullable' => true ],
            'description'  => [ 'type' => 'string', 'required' => false, 'nullable' => true ],
            'structure'    => [ 'type' => 'string', 'required' => false, 'nullable' => true ],
        ];

        $requestBody = json_decode($request->getContent());

        if($this->validateJsonRequestBody($requestBody, $parameters))
            return $this->validateJsonRequestBody($requestBody, $parameters);


        $data = $entityManager->getRepository(Diagram::class)->find($id);

        if (!$data)
            return $this->json('No Diagram found for id' . $id, 404);


        if(property_exists($requestBody, 'name'))
            $data->setName($requestBody->name);

        if(property_exists($requestBody, 'description'))
            $data->setDescription($requestBody->description);

        if(property_exists($requestBody, 'structure'))
            $data->setStructure($requestBody->structure);


        $entityManager->persist($data);
        $entityManager->flush();

        $data = [

            'id' => $data->getId(),
            'name' => $data->getName(),
            'description' => $data->getDescription(),
            'structure' => $data->getStructure(),
        ];

        return $this->json($data);
    }


    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[ARR(routerName: 'deleteAction', role: "ROLE_API_DIAGRAM_DELETE", title: 'Deletar')]
    public function deleteAction(ManagerRegistry $doctrine, int $id): Response
    {
        //$this->validateAccess("ROLE_API_DIAGRAM_DELETE");

        $entityManager = $doctrine->getManager();

        $data = $entityManager->getRepository($this->getRepository())->find($id);

        /** Verifica se o diagrama existe */
        if (!$data)
            return $this->json([
                'status' => false,
                'message' => 'Error on Deleted { Diagram } with id ' . $id,
            ], 404);

        /** Verifica se existem aplicações vinculadas ao diagrama */
        $aplicacoes = count($data->getApplication());

        if($aplicacoes)
            return $this->json([
                'status' => false,
                'message' => "Existem aplicações vinculadas a este diagrama.",
            ], 404);


        $entityManager->remove($data);
        $entityManager->flush();

        return $this->json([
            'status' => true,
            'message' => 'Diagrama removido com sucesso ',
        ]);
    }






    public function createBaseStructure()
    {





        return
            [
                "class" => [
                    $this->createClass(
                        key: '1', location: '-498 -25', associativeModel: false, systemModel: true,
                        className: 'Media (Sistema)', tableName: 'media', description: 'Classe de Media',
                        attributes: [
                            $this->createAttribute(
                                key: '1', ico: 'pk', attributeName: 'id', fieldName: '', type: 'int', primaryKey: true, foreingKey: false, autoGenerate: true,
                                nullable: false, unique: true, defaultValue: '', precision: 0.0, scale: 0.0, lengthMax: 0, lengthMin: 0
                            ),
                            $this->createAttribute(
                                key: '2', ico: '', attributeName: 'name', fieldName: '', type: 'string', primaryKey: false, foreingKey: false, autoGenerate: true,
                                nullable: false, unique: true, defaultValue: '', precision: 0.0, scale: 0.0, lengthMax: 0, lengthMin: 0
                            ),
                            $this->createAttribute(
                                key: '3', ico: '', attributeName: 'description', fieldName: '', type: 'text', primaryKey: false, foreingKey: false, autoGenerate: true,
                                nullable: false, unique: true, defaultValue: '', precision: 0.0, scale: 0.0, lengthMax: 0, lengthMin: 0
                            ),
                            $this->createAttribute(
                                key: '4', ico: '', attributeName: 'enabled', fieldName: '', type: 'int', primaryKey: false, foreingKey: false, autoGenerate: true,
                                nullable: false, unique: true, defaultValue: '', precision: 0.0, scale: 0.0, lengthMax: 0, lengthMin: 0
                            ),
                        ],
                        methods: []
                    ),
                    $this->createClass(
                        key: '2', location: '-504 202', associativeModel: false, systemModel: true,
                        className: 'Galeria (Sistema)', tableName: 'galeria', description: 'Classe de Galeria de Medias',
                        attributes: [
                            $this->createAttribute(
                                key: '1', ico: 'pk', attributeName: 'id', fieldName: '', type: 'int', primaryKey: true, foreingKey: false, autoGenerate: true,
                                nullable: false, unique: true, defaultValue: '', precision: 0.0, scale: 0.0, lengthMax: 0, lengthMin: 0
                            ),
                            $this->createAttribute(
                                key: '2', ico: '', attributeName: 'name', fieldName: '', type: 'string', primaryKey: false, foreingKey: false, autoGenerate: true,
                                nullable: false, unique: true, defaultValue: '', precision: 0.0, scale: 0.0, lengthMax: 0, lengthMin: 0
                            ),
                            $this->createAttribute(
                                key: '3', ico: '', attributeName: 'enabled', fieldName: '', type: 'int', primaryKey: false, foreingKey: false, autoGenerate: true,
                                nullable: false, unique: true, defaultValue: '', precision: 0.0, scale: 0.0, lengthMax: 0, lengthMin: 0
                            ),
                        ],
                        methods: []
                    ),
                    $this->createClass(
                        key: '3', location: '-924 85', associativeModel: true, systemModel: true,
                        className: 'media_galeria (Sistema)', tableName: 'media_galeria (Sistema)', description: 'Medias em Galeria',
                        attributes: [
                            $this->createAttribute(
                                key: '1', ico: 'fk', attributeName: 'media_id', fieldName: '', type: 'int', primaryKey: false, foreingKey: true, autoGenerate: true,
                                nullable: false, unique: true, defaultValue: '', precision: 0.0, scale: 0.0, lengthMax: 0, lengthMin: 0
                            ),
                            $this->createAttribute(
                                key: '2', ico: 'fk', attributeName: 'galeria_id', fieldName: '', type: 'int', primaryKey: false, foreingKey: true, autoGenerate: true,
                                nullable: false, unique: true, defaultValue: '', precision: 0.0, scale: 0.0, lengthMax: 0, lengthMin: 0
                            ),
                            $this->createAttribute(
                                key: '3', ico: '', attributeName: 'position', fieldName: '', type: 'int', primaryKey: false, foreingKey: false, autoGenerate: true,
                                nullable: false, unique: true, defaultValue: '', precision: 0.0, scale: 0.0, lengthMax: 0, lengthMin: 0
                            ),
                            $this->createAttribute(
                                key: '4', ico: '', attributeName: 'enabled', fieldName: '', type: 'int', primaryKey: false, foreingKey: false, autoGenerate: true,
                                nullable: false, unique: true, defaultValue: '', precision: 0.0, scale: 0.0, lengthMax: 0, lengthMin: 0
                            ),
                        ],
                        methods: []
                    ),

                ], // End Class

                "relationships" => [
                    $this->createRelationship(from: '1', to: '3', typeRelationship: 'one-to-many', attributeTo: '', attributeFromField: ''),
                    $this->createRelationship(from: '2', to: '3', typeRelationship: 'one-to-many', attributeTo: '', attributeFromField: ''),
                ]
            ];
    }


    public function createRelationship(string $from, string $to, string $typeRelationship, string $attributeTo = '', string $attributeFromField = '' )
    {
        return [
            "from" => $from,
            "to" => $to,
            "attributeTo" => $attributeTo,
            "attributeFromField" => $attributeFromField,
            "typeRelationship" => $typeRelationship,
        ];

    }

    public function createClass(string $key, string $location, bool $associativeModel, bool $systemModel, string $className, string $tableName, string $description, array $attributes, array $methods): array
    {
        return [
            "key" => $key,
            "location" => $location,
            "associativeModel" => $associativeModel,
            "systemModel" => $systemModel,
            "className" => $className,
            "tableName" => $tableName,
            "description" => $description,
            "attributes" => $attributes,
            "methods" => $methods,
        ];
    }



    public function createAttribute(
        string $key, string $ico, string $attributeName, string $fieldName, string $type, bool $primaryKey,
        bool $foreingKey, bool $autoGenerate, bool  $nullable, bool $unique, string $defaultValue, float $precision,
        float $scale, int $lengthMax, int $lengthMin  ): array
    {
        return [
            "key" => $key,
            "ico" => $ico,

            "attributeName" => $attributeName,
            "fieldName" => $fieldName,
            "type" => $type,

            "primaryKey" => $primaryKey,
            "foreingKey" => $foreingKey,
            "autoGenerate" => $autoGenerate,
            "nullable" => $nullable,
            "unique" => $unique,
            "defaultValue" => $defaultValue,
            "precision" => $precision,
            "scale" => $scale,
            "lengthMax" => $lengthMax,
            "lengthMin" => $lengthMin,
        ];
    }


}