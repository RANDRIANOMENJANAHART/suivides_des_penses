<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Form\ExpenseType;
use App\Repository\ExpenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/expense')]
class ExpenseController extends AbstractController
{
    #[Route('/', name: 'app_expense_index', methods: ['GET'])]
    public function index(ExpenseRepository $expenseRepository): Response
    {
        return $this->render('expense/index.html.twig', [
            'expenses' => $expenseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_expense_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $expense = new Expense();
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($expense);
            $em->flush();

            return $this->redirectToRoute('app_expense_index');
        }

        return $this->render('expense/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_expense_show', methods: ['GET'])]
    public function show(Expense $expense): Response
    {
        return $this->render('expense/show.html.twig', [
            'expense' => $expense,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_expense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Expense $expense, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_expense_index');
        }

        return $this->render('expense/edit.html.twig', [
            'form' => $form->createView(),
            'expense' => $expense,
        ]);
    }

    #[Route('/{id}', name: 'app_expense_delete', methods: ['POST'])]
    public function delete(Request $request, Expense $expense, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $expense->getId(), $request->request->get('_token'))) {
            $em->remove($expense);
            $em->flush();
        }

        return $this->redirectToRoute('app_expense_index');
    }

    #[Route('/statistiques', name: 'app_expense_stats')]
    public function stats(ExpenseRepository $repo): Response
    {
        $stats = $repo->getTotalByCategory();

        return $this->render('expense/stats.html.twig', [
            'stats' => $stats
        ]);
    }
}
