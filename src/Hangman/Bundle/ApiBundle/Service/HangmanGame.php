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
    public function __construct(EntityManager $entityManager, HangmanWord $word) {
        $this->entityManager = $entityManager;
        $this->word = $word;
    }

    /**
     * @return Game
     */
    public function create() {

        $word = $this->word->getRandomWord();
        
        $game = new Game();
        $game->setTriesLeft(self::MAX_TRIES);
        $game->setWord($word);
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
