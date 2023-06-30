<?php
/**
 * Gestion des CRUD des tâches
 *
 * @copyright  2017-2023 Telecom SudParis
 * @license    "MIT/X" License - cf. LICENSE file at project root
 */

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Controleur Todo
 */
#[Route('/todo')]
class TodoController extends AbstractController
{    
    /**
     * Lists all todo entities.
     */
    #[Route('/', name: 'todo_home', methods: ['GET'])]
    #[Route('/list', name: 'todo_list', methods: ['GET'])]
    #[Route('/index', name: 'todo_index', methods: ['GET'])]
    public function listAction(ManagerRegistry $doctrine)
    {
        $entityManager= $doctrine->getManager();
        $todos = $entityManager->getRepository(Todo::class)->findAll();
        
        return $this->render('todo/index.html.twig', array(
            'todos' => $todos,
        ));
    }
    
    /**
     * Lists all active todo entities.
     *
     * The todo entities which aren't yet completed
     */
    #[Route('/list-active', name: 'todo_active_list', methods: ['GET'])]
    public function activelistAction(ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        
        $todos = $em->getRepository(Todo::class)->findByCompleted(false);
        

        return $this->render('todo/active-index.html.twig', array(
            'todos' => $todos,
        ));
    }
    
    /**
     * Finds and displays a todo entity.
     */
    #[Route('/{id}', name: 'todo_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showAction(Todo $todo): Response
    {
        return $this->render('todo/show.html.twig', array(
            'todo' => $todo,
        ));
    }
    
    #[Route('/new', name: 'todo_new', methods: ['GET', 'POST'])]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $todo->setCreated(new \DateTime());
            $em = $doctrine->getManager();
            $em->persist($todo);
            $em->flush();
            
            // Make sure message will be displayed after redirect
            $this->addFlash('message', 'tâche bien ajoutée');
            
            return $this->redirectToRoute('todo_index');
        }
        
        return $this->render('todo/new.html.twig', [
            'todo' => $todo,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/{id}/edit', name: 'todo_edit', methods: ['GET', 'POST'])]
    public function edit(ManagerRegistry $doctrine, Request $request, Todo $todo): Response
    {
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $todo->setUpdated(new \DateTime());
            
            $doctrine->getManager()->flush();
            
            $this->addFlash('message', 'tâche mise à jour');
            
            return $this->redirectToRoute('todo_show', ['id' => $todo->getId()]);
        }
        
        return $this->render('todo/edit.html.twig', [
            'todo' => $todo,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/{id}', name: 'todo_delete', methods: ['POST'])]
    public function delete(ManagerRegistry $doctrine, Request $request, Todo $todo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$todo->getId(), $request->request->get('_token'))) {
            $em = $doctrine->getManager();
            $em->remove($todo);
            $em->flush();
            
            // Make sure message will be displayed after redirect
            $this->addFlash('message', 'tâche supprimée');
        }
        
        return $this->redirectToRoute('todo_index');
    }
    
}
