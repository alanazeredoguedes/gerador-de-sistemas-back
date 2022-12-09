<?php

namespace App\Application\Project\ContentBundle\Helper\AwsHelper\Aws;

use Aws\Ses\SesClient;
use Aws\Result as AwsResult;
use Symfony\Component\HttpFoundation\Request;

class Ses
{
    protected SesClient $client;

    public function __construct(
        protected array $credentials,
    ){
        $this->client = new SesClient([
            'region' => 'sa-east-1',
            'version' => '2010-12-01',
            'credentials' => $credentials,
        ]);
    }


    public function sendEmail(){

        $result = $this->client->sendEmail([
            'Source' => 'juca@gmail.com', // REQUIRED
            'ConfigurationSetName' => 'Teste',
            'Destination' => [ // REQUIRED
                /*'BccAddresses' => ['<string>', ...],
                'CcAddresses' => ['<string>', ...],*/
                'ToAddresses' => ['alanguedes00@live.com'],
            ],
            'Message' => [ // REQUIRED
                'Body' => [ // REQUIRED
                    'Html' => [
                        'Charset' => 'utf8',
                        'Data' => 'teste', // REQUIRED
                    ],
                ],
                'Subject' => [ // REQUIRED
                    'Charset' => 'utf8',
                    'Data' => 'teste', // REQUIRED
                ],
            ],
           /* 'ReplyToAddresses' => ['<string>', ...],
            'ReturnPath' => '<string>',
            'ReturnPathArn' => '<string>',

            'SourceArn' => '<string>',
            'Tags' => [
                [
                    'Name' => '<string>', // REQUIRED
                    'Value' => '<string>', // REQUIRED
                ],
                // ...
            ],*/
        ]);

        dd($result);
    }



}