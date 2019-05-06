<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index()
    {
        return $this->redirectToRoute('app_register');
    }
}