<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Rating;
use App\Form\MovieType;
use App\Form\RatingType;
use App\Repository\CategoryRepository;
use App\Repository\MovieRepository;
use App\Repository\RatingRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController {

    /**
     * @Route("/", name="home")
     */
    public function index(CategoryRepository $categoryRepo, MovieRepository $movieRepo) {
        return $this->render('movie/index.html.twig', [
            'categories' => $categoryRepo->findAll(),
            'lastMovies' => $movieRepo->getLastMovies(),
            'bestMovies' => $movieRepo->getBestMovies(),
            'worstMovies' => $movieRepo->getWorstMovies(),
        ]);
    }

    /**
     * @Route("/admin/movie", name="list_movies")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showMovies(MovieRepository $repo) {
        return $this->render('movie/movies.html.twig', [
            'movies' => $repo->findAll()
        ]);
    }

    /**
     * @Route("/admin/movie/new", name="create_movie")
     * @Route("/admin/movie/{id}/edit", name="edit_movie")
     * @IsGranted("ROLE_ADMIN")
     */
    public function createEdit(Movie $movie = null, Request $request, ObjectManager $manager) {
        $editMode = true;

        if (!$movie) {
            $movie = new Movie();
            $editMode = false;
        }

        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($movie);
            $manager->flush();

            return $this->redirectToRoute('list_movies');
        }

        return $this->render('movie/form-movie.html.twig', [
            'editMode' => $editMode,
            'movieForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/movie/{id}/delete", name="delete_movie")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(ObjectManager $manager, Movie $movie) {
        $manager->remove($movie);
        $manager->flush();
        return $this->redirectToRoute('list_movies');
    }

    /**
     * @Route("/movie/{slug}", name="movie_show")
     */
    public function show(Movie $movie, Request $request, RatingRepository $ratingRepo) {
        $rating = new Rating();

        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        // check if the user didn't already post a comment
        $currentUserRating = $ratingRepo->findOneBy([
            'author' => $this->getUser(),
            'movie' => $movie
        ]);

        $ratingIsUnique = is_null($currentUserRating);

        if ($form->isSubmitted() && $form->isValid() && $this->isGranted('ROLE_USER') && $ratingIsUnique) {
            $rating->setMovie($movie)
                ->setAuthor($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rating);
            $entityManager->flush();

            return $this->redirectToRoute('movie_show', ['slug' => $movie->getSlug()]);
        }

        return $this->render('movie/movie.html.twig', [
            'movie' => $movie,
            'commentForm' => $form->createView(),
            'ratingIsUnique' => $ratingIsUnique
        ]);
    }
}
