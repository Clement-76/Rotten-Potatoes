<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RatingRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("author")
 */
class Rating
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Veuillez entrer une note")
     * @Assert\Type(type="integer", message="Votre note doit être un nombre entier !")
     * @Assert\Range(
     *     min="0",
     *     maxMessage="5",
     *     invalidMessage="Votre valeur doit être comprise entre 0 et 5 !"
     * )
     */
    private $notation;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="Vous devez entrer un commentaire !")
     * @Assert\Length(min="10", minMessage="Votre commentaire doit contenir au moins {{ limit }} caractères")
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ratings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Movie", inversedBy="ratings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $movie;

    public function getStarsInfo() {
        $starsNb = 5;

        $halfStars = fmod($this->notation, 1) / 0.5;
        $fullStars = $this->notation - ($halfStars * 0.5);
        $fullEmptyStars = $starsNb - $fullStars - $halfStars;

        return [
            'starsNb' => $this->notation,
            'fullStars' => $fullStars,
            'halfStars' => $halfStars,
            'fullEmptyStars' => $fullEmptyStars
        ];
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedAtValue() {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getNotation(): ?int
    {
        return $this->notation;
    }

    public function setNotation(int $notation): self
    {
        $this->notation = $notation;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }
}
