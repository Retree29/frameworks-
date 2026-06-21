<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/loans')]
class LoanController extends AbstractController
{
    #[Route('', name: 'api_loans_index', methods: ['GET'])]
    public function index(LoanRepository $loanRepository): JsonResponse
    {
        $loans = $loanRepository->findAll();
        $data = [];
        foreach ($loans as $loan) {
            $data[] = [
                'id' => $loan->getId(),
                'borrower' => $loan->getBorrower(),
                'amount' => $loan->getAmount(),
                'interest_rate' => $loan->getInterestRate(),
            ];
        }
        return $this->json($data);
    }

    #[Route('', name: 'api_loans_store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        
        if (empty($content['borrower']) || empty($content['amount']) || empty($content['interest_rate'])) {
            return $this->json(['message' => 'Borrower, amount, and interest_rate are required'], 400);
        }

        $loan = new Loan();
        $loan->setBorrower($content['borrower']);
        $loan->setAmount((string) $content['amount']);
        $loan->setInterestRate((string) $content['interest_rate']);

        $entityManager->persist($loan);
        $entityManager->flush();

        return $this->json([
            'id' => $loan->getId(),
            'borrower' => $loan->getBorrower(),
            'amount' => $loan->getAmount(),
            'interest_rate' => $loan->getInterestRate(),
        ], 201);
    }

    #[Route('/{id}', name: 'api_loans_show', methods: ['GET'])]
    public function show(int $id, LoanRepository $loanRepository): JsonResponse
    {
        $loan = $loanRepository->find($id);

        if (!$loan) {
            return $this->json(['message' => 'Loan not found'], 404);
        }

        return $this->json([
            'id' => $loan->getId(),
            'borrower' => $loan->getBorrower(),
            'amount' => $loan->getAmount(),
            'interest_rate' => $loan->getInterestRate(),
        ]);
    }

    #[Route('/{id}', name: 'api_loans_update', methods: ['PATCH'])]
    public function update(int $id, Request $request, LoanRepository $loanRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $loan = $loanRepository->find($id);

        if (!$loan) {
            return $this->json(['message' => 'Loan not found'], 404);
        }

        $content = json_decode($request->getContent(), true);

        if (isset($content['borrower'])) {
            $loan->setBorrower($content['borrower']);
        }
        if (isset($content['amount'])) {
            $loan->setAmount((string) $content['amount']);
        }
        if (isset($content['interest_rate'])) {
            $loan->setInterestRate((string) $content['interest_rate']);
        }

        $entityManager->flush();

        return $this->json([
            'id' => $loan->getId(),
            'borrower' => $loan->getBorrower(),
            'amount' => $loan->getAmount(),
            'interest_rate' => $loan->getInterestRate(),
        ]);
    }

    #[Route('/{id}', name: 'api_loans_delete', methods: ['DELETE'])]
    public function delete(int $id, LoanRepository $loanRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $loan = $loanRepository->find($id);

        if (!$loan) {
            return $this->json(['message' => 'Loan not found'], 404);
        }

        $entityManager->remove($loan);
        $entityManager->flush();

        return $this->json(['message' => 'Loan deleted']);
    }
}
