<?php
namespace Hangman\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Hangman\Bundle\ApiBundle\Entity\Game;

class HelloWorldController extends FOSRestController
{
    public function helloWorldAction()
    {
        $view = $this->view("", 201);
        $view->setData(array("game" => "..."));
        return $this->handleView($view);
    }
} 