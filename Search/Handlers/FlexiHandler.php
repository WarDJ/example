<?php

declare(strict_types=1);

namespace App\Services\Search\Handlers;

use App\Models\Eloquent\Contracts\RegNumber as ERegNumberContract;
use App\Models\Std\Data\DefaultData;
use App\Repository\CarNumberFailedLog;
use App\Repository\Traits\RegNumberTrait;
use App\Services\External\FlexiManager;
use App\Services\Search\DTO\SearchData;
use App\Services\Search\Handlers\Contracts\ExternalHandlerInterface;
use App\Services\Search\Handlers\Contracts\HandlerInterface;
use Illuminate\Support\Arr;

/**
 * Class FlexiHandler
 * @package App\Services\Search\Handlers
 */
class FlexiHandler implements HandlerInterface, ExternalHandlerInterface
{
    use RegNumberTrait;

    private const FIELD_CAR_ID = 49;
    private const FIELD_VIN = 17;

    /**
     * @var FlexiManager
     */
    private $manager;

    /**
     * @var array
     */
    private $result;

    /**
     * FlexiHandler constructor.
     */
    public function __construct()
    {
        $this->manager = app(FlexiManager::class);
    }

    /**
     * @inheritDoc
     */
    public function handle(SearchData $data): ?ERegNumberContract
    {
        $this->result = $this->manager->get($data);

        if ($this->checkStatus()) {
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
        return Arr::get($this->result, 'hasFieldErrors');
    }

    /**
     * @return string
     */
    private function getErrorMessage(): string
    {
        return 'Service Flexi: ' . serialize($this->result);
    }

    /**
     * @return array
     */
    private function prepareData(): array
    {
        $vehicle = Arr::pluck(Arr::get($this->result, 'result.vehicleIdentification'), 'value','attribID');

        return [
            'carId' => Arr::get($vehicle, self::FIELD_CAR_ID),
            'vin'   => Arr::get($vehicle, self::FIELD_VIN),
        ];
    }
}
