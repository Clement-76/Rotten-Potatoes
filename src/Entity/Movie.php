<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Movie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez entrer un titre pour le film !")
     * @Assert\Length(min="8", minMessage="Le titre doit contenir au moins {{ limit }} caractères !")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez entrer une URL pour l'affiche du film !")
     * @Assert\Url(message="Veuillez entrer une URL valide !")
     */
    private $poster;

    /**
     * @ORM\Column(type="datetime")
     */
    private $releasedAt;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez entrer un synopsis pour le film !")
     * @Assert\Length(min="30", minMessage="Le synopsis du film doit contenir au moins {{ limit }} caractères")
     */
    private $synopsis;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="movies")
     * @Assert\Count(min="1", minMessage="Veuillez sélectionner au moins {{ limit }} catégorie !")
     */
    private $categories;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\People", inversedBy="directed")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Veuillez sélectionner un réalisateur !")
     */
    private $director;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\People", inversedBy="actedIn")
     * @Assert\NotBlank(message="Veuillez sélectionner un réalisateur !")
     * @Assert\Count(min="1", minMessage="Veuillez sélectionner au moins {{ limit }} acteur !")
     */
    private $actors;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rating", mappedBy="movie", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt"="DESC"})
     */
    private $ratings;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->actors = new ArrayCollection();
        $this->ratings = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     */
    public function setReleasedAtValue() {
        if (empty($this->releasedAt)) {
            $this->releasedAt = new \DateTime();
        }
    }

    public function getRandomActors(int $nb = 2): array {
        $randomActors = [];

        $randomKeys = array_rand($this->actors->getValues(), $nb);

        foreach ($randomKeys as $key) {
            $randomActors[] = $this->actors->get($key);
        }

        return $randomActors;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setSlugValue() {
        if (empty($this->slug)) {
            $this->slug = (new Slugify())->slugify($this->title);
        }
    }

    public function getSummary() {
        $summary = substr($this->synopsis, 0, 70);
        $summary .= strlen($summary) >= 70 ? '...' : '';

        return $summary;
    }

    public function getStarsInfo() {
        $starsNb = 5;

        $average = $this->getAverageRating(true);

        // 4.5 % 1 = 0.5 -> 0.5 / 0.5 = 1 -> 1 halfStar
        $halfStars = fmod($average, 1) / 0.5;

        // 4.5 - (1 * 0.5) = 4
        $fullStars = $average - ($halfStars * 0.5);

        // 5 - 4 - 1 = 0
        $fullEmptyStars = $starsNb - $fullStars - $halfStars;
        return [
            'starsNb' => $average,
            'fullStars' => $fullStars,
            'halfStars' => $halfStars,
            'fullEmptyStars' => $fullEmptyStars
        ];
    }

    public function getAverageRating(bool $halfValue = false) {
        if ($this->ratings->count() === 0) return 0;

        $total = 0;

        foreach ($this->ratings as $rating) {
            $total += $rating->getNotation();
        }

        $average = round($total / $this->ratings->count(), 1);
        if ($halfValue) $average = round($average * 2) / 2;

        return $average;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getReleasedAt(): ?\DateTimeInterface
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(\DateTimeInterface $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    public function getDirector(): ?People
    {
        return $this->director;
    }

    public function setDirector(?People $director): self
    {
        $this->director = $director;

        return $this;
    }

    /**
     * @return Collection|People[]
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }

    public function addActor(People $actor): self
    {
        if (!$this->actors->contains($actor)) {
            $this->actors[] = $actor;
        }

        return $this;
    }

    public function removeActor(People $actor): self
    {
        if ($this->actors->contains($actor)) {
            $this->actors->removeElement($actor);
        }

        return $this;
    }

    /**
     * @return Collection|Rating[]
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setMovie($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->contains($rating)) {
            $this->ratings->removeElement($rating);
            // set the owning side to null (unless already changed)
            if ($rating->getMovie() === $this) {
                $rating->setMovie(null);
            }
        }

        return $this;
    }
}
