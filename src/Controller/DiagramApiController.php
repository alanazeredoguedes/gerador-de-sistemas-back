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
        $objectData = $doctrine->getRepository( $this->getRepository() )->findAll();

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
        //$this->validateAccess("ROLE_API_DIAGRAM_CREATE");

        $entityManager = $doctrine->getManager();

        $parameters = [
            'name'         => [ 'type' => 'string', 'required' => true, 'nullable' => false ],
            'description'  => [ 'type' => 'string', 'required' => false, 'nullable' => true ],
            'structure'    => [ 'type' => 'string', 'required' => false, 'nullable' => true ],
        ];

        $requestBody = json_decode($request->getContent());

        if($this->validateJsonRequestBody($requestBody, $parameters))
            return $this->validateJsonRequestBody($requestBody, $parameters);


        $data = new Diagram();

        if(property_exists($requestBody, 'name'))
            $data->setName($requestBody->name);

        if(property_exists($requestBody, 'description'))
            $data->setDescription($requestBody->description);

        if(property_exists($requestBody, 'structure'))
            $data->setStructure($requestBody->structure);


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
        //$this->validateAccess("ROLE_API_DIAGRAM_SHOW");

        $objectData = $doctrine->getRepository($this->getRepository())->find($id);

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

}