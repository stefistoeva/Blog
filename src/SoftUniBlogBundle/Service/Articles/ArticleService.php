<?php


namespace SoftUniBlogBundle\Service\Articles;


use SoftUniBlogBundle\Entity\Article;

class ArticleService implements ArticleServiceInterface
{

    public function create(Article $article): bool
    {
        return true;
    }

    public function edit(Article $article): bool
    {
        return true;
    }

    public function delete(Article $article): bool
    {
        return true;
    }
}