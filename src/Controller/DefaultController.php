<?php

namespace App\Controller;

use App\Framework\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends BaseController
{
    /**
     * @return Response
     * @Route("/", name="index")
     * @Route("/api/login", name="login")
     * @Route("/api/logout", name="logout")
     */
    public function __invoke()
    {
        // however we will never get to this through index route because nginx serves it to index.html
        return new Response(file_get_contents(__DIR__.'/../../public/index.html'));
    }
}
