<?php

namespace App\Application\Project\ContentBundle\Helper\AwsHelper;

use App\Application\Project\ContentBundle\Helper\AwsHelper\Aws\CodeCommit;
use App\Application\Project\ContentBundle\Helper\AwsHelper\Aws\Ec2;
use App\Application\Project\ContentBundle\Helper\AwsHelper\Aws\Ses;
use App\Application\Project\ContentBundle\Helper\AwsHelper\Aws\Sns;
use App\Application\Project\ContentBundle\Helper\AwsHelper\Aws\Sqs;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class AwsHelper
{
    public Ec2 $ec2;
    public CodeCommit $codeCommit;
    public Sns $sns;
    public Sqs $sqs;
    public Ses $ses;

    public function __construct(
        protected ContainerBagInterface $containerInterface,
    )
    {

        /*$this->ec2 = new Ec2(
            credentials: $this->getCredentials(),
            projectDir: $this->projectDir
        );*/

        $this->codeCommit = new CodeCommit(
            credentials: $this->getCredentials(),
        );

        $this->sns = new Sns(
            credentials: $this->getCredentials(),
        );

        $this->sqs = new Sqs(
            credentials: $this->getCredentials(),
        );

        $this->ses = new Ses(
            credentials: $this->getCredentials(),
        );

    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getCredentials(): array
    {
        return [
            'key' => $this->containerInterface->get('aws.key') ?
                $this->containerInterface->get('aws.key') : '',
            'secret' => $this->containerInterface->get('aws.secret') ?
                $this->containerInterface->get('aws.secret') : '',
        ];
    }

}