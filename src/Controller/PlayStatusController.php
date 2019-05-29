<?php

namespace App\Controller;

use App\DBAL\Types\PlayStatusType;
use App\Framework\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PlayStatusController extends BaseController
{
    /**
     * @Route("/api/play-statuses", methods={"GET"})
     * @return JsonResponse
     */
    public function getPlayStatuses()
    {
        $statuses = PlayStatusType::getReadableValues();

        $statuses = array_map(function ($label, $id) {
            return [
                'id' => $id,
                'name' => $label,
            ];
        }, $statuses, array_keys($statuses));

        return $this->response([
            'statuses' => $statuses,
        ]);
    }
}
