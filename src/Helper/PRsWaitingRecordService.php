<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Helper;

use App\Database\Entity\PRWaitingStat;
use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;

class PRsWaitingRecordService
{
    /**
     * @var string
     */
    private string $githubToken;

    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * @param string $githubToken
     * @param EntityManager $entityManager
     */
    public function __construct(string $githubToken, EntityManager $entityManager)
    {
        $this->githubToken = $githubToken;
        $this->entityManager = $entityManager;
    }

    /**
     * @param bool $isDryRun
     *
     * @return string[]
     */
    public function recordAllPRsWaiting(bool $isDryRun): array
    {
        $logs = [];
        $map = PRStatsHelper::getTypesWithUrls();

        foreach ($map as $type => $url) {
            $total = $this->getCount($url);
            $statRecord = new PRwaitingStat($type, new DateTime(), $total);
            $logs[] = sprintf('Recorded %d PRs %s', $total, $type);
            if (!$isDryRun) {
                $this->entityManager->persist($statRecord);
            }
        }

        if (!$isDryRun) {
            $this->entityManager->flush();
        }

        return $logs;
    }

    /**
     * @param string $url
     *
     * @return int
     */
    private function getCount(string $url): int
    {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curlHandle, CURLOPT_VERBOSE, false);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER,
            array(
                'User-Agent: PHP Script',
                'Content-Type: application/json;charset=utf-8',
                'Authorization: bearer ' . $this->githubToken
            )
        );

        $data = json_decode(curl_exec($curlHandle), true);
        $info = curl_getinfo($curlHandle);
        $code = $info['http_code'];

        if ($code !== 200) {
            throw new Exception(sprintf('Received %s response', $code));
        }

        if (!isset($data['total_count'])) {
            throw new Exception('No total_count in JSON response');
        }

        return $data['total_count'];
    }
}
