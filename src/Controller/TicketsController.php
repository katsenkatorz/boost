<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Tickets;
use App\Form\CommentType;
use App\Form\TicketsType;
use App\Repository\CommentRepository;
use App\Repository\TicketsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class TicketsController extends AbstractController
{
    #[Route('/', name: 'tickets_index', methods: ['GET'])]
    public function index(TicketsRepository $ticketsRepository): Response
    {
        return $this->render('tickets/index.html.twig', [
            'tickets' => $ticketsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'tickets_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ticket = new Tickets();
        $form = $this->createForm(TicketsType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('tickets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tickets/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'tickets_show', methods: ['GET', 'POST'])]
    public function show(
        Tickets $ticket,
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        CommentRepository $commentRepository
    ): Response
    {
        $comment = new Comment();
        $comment->setTicket($ticket);
        // Todo replace this next line by user connected
        $comment->setUser($userRepository->findOneBy([]));
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->renderForm('tickets/show.html.twig', [
            'ticket' => $ticket,
            'commentsTicket' => $commentRepository->findByAllByIdTickets($ticket->getId()),
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'tickets_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tickets $ticket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TicketsType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('tickets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tickets/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'tickets_delete', methods: ['POST'])]
    public function delete(Request $request, Tickets $ticket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tickets_index', [], Response::HTTP_SEE_OTHER);
    }
}
