<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Repository\RatingRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AbstractController {

    /**
     * @Route("/admin/rating", name="rating_list")
     * @IsGranted("ROLE_MODERATOR")
     */
    public function index(RatingRepository $repo) {
        return $this->render('rating/index.html.twig', [
            'ratings' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/admin/rating/{id}/delete", name="delete_rating")
     * @IsGranted("ROLE_MODERATOR")
     */
    public function delete(ObjectManager $manager, Rating $rating) {
        $manager->remove($rating);
        $manager->flush();
        return $this->redirectToRoute('rating_list');
    }
}
