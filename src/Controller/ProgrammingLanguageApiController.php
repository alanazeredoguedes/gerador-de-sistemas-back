<?php

namespace App\Controller;

use App\Application\Project\ContentBundle\Attributes\ARR;
use App\Application\Project\ContentBundle\Controller\DefaultAbstractController;
use App\Entity\ProgrammingLanguage;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
#[OA\Tag(name: 'Programming Language')]
#[ARR(groupName: 'Linguagem de Programação', description: 'Permissões Api do modulo de Linguagem de Programação')]
#[Route('/api/programmingLanguage', name: 'api_programming_language_')]
class ProgrammingLanguageApiController extends DefaultAbstractController
{

    private function getRepository(): string
    {
        return ProgrammingLanguage::class;
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
                new OA\Property(property: 'active', type: 'boolean'),
                new OA\Property(property: 'logo', type: 'object'),
            ],
            type: 'object'
        )
    )]
    #[ARR(routerName: 'listAction', role: "ROLE_API_PROGRAMMING_LANGUAGE_LIST", title: 'Listar')]
    #[Route('', name: 'list', methods: ['GET'])]
    public function listAction(ManagerRegistry $doctrine): Response
    {
        //$this->validateAccess("ROLE_API_PROGRAMMING_LANGUAGE_LIST");
        $objectData = $doctrine->getRepository( $this->getRepository() )->findAll();

        $serializer = new Serializer([new ObjectNormalizer()]);
        $response = $serializer->normalize($objectData, null, [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'name',
                'description',
                'active',
                'logo' => [
                    'id',
                    'name',
                    'description',
                    'providerReference',
                    'contentType',
                ]
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
                new OA\Property(property: 'active',  type: 'boolean', nullable: false),
            ],
            type: 'object'
        )
    )]
    #[Route('', name: 'create', methods: ['POST'])]
    #[ARR(routerName: 'createAction', role: "ROLE_API_PROGRAMMING_LANGUAGE_CREATE", title: 'Criar')]
    public function createAction(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        //$this->validateAccess("ROLE_API_PROGRAMMING_LANGUAGE_CREATE");

        $entityManager = $doctrine->getManager();

        $parameters = [
            'name'         => [ 'type' => 'string', 'required' => true, 'nullable' => false ],
            'description'  => [ 'type' => 'string', 'required' => false, 'nullable' => true ],
            'active'       => [ 'type' => 'boolean', 'required' => false, 'nullable' => false ],
        ];

        $requestBody = json_decode($request->getContent());

        if($this->validateJsonRequestBody($requestBody, $parameters))
            return $this->validateJsonRequestBody($requestBody, $parameters);


        $data = new ProgrammingLanguage();

        if(property_exists($requestBody, 'name'))
            $data->setName($requestBody->name);

        if(property_exists($requestBody, 'description'))
            $data->setDescription($requestBody->description);

        if(property_exists($requestBody, 'active'))
            $data->setActive($requestBody->active);


        $entityManager->persist($data);
        $entityManager->flush();

        return $this->json([
            'status' => true,
            'message' => 'Created new { Programming Language } successfully with id ' . $data->getId(),
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
    #[ARR(routerName: 'showAction', role: "ROLE_API_PROGRAMMING_LANGUAGE_SHOW", title: 'Visualizar')]
    public function showAction(ManagerRegistry $doctrine, int $id): Response
    {
        //$this->validateAccess("ROLE_API_PROGRAMMING_LANGUAGE_SHOW");

        $objectData = $doctrine->getRepository($this->getRepository())->find($id);

        if (!$objectData)
            return $this->json([
                'status' => false,
                'message' => 'Not found { Programming Language } with id ' . $id,
            ], 404);

        $serializer = new Serializer([new ObjectNormalizer()]);
        $response = $serializer->normalize($objectData, null, [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'name',
                'description',
                'active',
                'logo' => [
                    'id',
                    'name',
                    'description',
                    'providerReference',
                    'contentType',
                ]
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
                new OA\Property(property: 'name', type: 'string', nullable: false,),
                new OA\Property(property: 'description', type: 'string', nullable: false),
                new OA\Property(property: 'active',  type: 'boolean', nullable: false),
            ],
            type: 'object'
        )
    )]
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[ARR(routerName: 'editAction', role: "ROLE_API_PROGRAMMING_LANGUAGE_EDIT", title: 'Editar')]
    public function editAction(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json('No user found for id' . $id, 404);
        }

        $decoded = json_decode($request->getContent());
        $username = $decoded->username;
        $email = $decoded->email;

        $user->setUsername($username);
        $user->setEmail($email);

        $entityManager->persist($user);
        $entityManager->flush();

        $data = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        return $this->json($data);
    }


    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[ARR(routerName: 'deleteAction', role: "ROLE_API_PROGRAMMING_LANGUAGE_DELETE", title: 'Deletar')]
    public function deleteAction(ManagerRegistry $doctrine, int $id): Response
    {
        //$this->validateAccess("ROLE_API_PROGRAMMING_LANGUAGE_DELETE");

        $entityManager = $doctrine->getManager();
        $data = $entityManager->getRepository($this->getRepository())->find($id);

        if (!$data)
            return $this->json([
                'status' => false,
                'message' => 'Error on Deleted { Programming Language } with id ' . $id,
            ], 404);

        $entityManager->remove($data);
        $entityManager->flush();

        return $this->json([
            'status' => true,
            'message' => 'Deleted { Programming Language } successfully with id ' . $id,
        ]);
    }

}