<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotBlank(message: "Les points ne peuvent pas être vides.")]
    #[Assert\Positive(message: "Les points doivent être un nombre positif.")]
    private ?int $points = null;

    #[ORM\Column(length: 510)]
    #[Assert\NotBlank(message: "L'énoncé de la question ne peut pas être vide.")]
    #[Assert\Length(
        max: 510,
        maxMessage: "L'énoncé de la question ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $statement = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?QuestionType $type = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    /**
     * @var Collection<int, PossibleAnswer>
     */
    #[ORM\OneToMany(targetEntity: PossibleAnswer::class, mappedBy: 'question')]
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

    public function getType(): ?QuestionType
    {
        return $this->type;
    }

    public function setType(?QuestionType $type): static
    {
        $this->type = $type;

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
            $possibleAnswer->setQuestion($this);
        }

        return $this;
    }

    public function removePossibleAnswer(PossibleAnswer $possibleAnswer): static
    {
        if ($this->possibleAnswers->removeElement($possibleAnswer)) {
            // set the owning side to null (unless already changed)
            if ($possibleAnswer->getQuestion() === $this) {
                $possibleAnswer->setQuestion(null);
            }
        }

        return $this;
    }

    public function getRightAnswers() : array
    {
        return array_filter($this->possibleAnswers->toArray(), function (PossibleAnswer $possibleAnswer) {
            return $possibleAnswer->isTrue();
        });
    }

    public function jsonSerialize(): mixed
    {
        return array(
            'id' => $this->id,
            'statement' => $this->statement,
            'type' => $this->type,
            'possibleAnswers' => $this->possibleAnswers->toArray(),
            'points' => $this->points,
        );
    }
}
