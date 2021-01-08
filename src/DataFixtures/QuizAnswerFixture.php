<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Game\Quiz\Phase\Prices\PricesAnswer;
use App\Entity\Game\Quiz\Phase\Questions\QuestionsAnswer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class QuizAnswerFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 3; $i++) {
            for ($j = 1; $j <= 4; $j++) {
                $answer = new QuestionsAnswer();
                $answer->setQuestion($this->getReference('question_' . $i));
                $answer->setAnswer('answer ' . $j);
                $answer->setCorrect($j === 1);

                $manager->persist($answer);
                $manager->flush();

                $this->addReference('answer' . $i . '_' . $j, $answer);
            }
        }

        for ($i = 1; $i <= 3; $i++) {
            $answer = new PricesAnswer();
            $answer->setQuestion($this->getReference('question_price_' . $i));
            $answer->setAnswer(10 * $i);

            $manager->persist($answer);
            $manager->flush();

            $this->addReference('answer_price_' . $i . '_', $answer);
        }
    }

    public function getDependencies(): array
    {
        return [
            QuizQuestionFixture::class,
        ];
    }
}
