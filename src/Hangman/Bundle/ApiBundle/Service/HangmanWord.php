<?php

namespace Hangman\Bundle\ApiBundle\Service;

use Doctrine\ORM\EntityManager;
use Hangman\Bundle\ApiBundle\Entity\Game;

class HangmanWord {

    /** @var EntityManager */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Word
     */
    public function getRandom() {

        $max = $this->entityManager->createQuery('
            SELECT MAX(w.id) FROM HangmanApiBundle:Word w
            ')->getSingleScalarResult();

        return $this->entityManager->createQuery('
            SELECT w FROM HangmanApiBundle:Word w
            WHERE w.id >= :rand
            ORDER BY w.id ASC
            ')->setParameter('rand', rand(0, $max))
                ->setMaxResults(1)
                ->getSingleResult();
    }

    /**
     * @return string
     */
    public function getRandomWord() {
      return $this->getRandom()->getWord();
    }

}
