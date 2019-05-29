<?php

namespace App\Framework\Renderer;

abstract class BasicRenderer implements RendererInterface
{
    abstract public function render(array $data);
    
    abstract public function isSuitable(array $data);
}
