<?php

namespace App\Notification;

use App\Utils\Utils;
use Twig\Environment;
use App\Entity\Parameter;

class ContactNotification 
{
    protected $mailer;
    protected $renderer;
    protected $utils;

    public function __construct(\Swift_Mailer $mailer,Environment $renderer,Utils $utils)
    { 
        $this->mailer = $mailer;
        $this->renderer = $renderer;
        $this->parameter = $utils;
    }

    public function notify($object, $email){
        $message = (new \Swift_Message("Message"))
            ->setFrom($object->getEmail())
            ->setReplyTo($object->getEmail())
            ->setTo($email)
            ->setBody($this->renderer->render("emails/contact.html.twig", [
                "contact" => $object,
                "email" => $email
            ]),"text/html")
        ;
        $this->mailer->send($message);
    }
}