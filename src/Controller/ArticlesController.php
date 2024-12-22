<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticleType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ArticlesController extends AbstractController
{
    #[Route('/articles', name: 'app_articles')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Articles::class);
        $articles = $repository->findAll();

        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/articles/create', name: 'app_articles_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFilename = $fileUploader->upload($imageFile);
                $article->setImageFilename($imageFilename);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé avec succès !');

            return $this->redirectToRoute('app_article_show');
        }

        return $this->render('articles/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/articles/edit/{id}', name: 'app_article_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $repository = $entityManager->getRepository(Articles::class);
        $article = $repository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFilename = $fileUploader->upload($imageFile);
                $article->setImageFilename($imageFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Article modifié avec succès !');

            return $this->redirectToRoute('app_article_show');
        }

        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/articles/delete/{id}', name: 'app_article_delete', methods: ['POST', 'GET'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Articles::class);
        $article = $repository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        $entityManager->remove($article);
        $entityManager->flush();

        $this->addFlash('success', 'Article supprimé avec succès !');

        return $this->redirectToRoute('app_articles');
    }

    #[Route('/articles/show', name: 'app_article_show')]
    public function show(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Articles::class);
        $articles = $repository->findAll();

        return $this->render('articles/show.html.twig', [
            'articles' => $articles,
        ]);
    }
}
