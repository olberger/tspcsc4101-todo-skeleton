<?php
/**
 * Gestion des CRUD des tâches
 *
 * @copyright  2017-2023 Telecom SudParis
 * @license    "MIT/X" License - cf. LICENSE file at project root
 */

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Todo;
use App\Form\TodoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
        
        //dump($todos);
        
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
        
        // $todos = $em->getRepository(Todo::class)->findByCompleted(false);
        $todos = $em->getRepository(Todo::class)->findAll(false);
        

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
    #[IsGranted('ROLE_USER')]
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
    
    #[Route('/project/{id}/addtodo', name: 'todo_add', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function add(Request $request, Project $project): Response
    {
        $todo = new Todo();
        // already set a project, so as to not need add that field in the form (in TodoType)
        $todo->setProject($project);
        
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $todo->setCreated(new \DateTime());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();
            
            // Make sure message will be displayed after redirect
            $this->get('session')->getFlashBag()->add('message', 'tâche bien ajoutée au projet');
            
            return $this->redirectToRoute('project_show', array('id' => $project->getId() ));
        }
        
        return $this->render('todo/add.html.twig', [
            'project' => $project,
            'todo' => $todo,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/{id}/edit', name: 'todo_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
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
    #[IsGranted('ROLE_USER')]
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
