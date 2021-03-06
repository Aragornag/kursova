<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/', name: 'article')]
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository(Article::class)->findAll();
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles
        ]);
    }

    #[Route('/article/single/{article}', name: 'single_article')]
    public function single(Article $article) : Response
    {
        return $this->render('article/single.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/create', name: 'create_article')]
    public function create(Request $request) : Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);


       // $category = new Category();
       // $category->setName('cat-1');
        $user = $this->getUser();

        $article->setAuthor($user);
      //  $article->setCategory($category);

        if ($form->isSubmitted() && $form->isValid()){
            $article = $form->getData();
            $article->setCreatedAt(new \DateTime('now'));

           /* $article->setAuthor($user->getId());*/

            $em = $this->getDoctrine()->getManager();

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('article');
        }

        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/article/update/{article}', name: 'update_article')]
    public function update(Request $request, Article $article) : Response
    {
        $form = $this->createForm(ArticleType::class, $article, [
            'action' => $this->generateUrl('update_article', [
                'article' => $article->getId()
            ]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $this->getUser();
            $userrole = $user->getRoles();

            if((($article->getAuthor()->getId() == $user->getId()) or ($userrole[0] == "ROLE_ADMIN")))
            {
                $article = $form ->getData();
                $article->setUpdatedAt(new \DateTime('now'));

                $em = $this->getDoctrine()->getManager();
                $em->flush();
            }


            return $this->redirectToRoute('article');
        }

        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/article/delete/{article}', name: 'article_delete')]
    public function delete(Article $article) : Response
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $userrole = $user->getRoles();

        if((($article->getAuthor()->getId() == $user->getId()) or ($userrole[0] == "ROLE_ADMIN")))
        {
            $em->remove($article);
            $em->flush();
        }

        return $this->redirectToRoute('article');
    }

    /**
     * @Route("/category/{category}", name="category")
     * @param Integer $category
     * @return Response
     */
    public function category(Int $category) : Response
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository(Article::class)->findBy(
            ['category' => $category]
        );

        return $this->render('category/index.html.twig', [
            'articles' => $articles
        ]);
    }

}
