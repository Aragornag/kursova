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
    public function single(Article $article)
    {
        return $this->render('article/single.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/create', name: 'create_article')]
    public function create(Request $request)
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
    public function update(Request $request, Article $article)
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
            $article = $form ->getData();
            $article->setUpdateAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('article');
        }

        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/article/delete/{article}', name: 'article_delete')]
    public function delete(Article $article)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        if($article->getAuthor()->getId() == $user->getId())
        {
            $em->remove($article);
            $em->flush();
        }

        return $this->redirectToRoute('article');
    }

    /**
     * @Route("/search", name="blog_search")
     */
    public function search(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $query = $request->query->get('q');

        $article = $em->getRepository(Article::class)->searchByQuery($query);

        return $this->render('search/search.html.twig', [
            'articles' => $article
        ]);
    }
}
