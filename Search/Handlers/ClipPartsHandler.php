<?php

declare(strict_types=1);

namespace App\Services\Search\Handlers;

use App\Models\Eloquent\Contracts\RegNumber as ERegNumberContract;
use App\Models\Std\Data\DefaultData;
use App\Repository\CarNumberFailedLog;
use App\Repository\Traits\RegNumberTrait;
use App\Repository\Vehicle\VehicleCode;
use App\Services\External\ClipPartsManager;
use App\Services\Search\DTO\SearchData;
use App\Services\Search\Handlers\Contracts\ExternalHandlerInterface;
use App\Services\Search\Handlers\Contracts\HandlerInterface;
use Illuminate\Support\Arr;

/**
 * Class ClipPartsHandler
 * @package App\Services\Search\Handlers
 */
class ClipPartsHandler implements HandlerInterface, ExternalHandlerInterface
{
    use RegNumberTrait;

    /**
     * @var ClipPartsManager
     */
    private $manager;

    /**
     * @var array
     */
    private $result;

    /**
     * NgcDataHandler constructor.
     */
    public function __construct()
    {
        $this->manager = app(ClipPartsManager::class);
    }

    /**
     * @inheritDoc
     */
    public function handle(SearchData $data): ?ERegNumberContract
    {
        $this->result = $this->manager->get($data);

        if (!$this->checkStatus()) {
            app(CarNumberFailedLog::class)->store($data, $this->getErrorMessage());

            return null;
        }

        $result = $this->prepareData();

        return $this->storeNumber($data->getNumber(), new DefaultData($result), $this->result);
    }

    /**
     * @return bool
     */
    private function checkStatus(): bool
    {
        return (bool)Arr::get($this->result, 'success') && Arr::get($this->result, 'cpVehicleID');
    }

    /**
     * @return string
     */
    private function getErrorMessage(): string
    {
        return 'Service ClipParts: ' . serialize($this->result);
    }

    /**
     * @return array
     */
    private function prepareData(): array
    {
        $carId = app(VehicleCode::class)->getByCpId(Arr::get($this->result, 'cpVehicleID'));

        return [
            'carId' => $carId,
            'vin'   => null,
        ];
    }
}
