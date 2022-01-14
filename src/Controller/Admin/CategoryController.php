<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Entity\Post;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Sodium\add;

#[Route('/admin/category')]
class CategoryController extends AbstractController
{
   #[Route('/{_locale<%app.locales%>}/', name: 'admin_category_index', methods: ['GET'])]
   public function index(CategoriesRepository $categories): Response
   {
       $authorCategories = $categories->findAll();
       return $this->render('admin/category/index.html.twig', [
           'categories' => $authorCategories
       ]);
   }

    #[Route('/{_locale<%app.locales%>}/new', name: 'admin_new_category', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categories = new Categories();

        $form = $this->createForm(CategoriesType::class, $categories)
                ->add('saveCategory', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categories);
            $entityManager->flush();

            $this->addFlash('success', 'category.created_successfully');
            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('admin/category/new.html.twig', [
            'categories' => $categories,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{_locale<%app.locales%>}/{id<\d+>}', name: 'admin_category_show', methods: ['GET'])]
    public function show(Categories $categories): Response
    {
        return $this->render('admin/category/show.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/{_locale<%app.locales%>}/{id<\d+>}/edit', name: 'admin_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categories $categories, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoriesType::class, $categories);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'category.updated_successfully');

            return $this->redirectToRoute('admin_category_edit', ['id' => $categories->getId()]);
        }

        return $this->render('admin/category/edit.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{_locale<%app.locales%>}/{id}/delete', name: 'admin_category_delete', methods: ['POST'])]
    public function delete(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_category_index');
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'category.deleted_successfully');
        return $this->redirectToRoute('admin_category_index');
    }
}
