<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="blog_search")
     */
    public function search(Request $request) : Response
    {

        $em = $this->getDoctrine()->getManager();
        $query = $request->query->get('q');

        $article = $em->getRepository(Article::class)->searchByQuery($query);

        return $this->render('search/search.html.twig', [
            'articles' => $article
        ]);
    }
}
