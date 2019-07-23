<?php


namespace SoftUniBlogBundle\Service\Roles;


interface RoleServiceInterface
{
    public function findOneBy(string $criteria);
}