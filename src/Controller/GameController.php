<?php

namespace App\Controller;

use App\Entity\Game;
use App\Framework\Controller\BaseController;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends BaseController
{
    /**
     * @Route("/api/games", methods={"GET"}, name="gamesGetList")
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request)
    {
        $query = $request->get('q');
        if (!$query) {
            return $this->response(['games' => []]);
        }
        $page = (int)$request->get('page');
        if ($page < 1)
            $page = 1;

        $itemsPerPage = 5;

        $gamesQuery = $this->em->createQueryBuilder()
            ->from(Game::class, 'game')
            ->select('game')
            ->where('game.name like :query')
            ->setMaxResults(10)
            ->setParameters([
                'query' => "%{$query}%",
            ])
        ;

        if (is_numeric($query)) {
            $gamesQuery
                ->orWhere('game.id = :gameId')
                ->setParameter('gameId', $query)
            ;
        }

        $paginator = new Paginator($gamesQuery->getQuery());

        $paginator->getQuery()
            ->setFirstResult($itemsPerPage*($page - 1))
            ->setMaxResults($itemsPerPage);

        /** @var Game[] $games */
        $games = $paginator->getIterator();

        $totalCount = $paginator->count();
        $maxPageNumber = ceil($totalCount / $itemsPerPage);

        return $this->response(
            [
                'games' => $games,
                'maxPageNumber' => $maxPageNumber
            ],
            ['attributes' => ['id', 'name']]
        );
    }
}
