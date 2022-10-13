<?php

namespace App\Controller;

use App\Application\Project\ContentBundle\Attributes\ARR;
use App\Application\Project\ContentBundle\Controller\DefaultAbstractController;
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
#[OA\Tag(name: 'Framework')]
#[ARR(groupName: 'Framework', description: 'Permissões Api do modulo Framework')]
#[Route('/api/framework', name: 'api_framework_')]
class FrameworkApiController extends DefaultAbstractController
{

    private function getRepository(): string
    {
        return Framework::class;
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
    #[ARR(routerName: 'listAction', role: "ROLE_API_FRAMEWORK_LIST", title: 'Listar')]
    #[Route('', name: 'list', methods: ['GET'])]
    ##[IsGranted("ROLE_API_FRAMEWORK_LIST")]
    public function listAction(ManagerRegistry $doctrine): Response
    {
        $this->validateAccess("ROLE_API_FRAMEWORK_LIST");
        $objectData = $doctrine->getRepository( $this->getRepository() )->findAll();


        $responseData = [];
        foreach ($objectData as $data){
            $serializer = new Serializer([new ObjectNormalizer()]);
            $dataSerialize = $serializer->normalize($data, null, [
                AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'name',
                    'description',
                    'active',
                    'programmingLanguage' => [
                        'id',
                    ],
                    'logo' => [
                        'id',
                    ]
                ],
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    //'logo'
                ]
            ]);
            $dataSerialize['logo'] = $this->getMediaUrl($data->getLogo());

            $responseData[] = $dataSerialize;
        }


        return $this->json($responseData);
    }


}