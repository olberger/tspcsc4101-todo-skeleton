<?php
/**
 * Gestion de la page d'accueil de l'application
 *
 * @copyright  2017-2022 Telecom SudParis
 * @license    "MIT/X" License - cf. LICENSE file at project root
 */

namespace App\Controller;

use App\Entity\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
