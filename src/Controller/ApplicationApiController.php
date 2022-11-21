<?php

namespace App\Controller;

use App\Application\Project\ContentBundle\Attributes\ARR;
use App\Application\Project\ContentBundle\Controller\DefaultAbstractController;
use App\Application\Project\ContentBundle\Helper\AwsHelper\AwsHelper;
use App\Entity\Application;
use App\Entity\Diagram;
use App\Entity\Framework;
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
#[OA\Tag(name: 'Application')]
#[ARR(groupName: 'Aplicações', description: 'Permissões Api do modulo Aplicações')]
#[Route('/api/application', name: 'api_application_')]
class ApplicationApiController extends DefaultAbstractController
{

    private function getRepository(): string
    {
        return Application::class;
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
                new OA\Property(property: 'diagram',  type: 'integer', nullable: false),
                new OA\Property(property: 'framework',  type: 'integer', nullable: false),
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

        $responseData = [];
        foreach ($objectData as $data){

            $serializer = new Serializer([new ObjectNormalizer()]);
            $dataSerialize = $serializer->normalize($data, null, [
                AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'name',
                    'description',
                    'diagram'=>[
                        'id',
                        'name',
                        'description'
                    ],
                    'framework'=>[
                        'id',
                        'name',
                        'logo',
                        'programmingLanguage' => [
                            'id',
                            'name',
                            'logo'
                        ]
                    ],
                ],
                AbstractNormalizer::IGNORED_ATTRIBUTES => []
            ]);
            $dataSerialize['framework']['logo'] = $this->getMediaUrl($data->getFramework()->getLogo());
            $dataSerialize['framework']['programmingLanguage']['logo'] = $this->getMediaUrl($data->getFramework()->getProgrammingLanguage()->getLogo());

            $responseData[] = $dataSerialize;
        }
        return $this->json($responseData);
    }

    #[OA\RequestBody(
        description: 'Json Payload',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', nullable: false),
                new OA\Property(property: 'description', type: 'string', nullable: false),
                new OA\Property(property: 'diagram',  type: 'integer', nullable: false),
                new OA\Property(property: 'framework',  type: 'integer', nullable: false),
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
            'description'  => [ 'type' => 'string', 'required' => true, 'nullable' => false ],
            'diagram'      => [ 'type' => 'integer', 'required' => true, 'nullable' => false ],
            'framework'    => [ 'type' => 'integer', 'required' => true, 'nullable' => false ],
        ];

        $requestBody = json_decode($request->getContent());

        if($this->validateJsonRequestBody($requestBody, $parameters))
            return $this->validateJsonRequestBody($requestBody, $parameters);

        $data = new Application();

        if(property_exists($requestBody, 'name'))
            $data->setName($requestBody->name);

        if(property_exists($requestBody, 'description'))
            $data->setDescription($requestBody->description);

        if(property_exists($requestBody, 'diagram')){
            $diagram = $entityManager->getRepository(Diagram::class)->findOneBy(['id' => $requestBody->diagram]);
            $data->setDiagram($diagram);
        }

        if(property_exists($requestBody, 'framework')){
            $framework = $entityManager->getRepository(Framework::class)->findOneBy(['id' => $requestBody->framework]);
            $data->setFramework($framework);
        }

        $data->setUser($user);

        $entityManager->persist($data);
        $entityManager->flush();

        return $this->json([
            'status' => true,
            'message' => "Sucesso ao criar aplicação!",
        ]);
    }


    /** @throws ExceptionInterface */
    #[OA\Response(
        response: 200,
        description: 'Return data',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', nullable: false),
                new OA\Property(property: 'description', type: 'string', nullable: false),
                new OA\Property(property: 'diagram',  type: 'integer', nullable: false),
                new OA\Property(property: 'framework',  type: 'integer', nullable: false),
            ],
            type: 'object'
        )
    )]
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[ARR(routerName: 'showAction', role: "ROLE_API_APPLICATION_SHOW", title: 'Visualizar')]
    public function showAction(ManagerRegistry $doctrine, int $id): Response
    {
        $this->validateAccess("ROLE_API_APPLICATION_SHOW");
        $user = $this->getUser();

        $objectData = $doctrine->getRepository($this->getRepository())->findOneBy(['id' => $id ,'user' => $user]);

        if (!$objectData)
            return $this->json([
                'status' => false,
                'message' => "Aplicação não encontrada.",
            ], 404);

        $serializer = new Serializer([new ObjectNormalizer()]);
        $response = $serializer->normalize($objectData, null, [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'name',
                'description',
                'url',
                'accessEmail',
                'accessPassword',
                'repository',
                'diagram'=>[
                    'id',
                    'name',
                    'description',
                    'structure'
                ],
                'framework'=>[
                    'id',
                    'name',
                    'logo',
                    'programmingLanguage' => [
                        'id',
                        'name',
                        'logo'
                    ]
                ],
            ],
            AbstractNormalizer::IGNORED_ATTRIBUTES => []
        ]);
        $response['framework']['logo'] = $this->getMediaUrl($objectData->getFramework()->getLogo());
        $response['framework']['programmingLanguage']['logo'] = $this->getMediaUrl($objectData->getFramework()->getProgrammingLanguage()->getLogo());

        return $this->json($response);
    }


    #[OA\RequestBody(
        description: 'Json Payload',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', nullable: false),
                new OA\Property(property: 'description', type: 'string', nullable: false),
                new OA\Property(property: 'diagram',  type: 'integer', nullable: false),
                new OA\Property(property: 'framework',  type: 'integer', nullable: false),
            ],
            type: 'object'
        )
    )]
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[ARR(routerName: 'editAction', role: "ROLE_API_APPLICATION_EDIT", title: 'Editar')]
    public function editAction(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $this->validateAccess("ROLE_API_APPLICATION_EDIT");
        $user = $this->getUser();

        $entityManager = $doctrine->getManager();

        $parameters = [
            'name'         => [ 'type' => 'string', 'required' => true, 'nullable' => false ],
            'description'  => [ 'type' => 'string', 'required' => true, 'nullable' => false ],
            'diagram'      => [ 'type' => 'integer', 'required' => true, 'nullable' => false ],
            'framework'    => [ 'type' => 'integer', 'required' => true, 'nullable' => false ],
        ];

        $requestBody = json_decode($request->getContent());

        if($this->validateJsonRequestBody($requestBody, $parameters))
            return $this->validateJsonRequestBody($requestBody, $parameters);


        $data = $doctrine->getRepository($this->getRepository())->findOneBy(['id' => $id ,'user' => $user]);

        if (!$data)
            return $this->json('Aplicação não encontrada', 404);


        if(property_exists($requestBody, 'name'))
            $data->setName($requestBody->name);

        if(property_exists($requestBody, 'description'))
            $data->setDescription($requestBody->description);

        if(property_exists($requestBody, 'diagram')){
            $diagram = $entityManager->getRepository(Diagram::class)->findOneBy(['id' => $requestBody->diagram]);
            $data->setDiagram($diagram);
        }

        if(property_exists($requestBody, 'framework')){
            $framework = $entityManager->getRepository(Framework::class)->findOneBy(['id' => $requestBody->framework]);
            $data->setFramework($framework);
        }

        $entityManager->persist($data);
        $entityManager->flush();

        return $this->json([
            'status' => true,
            'message' => "Sucesso ao atualizar aplicação!",
        ], 200);
    }



    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[ARR(routerName: 'deleteAction', role: "ROLE_API_APPLICATION_DELETE", title: 'Deletar')]
    public function deleteAction(ManagerRegistry $doctrine, int $id): Response
    {
        $this->validateAccess("ROLE_API_APPLICATION_DELETE");
        $user = $this->getUser();

        $entityManager = $doctrine->getManager();

        $data = $doctrine->getRepository($this->getRepository())->findOneBy(['id' => $id ,'user' => $user]);

        /** Verifica se o diagrama existe */
        if (!$data)
            return $this->json([
                'status' => false,
                'message' => 'Erro ao remover aplicação!',
            ], 404);

        $entityManager->remove($data);
        $entityManager->flush();

        return $this->json([
            'status' => true,
            'message' => 'Aplicação removida com sucesso ',
        ]);
    }


    #[Route('/generate/getInfo', name: 'generate_get_generate_info', methods: ['GET', 'POST'])]
    public function getApplicationInfoAction(ManagerRegistry $doctrine, Request $request): Response
    {
        $awsHelper = new AwsHelper();

        $content =  json_decode( $request->getContent() );
        if(isset($content) && isset($content->Type)){
            if($content->Type === "SubscriptionConfirmation"){
                $token = $content->Token;
                $topic = $content->TopicArn;
                $awsHelper->sns->confirmSubscribe($token, $topic);
            }
        }


        $message = $awsHelper->sqs->getMessageCodeGenetateInfo(true);
        if(!$message)
            return $this->json(['status' => false, 'message' => 'Sem dados a processar!']);


        if(!$message['status'])
            return $this->json(['status' => false, 'message' => 'Sem dados para processar!' ]);

        $data = json_decode($message['message']);
        $message = json_decode( $data->Message );




        $em = $doctrine->getManager();
        $application = $doctrine->getRepository(Application::class)->find($message->app);


        if( $message->repository )
            $application->setRepository($message->repository);

        if( $message->url )
            $application->setUrl( $message->url );


        if( $message->email )
            $application->setAccessEmail( $message->email );


        if( $message->password )
            $application->setAccessPassword( $message->password );


        if( $message->repository && $message->url )
            $application->setLastGenerate(null);



        $em->persist($application);
        $em->flush();

        return $this->json(['status' => true]);
    }

    #[Route('/generate/{id}', name: 'generate_application', methods: ['GET'])]
    #[ARR(routerName: 'generateApplication', role: "ROLE_API_APPLICATION_GENERATE", title: 'Gerar Aplicação')]
    public function generateApplicationAction(ManagerRegistry $doctrine, int $id): Response
    {
        $this->validateAccess("ROLE_API_APPLICATION_GENERATE");
        $user = $this->getUser();

        /** @var Application */
        $application = $doctrine->getRepository(Application::class)->findOneBy(['id' => $id ,'user' => 1]);

        if (!$application)
            return $this->json([
                'status' => false,
                'message' => "Aplicação não encontrada.",
            ], 404);


        /** Espera 10 minutos desde a ultima geração para gerar novo codigo */
        $dataLastGenerate = $application->getLastGenerate();
        if($dataLastGenerate){

            $dataLastGenerate->add(new \DateInterval('PT' . 10 . 'M'));
            $dataLastGenerate->format('Y-m-d H:i');

            if( $dataLastGenerate >= new \DateTime('now') )
                return $this->json([
                    'status' => true,
                    'message'=> 'Sua aplicação já está em processo de geração, aguarde alguns minutos enquanto o processo é finalizado!'
                ]);

        }

        $response = [
            'user'=> [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
            ],
            'app' => [
                'id' => $application->getId(),
                'name' => $application->getName(),
                'description' => $application->getDescription(),
                'diagram' => [
                    'id' => $application->getDiagram()->getId(),
                    'name' => $application->getDiagram()->getName(),
                    'structure' => json_decode( $application->getDiagram()->getStructure() ),
                ],
            ],

        ];

        $awsHelper = new AwsHelper();
        $awsHelper->sns->sentToNotifyGenerator(json_encode($response));


        $application->setLastGenerate(new \DateTime('now'));
        $application->setUrl(null);
        $application->setRepository(null);

        $doctrine->getManager()->persist($application);
        $doctrine->getManager()->flush();


        return $this->json([
            'status' => true,
            'message' => "Começamos a gerar sua aplicação, em alguns minutos finalizaremos os procedimentos!",
        ]);
    }










}