<?php

declare(strict_types=1);

namespace App\Services\Search\Formatter;

/**
 * Interface NumberFormatterInterface
 * @package App\Services\Search\Formatter
 */
interface NumberFormatterInterface
{
    /**
     * @param string $number
     *
     * @return string
     */
    public function handle(string $number): string;
}