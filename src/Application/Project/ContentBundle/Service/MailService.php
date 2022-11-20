<?php

namespace App\Application\Project\ContentBundle\Service;



use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class MailService
{

    protected Mailer $mailer;

    public function __construct()
    {

    }





    protected function configureTransport(){
        $transport = Transport::fromDsn('smtp://localhost');
        $this->mailer = new Mailer($transport);
    }


    public function send()
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')

            ->html('<p>See Twig integration for better HTML integration!</p>')

            ->attachFromPath('/path/to/documents/terms-of-use.pdf')
            ->attachFromPath('/path/to/documents/privacy.pdf', 'Privacy Policy')

            ;
    }





}