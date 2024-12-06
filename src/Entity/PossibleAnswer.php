<?php

namespace App\Entity;

use App\Repository\PossibleAnswerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PossibleAnswerRepository::class)]
class PossibleAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $is_true = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'possibleAnswers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $id_question = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isTrue(): ?bool
    {
        return $this->is_true;
    }

    public function setTrue(bool $is_true): static
    {
        $this->is_true = $is_true;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getIdQuestion(): ?Question
    {
        return $this->id_question;
    }

    public function setIdQuestion(?Question $id_question): static
    {
        $this->id_question = $id_question;

        return $this;
    }
}
