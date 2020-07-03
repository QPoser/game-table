<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Game\Game;
use App\Entity\Game\Quiz\Phase\Questions\Answer;
use App\Entity\Game\Quiz\Phase\Questions\Question;
use App\Entity\Game\Quiz\QuizGame;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class QuizAnswerFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 3; $i++) {
            for ($j = 1; $j <= 4; $j++) {
                $answer = new Answer();
                $answer->setQuestion($this->getReference('question_' . $i));
                $answer->setAnswer('answer 1');
                $answer->setCorrect($j === 1);

                $manager->persist($answer);
                $manager->flush();

                $this->addReference('answer' . $i . '_' . $j, $answer);
            }
        }
    }

    public function getDependencies(): array
    {
        return [
            QuizQuestionFixture::class,
        ];
    }
}
