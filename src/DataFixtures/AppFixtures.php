<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\PossibleAnswer;
use App\Entity\Question;
use App\Entity\QuestionType;
use App\Entity\Quiz;
use App\Entity\Theme;
use App\Entity\User;
use App\Enum\Difficulty;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ){}

    public function load(ObjectManager $manager): void
    {
        $entities = [
            User::class => null,
            QuestionType::class => null,
            Theme::class => null,
            Quiz::class => null,
            Question::class => null,
            PossibleAnswer::class => null,
            Answer::class => null,
        ];

        // Fetch data
        foreach (array_keys($entities) as $entity) {
            $objects = $this->getData($entity, $entities);
            $entities[$entity] = $objects;
        }

        foreach ($entities[Answer::class] as $answer) {
            $indexUser = random_int(0, count($entities[User::class])-1);
            $indexQuiz = random_int(0, count($entities[Quiz::class])-1);
            $answer->setUser($entities[User::class][$indexUser]);
            $answer->setQuiz($entities[Quiz::class][$indexQuiz]);
        }

        // Push in database
        foreach ($entities as $entity => $objects) {
            foreach ($objects as $object) {
                $manager->persist($object);
            }
            $manager->flush();
        }
    }

    public function getData(string $className, array $entities): array {
        $objects = [];
        $name = str_replace('App\Entity\\', '', $className);
        $json = file_get_contents(__DIR__ . "\json\\". lcfirst($name) .".json");
        $json_objects = json_decode($json, true);

        if (empty($json_objects)) {
            return $objects;
        }

        $setters = $this->getSetters($className);
        foreach ($json_objects as $json_object) {
            $object = new $className();
            foreach ($setters as $setter) {
                // Générer le nom de la propriété à partir du setter
                $property = lcfirst(substr($setter, 3)); // Retire "set" et met la 1ère lettre en minuscule

                // Vérifier si le JSON contient une valeur pour cette propriété
                if (array_key_exists($property, $json_object)) {
                    // Appeler le setter avec la valeur correspondante
                    $value = $this->getValueProperty($object, $json_object, $property, $className, $entities);
                    $object->$setter($value);
                }
            }
            $objects[] = $object;
        }
        return $objects;
    }

    function getValueProperty($object, $json_object, string $property, string $className, array $entities) : mixed {
        $value = $json_object[$property];

        switch ($className) {
            case User::class:
                if ($property == "password") {
                    $value = $this->hasher->hashPassword($object, $value);
                }
                break;
            case Question::class:
                if ($property == "quiz") {
                    $value = $entities[Quiz::class][$value];
                } elseif ($property == "type") {
                    $value = $entities[QuestionType::class][$value];
                }
                break;
            case Quiz::class:
                if ($property == "difficulty") {
                    $value = Difficulty::tryFrom($value);
                } elseif ($property == "maxTime") {
                    $value = DateInterval::createFromDateString($value);
                } elseif ($property == "user") {
                    $value = $entities[User::class][$value];
                } elseif ($property == "theme") {
                    $value = $entities[Theme::class][$value];
                }
                break;
            case PossibleAnswer::class:
                if ($property == "question") {
                    $value = $entities[Question::class][$value];
                }
                break;
            case Answer::class:
                if ($property == "time") {
                    $value = DateInterval::createFromDateString($value);
                } elseif ($property == "dateTime") {
                    $value = DateTime::createFromFormat("Y-m-d H:i:s", $value);
                }
                break;
            default:
                break;
        }
        return $value;
    }

    function getSetters(string $className): array
    {
        if (!class_exists($className)) {
            throw new \Exception("La classe $className n'existe pas.");
        }

        $reflection = new \ReflectionClass($className);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC); // Méthodes publiques
        $setters = [];

        foreach ($methods as $method) {
            // Vérifie si la méthode commence par "set" et a un nom valide
            if (str_starts_with($method->getName(), 'set') && strlen($method->getName()) > 3) {
                $setters[] = $method->getName();
            }
        }

        return $setters;
    }
}
