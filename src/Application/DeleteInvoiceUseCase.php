<?php

namespace App\Application;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use http\Message;

class DeleteInvoiceUseCase
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(int $id): void
    {
        $invoice = $this->entityManager->getRepository(Invoice::class)->find($id);

        if (!$invoice) {
            throw new \Exception("Facture non trouvée");
        }

        try{
            $this->entityManager->remove($invoice);
            $this->entityManager->flush();
        }catch (\Exception $e){
            throw new \Exception("Cannot delete invoice. Please try again later");
        }

    }
}