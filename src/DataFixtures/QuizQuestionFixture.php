<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Game\Quiz\Phase\Prices\PricesQuestion;
use App\Entity\Game\Quiz\Phase\Questions\QuestionsQuestion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class QuizQuestionFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $question = new QuestionsQuestion();
            $question->setQuestion('Is this question ' . $i . ' ?');
            $question->setEnabled(true);

            $manager->persist($question);
            $manager->flush();

            $this->addReference('question_' . $i, $question);
        }

        for ($i = 1; $i <= 3; $i++) {
            $question = new PricesQuestion();
            $question->setQuestion('How much cost Xiaomi mi band ' . $i . ' ?');
            $question->setEnabled(true);

            $manager->persist($question);
            $manager->flush();

            $this->addReference('question_price_' . $i, $question);
        }
    }
}
