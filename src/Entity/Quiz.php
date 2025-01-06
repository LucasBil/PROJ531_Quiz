<?php

namespace App\Entity;

use App\Enum\Difficulty;
use App\Repository\QuizRepository;
use DateInterval;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du quiz ne peut pas être vide.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom du quiz ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATEINTERVAL, nullable: true)]
    private ?DateInterval $max_time = null;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Theme $theme = null;

    /**
     * @var Collection<int, Answer>
     */
    #[ORM\OneToMany(targetEntity: Answer::class, mappedBy: 'quiz')]
    private Collection $answers;

    /**
     * @var Collection<int, Question>
     */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'quiz')]
    private Collection $questions;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private string $difficulty;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->difficulty = Difficulty::Easy->value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getMaxTime(): ?DateInterval
    {
        return $this->max_time;
    }

    public function setMaxTime(?DateInterval $max_time): static
    {
        $this->max_time = $max_time;

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

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): static
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setQuiz($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): static
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuiz() === $this) {
                $answer->setQuiz(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setQuiz($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getQuiz() === $this) {
                $question->setQuiz(null);
            }
        }

        return $this;
    }

    public function getDifficulty(): Difficulty
    {
        return Difficulty::from($this->difficulty);
    }

    public function setDifficulty(Difficulty $difficulty): static
    {
        $this->difficulty = $difficulty->value;

        return $this;
    }

    public function getMaxScore() : int
    {
        return (int) array_sum(
            $this->questions->map(function (Question $question) {
                return $question->getPoints();
            })->toArray()
        );
    }

    public function getUserAnswers() : array
    {
        return $this->answers->map(function (Answer $answer) {
            return $answer->getUser();
        })->toArray();
    }

    public function jsonSerialize(): mixed
    {
        return array(
            'id'=> $this->id,
            'name'=> $this->name,
            'theme'=> $this->theme,
            'difficulty'=> $this->difficulty,
            'creator'=> $this->user,
            'max_time'=> $this->max_time?->format("%H:%I:%S"),
            'max_score'=> $this->getMaxScore(),
            'questions' => $this->questions->toArray(),
            'answers' => $this->answers->toArray(),
            'response' => $this->getUserAnswers()
        );
    }
}