<?php

namespace SoftUniBlogBundle\Controller;

use SoftUniBlogBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="blog_index")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->findBy([],
                [
                    'viewCount' => 'DESC',
                    'dateAdded' => 'DESC'
                ]);

        return $this->render('home/index.html.twig',
            ['articles' => $articles]);
    }
}
