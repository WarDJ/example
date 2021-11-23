<?php

declare(strict_types=1);

namespace App\Services\Search\Handlers;

use App\Models\Eloquent\Contracts\RegNumber as ERegNumberContract;
use App\Models\Eloquent\RegNumber;
use App\Repository\Contracts\RegNumber as RRegNumberContract;
use App\Services\Search\DTO\SearchData;
use App\Services\Search\Handlers\Contracts\HandlerInterface;
use App\Services\Search\Handlers\Contracts\InternalHandlerInterface;

/**
 * Class RegNumberHandler
 * @package App\Services\Search\Handlers
 */
class RegNumberHandler implements HandlerInterface, InternalHandlerInterface
{
    /**
     * @var RegNumber
     */
    private $repository;

    /**
     * @inheritDoc
     */
    public function handle(SearchData $data): ?ERegNumberContract
    {
        $result = $this->getRepository()->findByNumber($data);

        if (empty($result)) {
            return null;
        }

        return $result;
    }

    /**
     * @return RRegNumberContract
     */
    private function getRepository(): RRegNumberContract
    {
        if (!$this->repository) {
            $this->repository = app(RRegNumberContract::class);
        }

        return $this->repository;
    }
}
