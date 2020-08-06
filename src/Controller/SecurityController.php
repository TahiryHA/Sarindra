<?php

namespace App\Controller;

use Exception;
use App\Utils\Utils;
use App\Entity\Parameter;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Notification\PasswordResetNotification;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
/**
 * @Route("/security")
 */
class SecurityController extends AbstractController
{
    protected $utils;

    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
    }

    /**
     * @Route("/login", name="app.login", options={"expose"=true})
     */
    public function login(EntityManagerInterface $manager,
        AuthenticationUtils $authenticationUtils, 
        UserRepository $repo, 
        UserPasswordEncoderInterface $encoder): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('admin_index');
        }else{
            if (empty($repo->findAll())) {
                $repo->createUser($manager, $encoder);
            }
            return $this->render('security/_login.html.twig', [
                'last_username' => $lastUsername,
                 'error' => $error,
            ]);
        }
    }

    /**
     * @Route("/forgotten_password", name="app.forgotten.password", options={"expose"=true})
     */
    public function forgottenPassword(
        Request $request,
        TokenGeneratorInterface $tokenGenerator,
        UserRepository $repo, EntityManagerInterface $manager,
        PasswordResetNotification $notification
    ): Response
    {

        if ($request->isMethod('POST')) {

            $email = $_POST["email"];
            $user = $repo->findOneByEmail($email);

            if ($user === null) {
                $this->addFlash('error',"Verifier votre adresse e-mail");
            }else{
                $token = $tokenGenerator->generateToken();

                try{
                    $user->setResetToken($token);
                    $manager->persist($user);
                    $manager->flush();

                    $url = $this->generateUrl('app.reset.password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
                    $parameter = $this->utils->_repository(Parameter::class)->findOneBy(["code" => "PARAMETER.EMAIL"]);
                    $email = $parameter->getValue();
                    $notification->notify($user,$url,$email);

                    $this->addFlash('success', 'Verifier votre boîte mail, un email a été envoyé');
                    return $this->redirectToRoute('app.login');
                } catch (\Exception $e) {
                    $this->addFlash('warning', $e->getMessage());
                    // dump('Warning');
                }
            }    

        }
        return $this->render('security/_forgotten_password.html.twig');
    }

    /**
     * @Route("/reset_password/{token}", name="app.reset.password")
     */
    public function resetPassword(Request $request, string $token, 
    UserPasswordEncoderInterface $passwordEncoder, UserRepository $repo, EntityManagerInterface $manager)
    {
        $user = $repo->findOneByResetToken($token);

        if ($user === null) {
            $this->addFlash('error', 'Veuillez verifier le lien ou votre boîte mail');
            return $this->redirectToRoute('app.login');
        }

        if ($request->isMethod('POST')) {
            
            $password1 = $request->request->get('password1');
            $password2 = $request->request->get('password2');

            if ($password1 === $password2) {
                try {
                    $user->setResetToken(null);
                    $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password1')));
                    $manager->persist($user);
                    $manager->flush();
                } catch (\Throwable $e) {
                    $this->addFlash('warning', $e->getMessage());
                    // dump('Warning');
                    return $this->redirectToRoute('app.login');
                }
                
                $this->addFlash('success', 'Mot de passe mis à jour');
                // dump($user);
                return $this->redirectToRoute('app.login');
                
            }else{
                $this->addFlash('error', 'Les mots de passes ne se correspondent pas');
            }
        }

        return $this->render('security/_reset_password.html.twig', ['token' => $token]);
    }

    /**
     * @Route("/logout", name="app.logout")
     */
    public function logout()
    {
        throw new Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
