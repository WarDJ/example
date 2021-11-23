<?php

declare(strict_types=1);

namespace App\Services\Search\Formatter;

/**
 * Class NumberFI
 * @package App\Services\Search\Formatter
 */
class NumberFI implements NumberFormatterInterface
{
    /**
     * @param string $number
     *
     * @return string
     */
    public function handle(string $number): string
    {
        $regNumber = trim($number);

        if (ctype_alpha($regNumber[0])) {
            preg_match_all('/[a-zA-Z]+/', $regNumber, $characterMatches);
            preg_match_all('/\d+/', $regNumber, $numberMatches);

            if (empty($characterMatches[0][0])
                || empty($numberMatches[0][0])
                || count($characterMatches[0]) > 1
                || count($numberMatches[0]) > 1
            ) {
                return $number;
            }

            $number = $characterMatches[0][0] . '-' . $numberMatches[0][0];
        }

        return $number;
    }
}