<?php

namespace App\Application;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;

class GetAllInvoicesUseCase
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(): array
    {
        return $this->entityManager->getRepository(Invoice::class)->findAll();
    }
}