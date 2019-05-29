<?php

namespace App\Framework\Renderer;

interface RendererInterface
{
    public function render(array $data);

    public function isSuitable(array $data);
}
