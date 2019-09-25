<?php
/**
 * Gestion de la page d'accueil de l'application
 *
 * @copyright  2017 Telecom SudParis
 * @license    "MIT/X" License - cf. LICENSE file at project root
 */

namespace App\Controller;

use App\Entity\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur Todo
 * @Route("/todo")
 */
class TodoController extends Controller
{    
    /**
     * @Route("/", name = "home", methods="GET")
     */
    public function indexAction()
    {
        $htmlpage = '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Welcome!</title>
    </head>
    <body>
        <h1>Welcome</h1>
            
    <p>Bienvenue dans notre todo list</p>
    </body>
</html>';
        
        return new Response(
            $htmlpage,
            Response::HTTP_OK,
            array('content-type' => 'text/html')
            );
    }
    /**
     * Lists all todo entities.
     *
     * @Route("/list", name = "todo_list", methods="GET")
     * @Route("/index", name="todo_index", methods="GET")
     */
    public function listAction()
    {
        $htmlpage = '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>todos list!</title>
    </head>
    <body>
        <h1>todos list</h1>
        <p>Here are all your todos:</p>
        <ul>';
        
        $em = $this->getDoctrine()->getManager();
        $todos = $em->getRepository(Todo::class)->findAll();
        foreach($todos as $todo) {
           $htmlpage .= '<li>
            <a href="/todo/'.$todo->getid().'">'.$todo->getTitle().'</a></li>';
         }
        $htmlpage .= '</ul>';

        $htmlpage .= '</body></html>';
        
        return new Response(
            $htmlpage,
            Response::HTTP_OK,
            array('content-type' => 'text/html')
            );
    }
    /**
     * Finds and displays a todo entity.
     *
     * @Route("/{id}", name="todo_show", requirements={ "id": "\d+"}, methods="GET")
     */
    public function showAction(Todo $todo): Response
    {
        $htmlpage = '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>todo n° '.$todo->getId().' details</title>
    </head>
    <body>
        <h2>Todo Details :</h2>
        <ul>
        <dl>';
        
        $htmlpage .= '<dt>TODO</dt><dd>' . $todo->getTitle() . '</dd>';
        $htmlpage .= '<dt>Date de création</dt> <dd> ' . $todo->getCreated()->format('Y-m-d') . '</dd>';
        $htmlpage .= '<dt>Date de modification</dt> <dd> '. $todo->getUpdated()->format('Y-m-d') . '</dd>';
        $htmlpage .= '<dt>Satus</dt><dd>' . ($todo->getCompleted() ? '	Terminé ' : ' En cours ' ) . '</dd>';
        $htmlpage .= '</dl>';
        $htmlpage .= '</ul></body></html>';
                
        return new Response(
                $htmlpage,
                Response::HTTP_OK,
                array('content-type' => 'text/html')
                );
    }
}
