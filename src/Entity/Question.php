<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $points = null;

    #[ORM\Column(length: 510)]
    private ?string $statement = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?QuestionType $id_type = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $id_quiz = null;

    /**
     * @var Collection<int, PossibleAnswer>
     */
    #[ORM\OneToMany(targetEntity: PossibleAnswer::class, mappedBy: 'id_question')]
    private Collection $possibleAnswers;

    public function __construct()
    {
        $this->possibleAnswers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getStatement(): ?string
    {
        return $this->statement;
    }

    public function setStatement(string $statement): static
    {
        $this->statement = $statement;

        return $this;
    }

    public function getIdType(): ?QuestionType
    {
        return $this->id_type;
    }

    public function setIdType(?QuestionType $id_type): static
    {
        $this->id_type = $id_type;

        return $this;
    }

    public function getIdQuiz(): ?Quiz
    {
        return $this->id_quiz;
    }

    public function setIdQuiz(?Quiz $id_quiz): static
    {
        $this->id_quiz = $id_quiz;

        return $this;
    }

    /**
     * @return Collection<int, PossibleAnswer>
     */
    public function getPossibleAnswers(): Collection
    {
        return $this->possibleAnswers;
    }

    public function addPossibleAnswer(PossibleAnswer $possibleAnswer): static
    {
        if (!$this->possibleAnswers->contains($possibleAnswer)) {
            $this->possibleAnswers->add($possibleAnswer);
            $possibleAnswer->setIdQuestion($this);
        }

        return $this;
    }

    public function removePossibleAnswer(PossibleAnswer $possibleAnswer): static
    {
        if ($this->possibleAnswers->removeElement($possibleAnswer)) {
            // set the owning side to null (unless already changed)
            if ($possibleAnswer->getIdQuestion() === $this) {
                $possibleAnswer->setIdQuestion(null);
            }
        }

        return $this;
    }
}
