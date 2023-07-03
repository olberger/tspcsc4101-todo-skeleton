<?php

namespace App\Controller;

use App\Entity\Paste;
use App\Form\PasteType;
use App\Repository\PasteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/paste')]
class PasteController extends AbstractController
{
    #[Route('/', name: 'paste_index', methods: ['GET'])]
    public function index(PasteRepository $pasteRepository): Response
    {
        return $this->render('paste/index.html.twig', [
            'pastes' => $pasteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'paste_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PasteRepository $pasteRepository): Response
    {
        $paste = new Paste();
        $form = $this->createForm(PasteType::class, $paste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagefile = $paste->getImageFile();
            if($imagefile) {
                $mimetype = $imagefile->getMimeType();
                $paste->setContentType($mimetype);
            }
            $pasteRepository->save($paste, true);
            
            // Make sure message will be displayed after redirect
            $this->addFlash('message', 'paste bien ajouté');

            return $this->redirectToRoute('paste_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paste/new.html.twig', [
            'paste' => $paste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'paste_show', methods: ['GET'])]
    public function show(Paste $paste): Response
    {
        return $this->render('paste/show.html.twig', [
            'paste' => $paste,
        ]);
    }

    #[Route('/{id}/edit', name: 'paste_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Paste $paste, PasteRepository $pasteRepository): Response
    {
        $form = $this->createForm(PasteType::class, $paste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pasteRepository->save($paste, true);
            
            // Make sure message will be displayed after redirect
            $this->addFlash('message', 'paste bien modifié');
            
            return $this->redirectToRoute('paste_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paste/edit.html.twig', [
            'paste' => $paste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'paste_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Paste $paste, PasteRepository $pasteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paste->getId(), $request->request->get('_token'))) {
            $pasteRepository->remove($paste, true);
            
            // Make sure message will be displayed after redirect
            $this->addFlash('message', 'paste supprimé');
        }

        return $this->redirectToRoute('paste_index', [], Response::HTTP_SEE_OTHER);
    }
}
