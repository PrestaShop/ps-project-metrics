<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Database\PDOProvider;
use PDO;

class MainController extends AbstractController
{
    private $pdoProvider;

    public function __construct(PDOProvider $provider)
    {
        $this->pdoProvider = $provider;
    }

    public function home(): Response
    {
        $lastSevenAndDays = $this->getLastSeven();

        return $this->render('main.html.twig', $lastSevenAndDays);
    }

        public function viewDeveloper($login): Response
    {
        $developer = [
            'PierreRambaud',      # Pierre R.
            'matks',              # Matthieu F.
            'jolelievre',         # Jonathan L.
            'matthieu-rolland',   # Matthieu R.
            'Progi1984',          # Franck L.
            'atomiix',            # Thomas B.
            'NeOMakinG',          # Valentin S.
            'sowbiba',            # Ibrahima S.
        ];

        if (!in_array($login, $developer)) {
            throw $this->createNotFoundException('No developer');
        }

        $developerStats = $this->getDeveloperStats($login);

        return $this->render('developer.html.twig',
            ['stats' => $developerStats, 'login' => $login]
        );
    }

    private function getLastSeven()
    {
        $pdo = $this->pdoProvider->getPDO();

        $sql = 'SELECT login, day, total FROM reviews ORDER BY day ASC LIMIT 56';
        $result = $pdo->query($sql)->fetchAll();

        $days = [];
        $groupedByLogin = [
            'PierreRambaud' => [],      # Pierre R.
            'matks' => [],              # Mattieu F.
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
            $total += (int) $item['total'];
        }

        return [
            'days' => $days,
            'lastSeven' => $groupedByLogin,
            'totalTeam' => $total,
        ];
    }

    private function getDeveloperStats($login)
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

    private function addOrInsert($groupedByLogin, $login, $day, $total)
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

    private function formatPRs(string $PRsString)
    {
        if ($PRsString === '""') {
            return '';
        }

        $html = '';

        $isFirst = true;
        $items = explode(';', $PRsString);

        foreach ($items as $PR) {


            $PR = str_replace(['"',"'"], "", $PR);
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
}