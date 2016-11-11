<?php

/**
 * Description of GameController
 *
 * @author wojtek
 */

namespace Hangman\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class GameController extends FOSRestController {

    /**
     * @return View
     */
    public function createAction(Request $request) {
        $view = $this->view("", 201);
        
        $gameManager = $this->get('hangman.game');
        $game = $gameManager->create();
        
        $view->setData(array("game" => $game));
        return $this->handleView($view);
    }
    
    /**
     * @param integer $id
     *
     * @return View
     */
    public function guessAction(Request $request, $id) {
        $view = $this->view("", 200);
        $char = $request->get('char');
        
        $gameManager = $this->get('hangman.game');
        $game = $gameManager->guess($id, $char);
        
        $view->setData(array("game" => $game));
        return $this->handleView($view);
    }

}
