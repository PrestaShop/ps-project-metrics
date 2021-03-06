<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Helper;

use App\Database\Entity\PRWaitingStat;

class PRStatsHelper
{
    /**
     * @param bool $outputApiUrl
     *
     * @return string[]
     */
    public static function getTypesWithUrls(bool $outputApiUrl = true): array
    {
        if ($outputApiUrl) {
            $baseUrl = 'https://api.github.com/search/issues?per_page=100';
        } else {
            $baseUrl = 'https://github.com/pulls?';
        }

        $mapping = [
            PRWaitingStat::PR_WAITING_FOR_REVIEW => 'q=org%3APrestaShop+is%3Apr+is%3Aopen+-label%3A%22waiting+for+QA%22+-label%3A%22waiting+for+UX%22+-label%3A%22waiting+for+author%22+-label%3A%22waiting+for+PM%22+-label%3A%22waiting+for+rebase%22+-label%3A%22QA+%E2%9C%94%EF%B8%8F%22+-label%3AWIP+archived%3Afalse+-repo%3Aprestashop%2Fprestashop-specs+draft%3Afalse',
            PRWaitingStat::PR_WAITING_FOR_QA => 'q=org%3APrestaShop+is%3Apr+is%3Aopen+label%3A"Waiting+for+QA"+archived%3Afalse',
            PRWaitingStat::PR_WAITING_FOR_PM => 'q=org%3APrestaShop+is%3Apr+is%3Aopen+label%3A"Waiting+for+PM"+archived%3Afalse',
            PRWaitingStat::PR_WAITING_FOR_DEV => 'q=org%3APrestaShop+is%3Apr+is%3Aopen+label%3A"Waiting+for+dev"+archived%3Afalse+sort%3Aupdated-asc',
            PRWaitingStat::PR_WAITING_FOR_DEV_AND_QA => 'q=org%3APrestaShop+is%3Apr+is%3Aopen+archived%3Afalse+sort%3Aupdated-asc+label%3A"Waiting+for+dev"+label%3A"Waiting+for+QA"',
            PRWaitingStat::PR_Open => 'q=org%3APrestaShop+is%3Apr+is%3Aopen+archived%3Afalse+sort%3Aupdated-asc',
        ];

        return array_map(function ($parameters) use ($baseUrl) {
            return $baseUrl . '&' . $parameters;
        }, $mapping);
    }

    /**
     * @return string[]
     */
    public static function getTypesWithLabels(): array
    {
        return [
            PRWaitingStat::PR_WAITING_FOR_REVIEW => 'Waiting for review',
            PRWaitingStat::PR_WAITING_FOR_QA => 'Waiting for QA',
            PRWaitingStat::PR_WAITING_FOR_PM => 'Waiting for PM',
            PRWaitingStat::PR_WAITING_FOR_DEV => 'Waiting for dev',
            PRWaitingStat::PR_WAITING_FOR_DEV_AND_QA => 'Waiting for QA by dev',
            PRWaitingStat::PR_Open => 'Open',
        ];
    }

    /**
     * @param bool $asKeys
     *
     * @return string[]|null[]
     */
    public static function getTypes(bool $asKeys = false): array
    {
        if ($asKeys) {
            return array_map(function () {
                return null;
            }, self::getTypesWithLabels());
        }

        return array_keys(self::getTypesWithLabels());
    }

    /**
     * @param array<string, mixed> $groupedByName
     *
     * @return array<string, mixed>
     */
    public static function reorderByPRTypeOrder(array $groupedByName): array
    {
        $reordered = array_fill_keys(self::getTypes(), 0);

        foreach ($groupedByName as $name => $group) {
            $reordered[$name] = $group;
        }

        return $reordered;
    }
}
