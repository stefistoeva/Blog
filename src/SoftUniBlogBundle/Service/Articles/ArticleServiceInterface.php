<?php


namespace SoftUniBlogBundle\Service\Articles;


use Doctrine\Common\Collections\ArrayCollection;
use SoftUniBlogBundle\Entity\Article;

interface ArticleServiceInterface
{
    /**
     * @return ArrayCollection|Article[]
     */
    public function getAll();
    public function create(Article $article): bool;
    public function edit(Article $article): bool;
    public function delete(Article $article): bool;
    public function getOne(int $id): ?Article;
}