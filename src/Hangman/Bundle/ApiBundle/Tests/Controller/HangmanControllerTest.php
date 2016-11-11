<?php
namespace Hangman\Bundle\ApiBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Hangman\Bundle\ApiBundle\Entity\Game;


class HangmanControllerTest extends WebTestCase
{
    
    private $client;
    
    private $orm;
    
    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        
        $this->orm = $this->getContainer()->get('doctrine');
    }
    
    public function testSuccess () {
      $gameId = $this->assertCreate();
      $this->assertWin($gameId);
    }
    public function testFailure () {
      $gameId = $this->assertCreate();
      $this->assertLoose($gameId);
    }
    public function testHttpNotFound () {
      $response = $this->putRequest("nosuchid", "x");
      $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testHttpBadReuest () {
      $gameId = $this->assertCreate();
      $response = $this->putRequest($gameId, "xxx");
      $this->assertEquals(400, $response->getStatusCode());
    }
    
    private function assertCreate()
    {
        $this->client->request('POST', '/games');
        $response = $this->client->getResponse();
        $this->assertEquals(201, $response->getStatusCode());
        $json = $this->fetchJson($response);
        $this->assertEquals(Game::STATUS_BUSY, $json->game->status);
        return $json->game->id;
    }
    
    private function assertWin ($gameId) {
      $game = $this->orm->getRepository('HangmanApiBundle:Game')
        ->find($gameId);
      $word = $game->getWord();
      for ($i = 0; $i < strlen($word); $i++) {
        $response = $this->putRequest($gameId, $word[$i]);
        $json = $this->fetchJson($response);
        if (strcmp($json->game->word,$game->getWord()) === 0) {
          $this->assertEquals(Game::STATUS_SUCCESS, $json->game->status);
          break;
        } else {
          $this->assertEquals(Game::STATUS_BUSY, $json->game->status);
        }
      }
    }
    
    private function assertLoose ($gameId) {
      $game = $this->orm->getRepository('HangmanApiBundle:Game')
        ->find($gameId);
      $word = str_split($game->getWord());
      $letter = "x";
      for ($i = 10; $i >= 0; $i--) {
        $response = $this->putRequest($gameId, $letter);
        $json = $this->fetchJson($response);
        $this->assertEquals($i, $json->game->tries_left);
        if ($i === 0) {
          $this->assertEquals(Game::STATUS_FAIL, $json->game->status);
        } else {
          $this->assertEquals(Game::STATUS_BUSY, $json->game->status);
        }
      }
    }
    
    private function putRequest ($gameId, $char) {
      $this->client->request('PUT', '/games/' . $gameId . "?char=".$char);
      $response = $this->client->getResponse();
      return $response;
    }
    
    
    private function fetchJson($response) {
        $content = $response->getContent();
        $json = json_decode($content);
        return $json;
    }
}
