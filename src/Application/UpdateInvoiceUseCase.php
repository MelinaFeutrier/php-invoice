<?php

namespace App\Application;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;

class UpdateInvoiceUseCase
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(int $id, string $title, ?string $description, float $price, string $status): void
    {
        $invoice = $this->entityManager->getRepository(Invoice::class)->find($id);

        if (!$invoice) {
            throw new \Exception("Facture non trouvée");
        }

        $invoice->setTitle($title);
        $invoice->setDescription($description);
        $invoice->setPrice($price);
        $invoice->setStatus($status);

        $this->entityManager->flush();
    }
}