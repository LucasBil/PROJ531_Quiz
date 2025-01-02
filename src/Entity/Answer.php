<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use DateInterval;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnswerRepository::class)]
class Answer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATEINTERVAL)]
    private ?DateInterval $time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $date_time = null;

    #[ORM\ManyToOne(inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTime(): ?DateInterval
    {
        return $this->time;
    }

    public function setTime(DateInterval $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function getDateTime(): ?DateTime
    {
        return $this->date_time;
    }

    public function setDateTime(DateTime $date_time): static
    {
        $this->date_time = $date_time;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }
}
