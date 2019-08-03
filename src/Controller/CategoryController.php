<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController {

    /**
     * @Route("/category/{slug}", name="category")
     */
    public function index(Category $category) {
        return $this->render('category/category.html.twig', [
            'category' => $category,
        ]);
    }
}
