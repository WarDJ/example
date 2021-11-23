<?php

declare(strict_types=1);

namespace App\Services\Search\Handlers;

use App\Models\Eloquent\Contracts\RegNumber as ERegNumberContract;
use App\Models\Std\Data\DefaultData;
use App\Repository\CarNumberFailedLog;
use App\Repository\Traits\RegNumberTrait;
use App\Services\External\NgcDataManager;
use App\Services\Search\DTO\SearchData;
use App\Services\Search\Handlers\Contracts\ExternalHandlerInterface;
use App\Services\Search\Handlers\Contracts\HandlerInterface;
use Illuminate\Support\Arr;

/**
 * Class NgcDataHandler
 * @package App\Services\Search\Handlers
 */
class NgcDataHandler implements HandlerInterface, ExternalHandlerInterface
{
    use RegNumberTrait;

    /**
     * @var NgcDataManager
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
        $this->manager = app(NgcDataManager::class);
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
        return !Arr::has($this->result, 'error') && Arr::get($this->result, 'options');
    }

    /**
     * @return string
     */
    private function getErrorMessage(): string
    {
        return 'Service NgcData: ' . serialize($this->result);
    }

    /**
     * @return array
     */
    private function prepareData(): array
    {
        $item = Arr::first(Arr::get($this->result, 'options'));

        return [
            'carId' => Arr::get($item, 'value'),
            'vin'   => Arr::get($this->result, 'codif_vin_prf'),
        ];
    }
}
