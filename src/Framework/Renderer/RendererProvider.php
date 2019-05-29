<?php

namespace App\Framework\Renderer;

/**
 * Provide a renderer for a response
 */
class RendererProvider
{
    /** @var RendererInterface[] */
    protected $renderers = [];

    public function addRenderer(RendererInterface $renderer)
    {
        $this->renderers[] = $renderer;
        return $this;
    }

    public function getRenderer($data)
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->isSuitable($data)) {
                return $renderer;
            }
        }

        return null;
    }
}
