<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/post')]
class BlogController extends AbstractController
{
    #[Route('/', name: 'admin_post_index', methods: ['GET'])]
    public function index(PostRepository $posts): Response
    {
        $authorPosts =  $posts->findAll();
        return $this->render('admin/blog/index.html.twig', [
            'posts' => $authorPosts
        ]);
    }

    #[Route('/new', name: 'admin_new_post', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'article créer avec succès');
            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('admin/blog/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id<\d+>}', name: 'admin_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('admin/blog/show.html.twig', [
            'post' => $post
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'admin_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
       $form = $this->createForm(PostType::class, $post);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           $entityManager->flush();
           $this->addFlash('success', 'article modifié avec succès');

           return $this->redirectToRoute('admin_post_edit', ['id' => $post->getId()]);
       }

       return $this->render('admin/blog/edit.html.twig', [
           'post' => $post,
           'form' => $form->createView(),
       ]);
    }

    #[Route('/{id}/delete', name: 'admin_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_post_index');
        }

        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'article supprimé avec succès');
        return $this->redirectToRoute('admin_post_index');
    }
}
