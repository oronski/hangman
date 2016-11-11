<?php

namespace Hangman\Bundle\ApiBundle\Tests\Entity;

use Hangman\Bundle\ApiBundle\Entity\Game;

class GameTest extends \PHPUnit_Framework_TestCase {

    private $word = "hangman";

    private $tries = 11;

    public function testCharOutOfRange() {
        $game = new Game();
        $game->setWord($this->word);
        $chars = array('abc', 1, '!', 'A', '*');
        foreach ($chars as $char) {
            try {
                $game->guess($char);
                $this->assertEquals(1, 2);
            } catch (\Exception $ex) {
                $this->assertEquals("Character must be a-z", $ex->getMessage());
            }
        }
    }

    public function testIncorrectChar() {
        $game = new Game();
        $game->setWord($this->word);
        $game->guess("x");
        $this->assertEquals(0, count($game->getCharactersGuessed()));
    }

    public function testCorrectChars() {
        $game = new Game();
        $game->setWord($this->word);
        $this->assertEquals($this->word, $game->getWord());
        $chars = array("h","m","n");
        $i = 0;
        foreach ($chars as $char) {
            $game->guess($char);
            $this->assertEquals(++$i, count($game->getCharactersGuessed()));
        }
    }

    public function testKeepTriesLeft() {
        $game = new Game();
        $game->setWord($this->word);
        $game->setTriesLeft($this->tries);
        $this->assertEquals($this->word, $game->getWord());
        $chars = array("h","m","n");
        foreach ($chars as $char) {
            $game->guess($char);
            $this->assertEquals($this->tries, $game->getTriesLeft());
        }
    }

    public function testDecrementTriesLeft() {
        $game = new Game();
        $game->setWord($this->word);
        $game->setTriesLeft($this->tries);
        $this->assertEquals($this->word, $game->getWord());
        $chars = array("x","y","z","f");
        $i = 1;
        foreach ($chars as $char) {
            $game->guess($char);
            $this->assertEquals($this->tries - $i++, $game->getTriesLeft());
        }
    }

}
