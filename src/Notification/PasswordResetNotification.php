<?php

namespace App\Notification;

use App\Entity\User;
use App\Utils\Utils;
use Twig\Environment;

class PasswordResetNotification
{
    protected $mailer;
    protected $renderer;
    protected $utils;

    public function __construct(\Swift_Mailer $mailer,Environment $renderer, Utils $utils)
    { 
        $this->mailer = $mailer;
        $this->renderer = $renderer;

        $this->utils = $utils;
    }

    public function notify(User $user,$url, $email){

        $message = (new \Swift_Message("Réinitialiser le mot de passe"))
            ->setFrom($email)
            // ->setTo($user->getEmail())
            ->setBody($this->renderer->render("emails/reset_password.html.twig", [
                "user" => $user,
                "url" => $url,
                "email" => $email
            ]),"text/html")
        ;

        $this->mailer->send($message);
    }
}
