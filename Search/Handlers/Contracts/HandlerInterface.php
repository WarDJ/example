<?php

declare(strict_types=1);

namespace App\Services\Search\Handlers\Contracts;

use App\Models\Eloquent\Contracts\RegNumber as ERegNumberContract;
use App\Services\Search\DTO\SearchData;

/**
 * Interface HandlerInterface
 * @package App\Services\Search\Handlers\Contracts
 */
interface HandlerInterface
{
    /**
     * Handling the request and working with an external server
     *
     * @param SearchData $data
     *
     * @return mixed
     */
    public function handle(SearchData $data): ?ERegNumberContract;
}
