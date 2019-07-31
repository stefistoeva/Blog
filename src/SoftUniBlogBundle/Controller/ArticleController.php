<?php

namespace SoftUniBlogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SoftUniBlogBundle\Entity\Article;
use SoftUniBlogBundle\Entity\User;
use SoftUniBlogBundle\Form\ArticleType;
use SoftUniBlogBundle\Service\Articles\ArticleServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends Controller
{
    /**
     * @var ArticleServiceInterface
     */
    private $articleService;

    /**
     * ArticleController constructor.
     * @param $articleService
     */
    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * @Route("/create", name="article_create", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function create()
    {
        return $this->render('articles/create.html.twig',
            ['form' => $this
                ->createForm(ArticleType::class)
                ->createView()]);
    }

    /**
     * @Route("/create", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function createProcess(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        $this->uploadFile($form, $article);
        $this->articleService->create($article);

        $this->addFlash("info", "Create article successfully");
        return $this->redirectToRoute("blog_index");
    }

    /**
     * @Route("/edit/{id}", name="article_edit", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return Response
     */
    public function edit($id)
    {
        $article = $this->articleService->getOne($id);

        if (null === $article) {
            return $this->redirectToRoute("blog_index");
        }

        if (!$this->isAuthorOrAdmin($article)) {
            return $this->redirectToRoute("blog_index");
        }

        return $this->render('articles/edit.html.twig',
            [
                'form' => $this->createForm(ArticleType::class)->createView(),
                'article' => $article
            ]);
    }

    /**
     * @Route("/edit/{id}", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editProcess(Request $request, $id)
    {
        $article = $this->articleService->getOne($id);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        $this->uploadFile($form, $article);
        $this->articleService->edit($article);

        return $this->redirectToRoute("blog_index");
    }

    /**
     * @Route("/delete/{id}", name="article_delete", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @return Response
     */
    public function delete(int $id)
    {
        $article = $this->articleService->getOne($id);

        if (null === $article) {
            return $this->redirectToRoute("blog_index");
        }

        if (!$this->isAuthorOrAdmin($article)) {
            return $this->redirectToRoute("blog_index");
        }

        return $this->render('articles/delete.html.twig',
            [
                'form' => $this->createForm(ArticleType::class)->createView(),
                'article' => $article
            ]);
    }

    /**
     * @Route("/delete/{id}", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function deleteProcess(Request $request, int $id)
    {
        $article = $this->articleService->getOne($id);
        $form = $this->createForm(ArticleType::class, $article);

        $form->remove('imageURL');

        $form->handleRequest($request);
        $this->articleService->delete($article);
        return $this->redirectToRoute("blog_index");
    }

    /**
     * @Route("/article/{id}", name="article_view")
     *
     * @param $id
     * @return Response
     */
    public function view($id)
    {
        $article = $this->articleService->getOne($id);

        if (null === $article) {
            return $this->redirectToRoute("blog_index");
        }

        $article->setViewCount($article->getViewCount() + 1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return $this->render('articles/view.html.twig',
            [
                'article' => $article
            ]);
    }

    /**
     * @param Article $article
     * @return bool
     */
    private function isAuthorOrAdmin(Article $article)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!$currentUser->isAuthor($article) && !$currentUser->isAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * @Route("/articles/my_articles", name="my_articles")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function getAllArticlesByUser()
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->findBy(['author' => $this->getUser()],
                [
                    'dateAdded' => 'DESC'
                ]);

        return $this->render("articles/myArticles.html.twig",
            ['articles' => $articles]);
    }

    /**
     * @param FormInterface $form
     * @param Article $article
     */
    private function uploadFile(FormInterface $form, Article $article)
    {
        /** @var UploadedFile $file */
        $file = $form['image']->getData();
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        if ($file) {
            $file->move(
                $this->getParameter('articles_directory'),
                $fileName
            );

            $article->setImage($fileName);
        }
    }
}
