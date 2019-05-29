<?php

namespace App\Service;

use App\Framework\Renderer\RendererProvider;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

class ResponseRenderer implements EngineInterface
{
    /** @var RendererProvider */
    protected $rendererProvider;

    public function __construct(RendererProvider $rendererProvider)
    {
        $this->rendererProvider = $rendererProvider;
    }

    public function render($name, array $parameters = [])
    {
        return $this->rendererProvider->getRenderer($parameters)->render($parameters);
    }

    public function exists($name)
    {
        return true;
    }

    public function supports($name)
    {
        return true;
    }


    public function renderResponse($view, array $parameters = [], Response $response = null)
    {
        $response->setContent($this->render($view, $parameters));
        return $response;
    }
}
