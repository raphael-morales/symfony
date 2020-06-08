<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{

    /**
     * @Route("/add", name="add")
     */
    public function addCategory(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine = $this->getDoctrine();
            $em = $doctrine->getManager();
            $em ->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_index');
        }
        return $this->render('category/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="index")
     */
    public function index() :Response
    {
        $doctrine = $this->getDoctrine();
        $repository = $doctrine->getRepository(Category::class);
        $category = $repository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $category
        ]);
    }
}
