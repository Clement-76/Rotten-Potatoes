<?php

namespace App\Controller;

use App\Entity\People;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PeopleController extends AbstractController {

    /**
     * @Route("/people/{slug}", name="show_people")
     */
    public function index(People $people) {
        return $this->render('people/people.html.twig', [
            'people' => $people
        ]);
    }
}
