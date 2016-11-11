<?php

namespace Hangman\Bundle\ApiBundle\Service;

use Doctrine\ORM\EntityManager;
use Hangman\Bundle\ApiBundle\Entity\Game;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HangmanGame {

    /** @var EntityManager */
    private $entityManager;

    const MAX_TRIES = 11;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Game
     */
    public function create() {

        $max = $this->entityManager->createQuery('
            SELECT MAX(w.id) FROM HangmanApiBundle:Word w
            ')->getSingleScalarResult();

        $randomWord = $this->entityManager->createQuery('
            SELECT w FROM HangmanApiBundle:Word w 
            WHERE w.id >= :rand
            ORDER BY w.id ASC
            ')->setParameter('rand', rand(0, $max))
                ->setMaxResults(1)
                ->getSingleResult();

        $game = new Game();
        $game->setTriesLeft(self::MAX_TRIES);
        $game->setWord($randomWord->getWord());
        $game->setStatus(Game::STATUS_BUSY);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        return $game;
    }

    public function guess($id, $char) {
        $game = $this->entityManager
                ->getRepository('HangmanApiBundle:Game')
                ->find($id);
        if (!$game) {
            throw new HttpException(404, "No games found for id " . $id);
        }

        try {
            if ($game->getStatus() === Game::STATUS_BUSY && $game->getTriesLeft() > 0) {
                $game->guess($char);
            }
            if (strcmp($game->getWord(),$game->getAnswer()) === 0) {
                $game->setStatus(Game::STATUS_SUCCESS);
            } elseif ($game->getStatus() === Game::STATUS_BUSY && $game->getTriesLeft() <= 0) {
                $game->setStatus(Game::STATUS_FAIL);
            }
        } catch (\Exception $e) {
            throw new HttpException(400, $e->getMessage());
        }

        $this->entityManager->persist($game);
        $this->entityManager->flush();
        return $game;
    }

}
