<?php

namespace App\Application;

use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;

class CreateInvoiceUseCase
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function execute($title, $price, $description) {

        try {
            $invoice = new Invoice($title, $price, $description);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        try {
            $this->entityManager->persist($invoice);
            $this->entityManager->flush();

        } catch (\Exception $exception) {
            throw new \Exception("Cannot create invoice. Please try again later");
        }
    }

}