<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/profile", name="app_profile")
     */
    public function profile() : Response
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository(Article::class)->findBy(
            ['author' => $user->getId()]
        );
        return  $this->render('security/profile.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/admin", name="app_admin")
     */
    public function admin() : Response
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository(User::class)->findAll();

        return  $this->render('security/admin.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/admin/{user}", name="admin_see_profile")
     */
    public function admin_see_profile(User $user) : Response
    {
        /*$em = $this->getDoctrine()->getManager();

        $users = $em->getRepository(User::class)->findAll();*/

        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository(Article::class)->findBy(
            ['author' => $user->getId()]
        );

        return  $this->render('security/profile.html.twig', ['articles' => $articles]);
    }

}
