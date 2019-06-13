<?php

namespace App\Command;

use App\Api\Steam\SteamIdApiProvider;
use App\Entity\Event;
use App\Entity\EventEntry;
use App\Entity\Game;
use App\Entity\GameCategory;
use App\Entity\User;
use App\Framework\Command\BaseCommand;
use App\Framework\Exceptions\UnexpectedResponseException;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use GuzzleHttp\Exception\GuzzleException;
use Knojector\SteamAuthenticationBundle\Security\User\SteamUserProvider;
use Knojector\SteamAuthenticationBundle\User\SteamUserInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\User\UserInterface;

class AddLoremIpsumEvents extends BaseCommand
{
    /** @var SteamUserProvider */
    protected $steamUserProvider;

    /** @var SteamIdApiProvider */
    protected $idProvider;

    public function __construct(SteamUserProvider $steamUserProvider, SteamIdApiProvider $idProvider)
    {
        parent::__construct();
        $this->steamUserProvider = $steamUserProvider;
        $this->idProvider = $idProvider;
    }

    protected function configure()
    {
        $this->setName('loip:events:add')
            ->getDefinition()
            ->addArguments([
                new InputArgument('user', InputArgument::IS_ARRAY, 'steamId or profileName')
            ]);
    }

    protected function proceed(InputInterface $input, OutputInterface $output)
    {
        $ss = new SymfonyStyle($input, $output);

        $userRepo = $this->em->getRepository(User::class);

        $users = [];

        $userIdentities = $input->getArgument('user');
        foreach ($userIdentities as $userIdentity) {
            try {
                $user = $userRepo->findByIdentity($userIdentity);
            } catch (NonUniqueResultException $e) {
                $ss->error("Ambiguity with user indentity: {$userIdentity}");
                return;
            }

            if (!$user) {
                try {
                    $user = $this->createByIdentity($userIdentity);
                } catch (UnexpectedResponseException|GuzzleException $e) {
                    $ss->error($e->getMessage());
                    return;
                }
            }

            if (!$user) {
                $ss->error("Cannot obtain or create {$userIdentity}");
                return;
            }

            $users[] = $user;
        }

        $gameRepo = $this->em->getRepository(Game::class);

        $categories = [
            0 => [
                (new GameCategory)->setName('Star wars'),
                (new GameCategory)->setName('Space'),
                (new GameCategory)->setName('4 related'),
            ],
            1 => [
                (new GameCategory)->setName('Female protagonist'),
                (new GameCategory)->setName('Won from a female user'),
                (new GameCategory)->setName('Funny / Joke / Humorous'),
            ]
        ];

        $gameCategoryRepo = $this->em->getRepository(GameCategory::class);
        array_walk_recursive($categories, function (GameCategory &$gameCategory) use ($gameCategoryRepo) {
            if ($existCategory = $gameCategoryRepo->findByName($gameCategory->getName())) {
                $gameCategory = $existCategory;
            }
        });

        $events = [
            (new Event)
                ->setName('May the 4th be with you')
                ->setDescription('
The theme for this month is

May the 4th be with you
Games that qualify for it are:
Star Wars games
Space games
Sci-Fi games
Games that have a "4" in their appID - if you don\'t know how to check the appID see below this list
Games that take around 4 hours to complete according to HLTB - HowLongToBeat website. Any of the 3 times (main story, main + extras, completionist) can be used.
Games that there is 4 of them in the series - can be whichever game in the series
Games where there is a character named May in it (or any variant of it)
Games a 4 year old could play - aka children appropriate games
Games with 4 way co-op
Games with 4 in the name
                ')
                ->setHost($users[0])
                ->setGameCategories($categories[0])
                ->setStartedAt(new DateTime)
                ->setEndedAt(new DateTime('+1 month'))
                ->setEntries([
                    (new EventEntry)
                        ->setPlayer($users[0])
                        ->setGame($gameRepo->find(291650)) // PoE
                        ->setCategory($categories[0][array_rand($categories[0], 1)])
                        ->setNotes('RPG'),
                    (new EventEntry)
                        ->setPlayer($users[1])
                        ->setGame($gameRepo->find(262060)) // Darkest Dungeon
                        ->setCategory($categories[0][array_rand($categories[0], 1)])
                        ->setNotes('pain'),
                ]),
            (new Event)
                ->setName('Girls just wanna have fun')
                ->setDescription('
Girls just wanna have fun
(Big thank you to Moony1986 for coming up with it!)

Games that qualify for it are:
Funny / Joke / Humorous games - anything tagged as such on the Steam store will do, you can also give your own argument on why the game should count as long as it is half reasonable :)
Games with female protagonist/antagonist - protagonist must be playable
Games you won from a female Steamgifts user - feel free to ask any of the GA creators or provide any kind of proof, just please don\'t be creepy ;D Here is also a comment if anyone is searching for any suggestions or anyone wants to add themselves on to make peoples life easier. Even if I know quite a few, I won\'t be adding anyone who does not want to be added out of respect of their privacy.
                ')
                ->setHost($users[1])
                ->setGameCategories($categories[1])
                ->setStartedAt(new DateTime)
                ->setEndedAt(new DateTime('+1 month'))
                ->setEntries([
                    (new EventEntry)
                        ->setPlayer($users[1])
                        ->setGame($gameRepo->find(291650)) // PoE
                        ->setCategory($categories[1][array_rand($categories[1], 1)])
                        ->setNotes('lalala'),
                    (new EventEntry)
                        ->setPlayer($users[0])
                        ->setGame($gameRepo->find(262060)) // Darkest Dungeon
                        ->setCategory($categories[1][array_rand($categories[1], 1)])
                        ->setNotes('test'),
                ]),
        ];

        foreach ($events as $event) {
            $this->em->persist($event);
        }

        $this->em->flush();

        $ss->success('Done!');
    }

    /**
     * @param string $secondUserIdentity
     * @return bool|SteamUserInterface|object|UserInterface|null
     * @throws UnexpectedResponseException
     * @throws GuzzleException
     */
    protected function createByIdentity(string $secondUserIdentity)
    {
        if (!is_numeric($secondUserIdentity)) {
            $secondUserIdentity = $this->idProvider->fetch($secondUserIdentity);
            if (!$secondUserIdentity) {
                return false;
            }
        }

        return $this->steamUserProvider->loadUserByUsername((int)$secondUserIdentity);
    }
}
