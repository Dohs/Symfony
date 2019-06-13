<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ForgottenPasswordType;
use App\Service\TokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/deconnexion", name="logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/mot-de-passe-oublier", name="app_forgotten_password")
     */
    public function forgottenPassword(
        Request $request,
        \Swift_Mailer $mailer,
        TokenService $tokenService
    ): Response {
        $form = $this->createForm(ForgottenPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form->getData()['username'];
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByUsername($username);
            if ($user === null) {
                $this->addFlash('wrong-notice', 'Pseudo invalide');
                return $this->redirectToRoute('app_forgotten_password');
            }

            $token = $tokenService->generate($username);

            $url = $this->generateUrl(
                'app_reset_password',
                ['token' => $token,
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $message = (new \Swift_Message('Récupération de votre mot de passe'))
                ->setFrom($this->getParameter('mailer_from'))
                ->setTo($user->getEmail())
                ->setBody(
                    "Cliquer sur le lien pour réinitialiser votre mot de passe " . $url,
                    'text/html'
                );
            $mailer->send($message);
            $this->addFlash('notice', 'Votre demande a bien été prise en compte.
             Vous allez recevoir un mail permettant de réinitialiser votre mot de passe à l\'adresse indiquée.');
            return $this->redirectToRoute('app_forgotten_password');
        }
        return $this->render('security/forgotten_password.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/reset_password/{token}", name="app_reset_password")
     */
    public function resetPassword(
        Request $request,
        string $token,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenService $tokenService
    ) {
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form->getData()['username'];
            if (!$tokenService->isValid($token, $username)) {
                $this->addFlash('danger', 'Lien invalide');
                return $this->redirectToRoute('app_forgotten_password');
            }
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByUsername($username);
            $user->setPassword($passwordEncoder->encodePassword($user, $form->getData()['password']));
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('updated-password', 'Mot de passe mis à jour');
            return $this->redirectToRoute('app_login');
        } else {
            return $this->render('security/reset_password.html.twig', ['form' => $form->createView()]);
        }
    }
}
