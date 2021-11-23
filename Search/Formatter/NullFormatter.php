<?php

declare(strict_types=1);

namespace App\Services\Search\Formatter;

/**
 * Class NumberNull
 * @package App\Services\Search\Formatter
 */
class NullFormatter implements NumberFormatterInterface
{
    /**
     * @inheritDoc
     */
    public function handle(string $number): string
    {
        return $number;
    }
}