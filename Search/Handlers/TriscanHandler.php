<?php

declare(strict_types=1);

namespace App\Services\Search\Handlers;

use App\Models\Eloquent\Contracts\RegNumber as ERegNumberContract;
use App\Models\Std\Data\DefaultData;
use App\Repository\CarNumberFailedLog;
use App\Repository\Traits\RegNumberTrait;
use App\Services\External\NgcDataManager;
use App\Services\External\TriscanManager;
use App\Services\Search\DTO\SearchData;
use App\Services\Search\Formatter\NullFormatter;
use App\Services\Search\Formatter\NumberFormatterInterface;
use App\Services\Search\Handlers\Contracts\ExternalHandlerInterface;
use App\Services\Search\Handlers\Contracts\HandlerInterface;
use Illuminate\Support\Arr;

/**
 * Class TriscanHandler
 * @package App\Services\Search\Handlers
 */
class TriscanHandler implements HandlerInterface, ExternalHandlerInterface
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
     * @var NumberFormatterInterface
     */
    protected $numberFormatter;

    /**
     * NgcDataHandler constructor.
     */
    public function __construct()
    {
        $this->manager = app(TriscanManager::class);
        $this->numberFormatter = app(NullFormatter::class);
    }

    /**
     * @inheritDoc
     */
    public function handle(SearchData $data): ?ERegNumberContract
    {
        $number = $data->getNumber();

        $data->setNumber($this->numberFormatter->handle($number));

        $this->result = $this->manager->get($data);

        $result = $this->prepareData();

        if (!$result || !$this->checkStatus()) {
            app(CarNumberFailedLog::class)->store($data, $this->getErrorMessage());

            return null;
        }

        return $this->storeNumber($number, new DefaultData($result), $this->result);
    }

    /**
     * @return bool
     */
    private function checkStatus(): bool
    {
        return !Arr::has($this->result, 'carId');
    }

    /**
     * @return string
     */
    private function getErrorMessage(): string
    {
        return 'Service Triscan: ' . serialize($this->result);
    }

    /**
     * @return array
     */
    private function prepareData(): ?array
    {
        $xmlResult = Arr::get($this->result, 'GetRegnrResult');

        if ($xmlResult) {
            $data = simplexml_load_string($xmlResult);

            $paramName = $this->getPrefixParamName() . '_VeReg';

            if (isset($data->Lank) && isset($data->Lank->EgenBilkod)) {
                $vin = null;

                if ($data->{$paramName}) {
                    $vin = $data->{$paramName}->Chassinr;
                }

                return [
                    'carId' => (int)$data->Lank->EgenBilkod ?? null,
                    'vin'   => (string)$vin ?? null,
                ];
            }
        }

        return null;
    }

    /**
     * @return string
     */
    protected function getPrefixParamName(): string
    {
        return ucfirst(app('Settings')->getLocale());
    }
}
