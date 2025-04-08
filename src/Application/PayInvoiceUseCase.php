<?php

namespace App\Application;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class PayInvoiceUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(int $invoiceId): void
    {
        $invoice = $this->entityManager->getRepository(Invoice::class)->find($invoiceId);

        if (!$invoice) {
            throw new EntityNotFoundException("Facture non trouvée");
        }

        try {
            $invoice->pay();
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}