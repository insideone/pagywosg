<?php

namespace App\Renderer;

use App\Framework\Renderer\BasicRenderer;
use Symfony\Component\Serializer\SerializerInterface;

class DefaultRenderer extends BasicRenderer
{
    /** @var SerializerInterface */
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function render(array $data)
    {
        $context = $data['_context'] ?? [];

        unset($data['_context']);

        $data['success'] = empty($data['errors']);

        return $this->serializer->serialize($data, 'json', $context);
    }

    public function isSuitable(array $data)
    {
        return true;
    }
}
