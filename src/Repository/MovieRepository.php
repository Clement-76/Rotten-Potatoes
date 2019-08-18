<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    /**
     * Search a movie with the given term in its title, synopsis and its categories
     * @param string $term
     * @param int|null $maxResults
     * @return mixed an empty array or a movies collection
     */
    public function findMoviesWithTerm(string $term, int $maxResults = null) {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.categories', 'c')
            ->orWhere('m.title LIKE :term')
            ->orWhere('m.synopsis LIKE :term')
            ->orWhere('c.title LIKE :term')
            ->setParameter('term', "%$term%")
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult();
    }

    public function getMoviesBy($sort, $order, $nb = 3) {

//        $sql = 'SELECT movie.*
//                FROM movie
//                INNER JOIN rating
//                ON movie.id = rating.movie_id
//                GROUP BY rating.movie_id
//                ORDER BY AVG(rating.notation)';

        return $this->createQueryBuilder('m')
            ->innerJoin('m.ratings', 'r')
            ->groupBy('m.id')
            ->orderBy($sort, $order)
            ->setMaxResults($nb)
            ->getQuery()
            ->getResult();
    }

    public function getBestMovies() {
        return $this->getMoviesBy('AVG(r.notation)', 'DESC');
    }

    public function getWorstMovies() {
        return $this->getMoviesBy('AVG(r.notation)', 'ASC');
    }

    public function getLastMovies() {
        return $this->getMoviesBy('m.releasedAt', 'DESC');
    }

    // /**
    //  * @return Movie[] Returns an array of Movie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Movie
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
