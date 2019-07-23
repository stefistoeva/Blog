<?php


namespace SoftUniBlogBundle\Service\Roles;


use SoftUniBlogBundle\Repository\RoleRepository;

class RoleService implements RoleServiceInterface
{
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }


    public function findOneBy(string $criteria)
    {
        return $this->roleRepository->findOneBy(
            ['name' => $criteria]
        );
    }
}