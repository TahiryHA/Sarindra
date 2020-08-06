<?php

namespace App\Notification;

use App\Entity\User;
use App\Utils\Utils;
use Twig\Environment;

class UserNotification
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

    public function notify(User $user,$password, $email){
        $roles = $user->getRoles();

        if ($roles[0] === 'ROLE_SUPER_ADMIN') {
            $role = "Super Admininstrateur";
        }else{
            $role = "Modérateur";
        }

        $message = (new \Swift_Message("Confirmation de compte"))
            ->setFrom($email)
            // ->setTo($user->getEmail())
            ->setBody($this->renderer->render("emails/confirmation.html.twig", [
                "user" => $user,
                "password"=> $password,
                "role" => $role,
                "email" => $email
            ]),"text/html")
        ;

        $this->mailer->send($message);
    }
}
