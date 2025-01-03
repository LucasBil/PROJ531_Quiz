<?php

namespace App\Enum;

enum Type: string
{
    case QCM = 'choix-multiple';
    case OPEN = 'ouverte';

    public function makeCoef(array $jsonQuestion) : float {
        return match ($this) {
            self::QCM => $this->coefQCM($jsonQuestion),
            self::OPEN => $this->coefOPEN($jsonQuestion),
        };
    }

    private function coefQCM(array $jsonQuestion) : float
    {

        $rightAnswer = count(
          array_filter($jsonQuestion["possibleAnswers"], fn($item) => $item["is_true"] === "true")
        );

        $right = 0;
        foreach ($jsonQuestion["possibleAnswers"] as $index => $answer) {
            if ($jsonQuestion["answer"][$index] === "true") {
                if ($answer["is_true"] === "true") {
                    $right++;
                } else {
                    return 0;
                }
            }
        }
        return $right/$rightAnswer;
    }

    private function coefOPEN(array $jsonQuestion) : float {
        $trueAnswer = array_filter($jsonQuestion["possibleAnswers"], function ($answer) {
            return $answer["is_true"] == "true";
        });
        $trueValues = array_map(function ($answer) {
            return $this->prettyValue($answer["value"]);
        }, $trueAnswer);

        return in_array($this->prettyValue($jsonQuestion["answer"]), $trueValues) ? 1 : 0;
    }

    private function prettyValue(string $value) : string {
        return strtolower(trim($value));
    }
}