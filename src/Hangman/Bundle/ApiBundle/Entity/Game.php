<?php

namespace Hangman\Bundle\ApiBundle\Entity;

use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Accessor;

/**
 * Game
 */
class Game {

    const STATUS_BUSY = "busy";
    const STATUS_FAIL = "fail";
    const STATUS_SUCCESS = "success";

    /**
     * @var integer
     */
    private $triesLeft;

    /**
     * @var string
     * @Accessor(getter="getAnswer",setter="setWord")
     */
    private $word;

    /**
     * (busy|fail|success).
     * @var string
     */
    private $status;

    /**
     * @var array
     * @Exclude
     */
    private $charactersGuessed = array();

    /**
     * @var integer
     */
    private $id;

    /**
     *
     * @param integer $triesLeft
     *
     * @return Game
     */
    public function setTriesLeft($triesLeft) {
        $this->triesLeft = $triesLeft;

        return $this;
    }

    /**
     * Get triesLeft
     *
     * @return integer
     */
    public function getTriesLeft() {
        return $this->triesLeft;
    }

    /**
     * Set word
     *
     * @param string $word
     *
     * @return Game
     */
    public function setWord($word) {
        $this->word = $word;

        return $this;
    }

    /**
     * Get word
     *
     * @return string
     */
    public function getWord() {
        return $this->word;
    }

    /**
     * 
     * @return string
     */
    public function getAnswer() {
        $str = "";
        $word = $this->getWord();
        for ($i = 0; $i < strlen($word); $i++) {
            $str .= in_array($word[$i], $this->charactersGuessed) ? $word[$i] : ".";
        }
        return $str;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Game
     */
    public function setStatus($status) {
        if (!in_array($status, array(self::STATUS_BUSY, self::STATUS_FAIL, self::STATUS_SUCCESS))) {
            throw new \InvalidArgumentException("Status out of range");
        }
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set charactersGuessed
     *
     * @param array $charactersGuessed
     *
     * @return Game
     */
    public function setCharactersGuessed($charactersGuessed) {
        $this->charactersGuessed = $charactersGuessed;

        return $this;
    }

    /**
     * Get charactersGuessed
     *
     * @return array
     */
    public function getCharactersGuessed() {
        return $this->charactersGuessed;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $char
     *
     * @return bool
     */
    public function guess($char) {
        if (!preg_match("/^[a-z]{1}$/", $char)) {
            throw new \Exception("Character must be a-z");
        }
        if (in_array($char, $this->charactersGuessed)) {
            return $this;
        }
        if (strpos($this->getWord(), $char) !== false) {
            array_push($this->charactersGuessed, $char);
        } else {
            $this->triesLeft -= 1;
        }

        return $this;
    }

}
