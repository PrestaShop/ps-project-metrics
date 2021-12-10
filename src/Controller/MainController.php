<?php

namespace App\Controller;

use App\Helper\TeamHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Database\PDOProvider;
use PDO;

class MainController extends AbstractController
{
    /**
     * @var PDOProvider
     */
    private $pdoProvider;

    /**
     * MainController constructor.
     * @param PDOProvider $provider
     */
    public function __construct(PDOProvider $provider)
    {
        $this->pdoProvider = $provider;
    }

    /**
     * @return Response
     */
    public function home(): Response
    {
        $lastSevenAndDays = $this->getTeamStatsGroupedByLogin(56, 0);

        $lastThirtyDays = $this->getTeamStatsGroupedByDay(240, 56);

        return $this->render(
            'main.html.twig',
            [
                'lastSeven' => $lastSevenAndDays,
                'lastThirty' => $lastThirtyDays,
            ]
        );
    }

    /**
     * @param string $login
     *
     * @return Response
     */
    public function viewDeveloper(string $login): Response
    {
        if (!in_array($login, TeamHelper::getTeam())) {
            throw $this->createNotFoundException('No developer');
        }

        $developerStats = $this->getDeveloperStats($login);

        return $this->render('developer.html.twig',
            ['stats' => $developerStats, 'login' => $login]
        );
    }

    /**
     * @param int $recordsNumber
     *
     * @return array
     */
    private function getTeamStatsGroupedByLogin(int $recordsNumber): array
    {
        $pdo = $this->pdoProvider->getPDO();

        $sql = 'SELECT login, day, total FROM reviews ORDER BY day DESC LIMIT ' . $recordsNumber;
        $result = $pdo->query($sql)->fetchAll();

        $days = [];
        $groupedByLogin = [
            'PierreRambaud' => [],      # Pierre R.
            'matks' => [],              # Mathieu F.
            'jolelievre' => [],         # Jonathan L.
            'matthieu-rolland' => [],   # Matthieu R.
            'Progi1984' => [],          # Franck L.
            'atomiix' => [],            # Thomas B.
            'NeOMakinG' => [],          # Valentin S.
            'sowbiba' => [],            # Ibrahima S.
        ];
        $total = 0;

        foreach ($result as $item) {
            $days[$item['day']] = $item['day'];
            $groupedByLogin = $this->addOrInsert(
                $groupedByLogin,
                $item['login'],
                $item['day'],
                $item['total']
            );
            $total += (int)$item['total'];
        }

        foreach ($groupedByLogin as $login => $group) {
            $groupedByLogin[$login] = array_reverse($group);
        }

        return [
            'days' => array_reverse($days),
            'lastSeven' => $groupedByLogin,
            'totalTeam' => $total,
        ];
    }

    /**
     * @param int $recordsNumber
     * @param int $skipRecordsNumber
     *
     * @return array
     */
    private function getTeamStatsGroupedByDay(int $recordsNumber, int $skipRecordsNumber): array
    {
        $pdo = $this->pdoProvider->getPDO();

        $sql = 'SELECT login, day, total FROM reviews ORDER BY day DESC LIMIT ' . $recordsNumber . ' OFFSET ' . $skipRecordsNumber;
        $result = $pdo->query($sql)->fetchAll();

        $groupedByDay = [];

        foreach ($result as $item) {
            $groupedByDay = $this->addOrInsert(
                $groupedByDay,
                $item['day'],
                $item['login'],
                $item['total']
            );
        }

        foreach ($groupedByDay as $day => $group) {
            $groupedByDay[$day] = $this->reorderByTeamOrder($group);
        }

        return [
            'teamMembers' => TeamHelper::getTeam(),
            'lastThirty' => $groupedByDay,
        ];
    }

    /**
     * @param string $login
     *
     * @return array
     */
    private function getDeveloperStats(string $login): array
    {
        $pdo = $this->pdoProvider->getPDO();

        $sql = sprintf(
            "SELECT day, PR, total FROM reviews WHERE login = '%s' ORDER BY day DESC", $login);;
        $result = $pdo->query($sql)->fetchAll();

        $cleanResult = [];
        foreach ($result as $item) {
            $cleanResult[] = [
                'day' => $item['day'],
                'PR' => $this->formatPRs($item['PR']),
                'total' => $item['total'],
            ];
        }

        return $cleanResult;
    }

    /**
     * @param array $groupedByLogin
     * @param string $login
     * @param string $day
     * @param int $total
     *
     * @return array
     */
    private function addOrInsert(array $groupedByLogin, string $login, string $day, int $total): array
    {
        if (!array_key_exists($login, $groupedByLogin)) {
            $groupedByLogin[$login] = [];
        }
        if (!array_key_exists($day, $groupedByLogin[$login])) {
            $groupedByLogin[$login][$day] = [];
        }
        $groupedByLogin[$login][$day] = $total;

        return $groupedByLogin;
    }

    /**
     * @param string $PRsString
     *
     * @return string
     */
    private function formatPRs(string $PRsString): string
    {
        if ($PRsString === '""') {
            return '';
        }
        $html = '';

        $isFirst = true;
        $items = explode(';', $PRsString);

        foreach ($items as $PR) {
            $PR = str_replace(['"', "'"], "", $PR);
            if ($isFirst) {
                $html .= sprintf(
                    '<a href="%s">%s#%s</a>',
                    $PR,
                    basename(dirname(dirname($PR))),
                    basename($PR)
                );
                $isFirst = false;
                continue;
            }

            $html .= sprintf(
                ', <a href="%s">%s#%s</a>',
                $PR,
                basename(dirname(dirname($PR))),
                basename($PR)
            );
        }

        return $html;
    }

    /**
     * @param array $groupedByLogin
     *
     * @return array
     */
    private function reorderByTeamOrder(array $groupedByLogin): array
    {
        $team = TeamHelper::getTeam(true);

        foreach ($groupedByLogin as $login => $group) {
            $team[$login] = $group;
        }

        return $team;
    }
}
