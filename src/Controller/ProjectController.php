<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Todo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/project')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'project_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $project = new Project();
        
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'project_show', methods: ['GET'])]
     public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/edit', name: 'project_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(ManagerRegistry $doctrine, Request $request, Project $project): Response
    {
        $originalTodos = new ArrayCollection();
        
        // Create an ArrayCollection of the current Todo objects in the database
        foreach ($project->getTodos() as $todo) {
            $originalTodos->add($todo);
        }
        
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $doctrine->getManager();
            foreach ($originalTodos as $todo) {
                // if the original todo is no longer there
                if (! $project->getTodos()->contains($todo)) {
                    // delete the Todo entirely
                    $entityManager->remove($todo);
                }
            }
            
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'project_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(ManagerRegistry $doctrine, Request $request, Project $project): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index');
    }
}
