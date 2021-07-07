<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\TicketStatus;
use App\Entity\TicketType;
use App\Entity\User;
use App\Form\TicketTypeDemand;
use App\Form\TicketTypeIncident;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @Route("/ticket/crud")
 */
class TicketCrudController extends AbstractController
{
    //L'utilisation du constructeur avec Security me permet d'accéder à l'User depuis n'importe ou.
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/", name="ticket_crud_index", methods={"GET"})
     */
    public function index(TicketRepository $ticketRepository): Response
    {
        return $this->render('ticket_crud/index.html.twig', [
            'tickets' => $ticketRepository->findAll(),
        ]);
    }

    /**
     * @Route("/myTickets", name="ticket_crud_my_tickets", methods={"GET"})
     */
    public function myTickets(TicketRepository $ticketRepository): Response
    {
        $user = $this->security->getUser();
        return $this->render('ticket_crud/index.html.twig', [
            
            'tickets' => $ticketRepository->findBy(
                ['author' => $user ]
            ),
        ]);
    }

    /**
     * @Route("/newDemand", name="ticket_crud_new_demand", methods={"GET","POST"})
     */
    public function newDemand(Request $request): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketTypeDemand::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $type = new TicketType();
            $type->setName('demand');
            $status = new TicketStatus();
            $status->setName('open');
            $user = $this->security->getUser();
            $ticket->setAuthor($user);
            $ticket->setCreationDate(new \DateTime());
            $ticket->setType($type);
            $ticket->setStatus($status);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($type);
            $entityManager->persist($status);
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_crud_my_tickets', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ticket_crud/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/newIncident", name="ticket_crud_new_incident", methods={"GET","POST"})
     */
    public function newIncident(Request $request): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketTypeIncident::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $type = new TicketType();
            $type->setName('incident');
            $status = new TicketStatus();
            $status->setName('open');
            $user = $this->security->getUser();
            $ticket->setAuthor($user);
            $ticket->setCreationDate(new \DateTime());
            $ticket->setType($type);
            $ticket->setStatus($status);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($type);
            $entityManager->persist($status);
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_crud_my_tickets', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ticket_crud/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_crud_show", methods={"GET"})
     */
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket_crud/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ticket_crud_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ticket $ticket): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ticket_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ticket_crud/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_crud_delete", methods={"POST"})
     */
    public function delete(Request $request, Ticket $ticket): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ticket_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
