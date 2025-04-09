<?php

namespace App\Controller;

use App\Application\CreateInvoiceUseCase;
use App\Application\DeleteInvoiceUseCase;
use App\Application\GetAllInvoicesUseCase;
use App\Application\GetInvoiceUseCase;
use App\Application\PayInvoiceUseCase;
use App\Application\UpdateInvoiceUseCase;
use App\Entity\Invoice;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{

    private $createInvoiceUseCase;
    private $deleteInvoiceUseCase;
    private $getInvoiceUseCase;
    private $getAllInvoiceUseCase;
    private $updateInvoiceUseCase;
    private $payInvoiceUseCase;



    public function __construct(CreateInvoiceUseCase $createInvoiceUseCase,DeleteInvoiceUseCase  $deleteInvoiceUseCase,GetInvoiceUseCase $getInvoiceUseCase, GetAllInvoicesUseCase $getAllInvoiceUseCase, UpdateInvoiceUseCase $updateInvoiceUseCase,  PayInvoiceUseCase $payInvoiceUseCase)
    {
        $this->createInvoiceUseCase = $createInvoiceUseCase;
        $this->deleteInvoiceUseCase = $deleteInvoiceUseCase;
        $this->getInvoiceUseCase = $getInvoiceUseCase;
        $this->getAllInvoiceUseCase = $getAllInvoiceUseCase;
        $this->updateInvoiceUseCase = $updateInvoiceUseCase;
        $this->payInvoiceUseCase = $payInvoiceUseCase;
    }


    #[Route('/create-invoice', name: 'create_invoice', methods: ['GET', 'POST'])]
    public function createInvoice(Request $request): Response
    {

        if ($request->getMethod() === 'POST') {
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $price = (float)$request->request->get('price');

            if (!$title || !$price) {
                $this->addFlash("error", "Le titre et le prix doivent être renseignés");
                return $this->render('invoice/create.html.twig', []);
            }

            try {
                $this->createInvoiceUseCase->execute($title, $price,$description);
            } catch (\Exception $e) {
                $this->addFlash("error", $e->getMessage());
            }

        }
        return $this->render('invoice/create.html.twig', []);
    }

    #[Route('/invoice/{id}', name: 'invoice_show', methods: ['GET'])]
    public function showInvoice(int $id, Request $request): Response
    {
        if ($request->getMethod() !== 'GET') {
            throw $this->createNotFoundException('Méthode non autorisée');
        }

        $invoice = $this->getInvoiceUseCase->execute($id);

        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/invoice/delete/{id}', name: 'invoice_delete', methods: ['POST'])]
    public function deleteInvoice(int $id, Request $request): Response
    {
        if ($request->getMethod() !== 'POST') {
            throw $this->createNotFoundException('Méthode non autorisée');
        }

        $this->deleteInvoiceUseCase->execute($id);
        $this->addFlash("success", "Facture supprimée avec succès");

        // Redirection vers la liste des factures
        return $this->redirectToRoute('invoice_list');
    }

    #[Route('/invoices', name: 'invoice_list', methods: ['GET'])]
    public function listInvoices(): Response
    {
        $invoices = $this->getAllInvoiceUseCase->execute();

        return $this->render('invoice/list.html.twig', [
            'invoices' => $invoices,
        ]);
    }

    #[Route('/invoice/edit/{id}', name: 'invoice_edit', methods: ['GET', 'POST'])]
    public function editInvoice(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $invoice = $entityManager->getRepository(Invoice::class)->find($id);

        if (!$invoice) {
            $this->addFlash("error", "Facture non trouvée");
            return $this->redirectToRoute('invoice_list');
        }

        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $price = (float)$request->request->get('price');
            $status = $request->request->get('status');

            try {
                $this->updateInvoiceUseCase->execute($id, $title, $description, $price, $status);
                $this->addFlash("success", "Facture mise à jour avec succès !");
                return $this->redirectToRoute('invoice_show', ['id' => $id]);
            } catch (\Exception $e) {
                $this->addFlash("error", $e->getMessage());
            }
        }

        return $this->render('invoice/edit.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/invoice/pay/{id}', name: 'invoice_pay', methods: ['POST'])]
    public function payInvoice(int $id, Request $request): Response
    {
        if ($request->getMethod() !== 'POST') {
            throw $this->createNotFoundException('Méthode non autorisée');
        }

        try {
            $this->payInvoiceUseCase->execute($id);
            $this->addFlash("success", "Facture payée avec succès");
        } catch (\Exception $e) {
            $this->addFlash("error", $e->getMessage());
        }

        return $this->redirectToRoute('invoice_show', ['id' => $id]);
    }


}