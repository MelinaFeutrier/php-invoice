<?php

namespace App\Application;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;

class GetInvoiceUseCase
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(int $id): ?Invoice
    {
        $invoice = $this->entityManager->getRepository(Invoice::class)->find($id);

        if (!$invoice) {
            throw new \Exception("Facture non trouvée");
        }

        return $invoice;
    }
}