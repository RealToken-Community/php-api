<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class Service
{
    /** @var EntityManagerInterface */
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }
}
