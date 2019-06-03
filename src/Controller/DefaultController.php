<?php

namespace App\Controller;

use App\Entity\User;
use App\Framework\Controller\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends BaseController
{
    /**
     * @return Response
     * @Route("/{page}", name="index", defaults={"page" = ""})
     * @Route("/login", name="login")
     * @Route("/events/new", name="new_event_page")
     * @Route("/events/{eventId}", name="event_detail_page")
     * @Route("/events/{eventId}/edit", name="event_edit_page")
     * @Route("/events/{eventId}/leaderboard", name="event_leaderboard_page")
     * @Route("/help/{page}", name="help_page", defaults={"page" = ""})
     */
    public function __invoke()
    {
        return new Response(file_get_contents(__DIR__.'/../Frontend/index.html'));
    }
}
