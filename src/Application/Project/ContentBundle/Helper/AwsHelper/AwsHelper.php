<?php

namespace App\Application\Project\ContentBundle\Helper\AwsHelper;

use App\Application\Project\ContentBundle\Helper\AwsHelper\Aws\CodeCommit;
use App\Application\Project\ContentBundle\Helper\AwsHelper\Aws\Ec2;
use App\Application\Project\ContentBundle\Helper\AwsHelper\Aws\Sns;
use App\Application\Project\ContentBundle\Helper\AwsHelper\Aws\Sqs;

class AwsHelper
{
    public Ec2 $ec2;
    public CodeCommit $codeCommit;
    public Sns $sns;
    public Sqs $sqs;


    public function __construct(
        //protected string $projectDir,
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

    }























    protected function getCredentials(): array
    {
        return [
            'key' => 'REDACTED_AWS_KEY',
            'secret' => 'No08qKH1ntfsXRO219qtEkUy/NNB8BhbT26af9Cm',
        ];
    }



}