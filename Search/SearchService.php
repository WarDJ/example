<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Exceptions\ResponseException;
use App\Helpers\Env;
use App\Models\Eloquent\Contracts\RegNumber as RegNumberContract;
use App\Services\Enums\ErrorCode;
use App\Services\Enums\SearchLog;
use App\Services\Search\DTO\SearchData;
use App\Services\Search\Handlers\Contracts\HandlerInterface;
use App\Services\Search\Handlers\Contracts\InternalHandlerInterface;
use App\Services\SearchLogService;
use Illuminate\Http\Response;

/**
 * Class SearchService
 * @package App\Services\Search
 */
class SearchService
{
    /**
     * @var array
     */
    private $handlers;

    /**
     * @var SearchLogService
     */
    private $logger;

    /**
     * SearchService constructor.
     *
     * @param array $handlers
     *
     * @throws \Exception
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
        $this->logger = app()->make(SearchLogService::class);
    }

    /**
     * @param $data
     *
     * @return RegNumberContract
     * @throws ResponseException
     */
    public function get(SearchData $data): RegNumberContract
    {
        $result = $this->pipeline($data);

        if (is_null($result)) {
            throw new ResponseException(ErrorCode::E_VEHICLE_NOT_FOUND, [], Response::HTTP_NOT_FOUND);
        }

        return $result;
    }

    /**
     * @param SearchData $data
     *
     * @return RegNumberContract|null
     */
    private function pipeline(SearchData $data): ?RegNumberContract
    {
        foreach ($this->handlers as $handler) {
            $data->setParams($handler['params'] ?? []);

            $handler = new $handler['handler'];

            $result = $handler->handle($data);

            if (!empty($result)) {
                $this->saveLog($handler);

                return $result;
            }
        }

        $this->logger->save(SearchLog::SERVICE_FAILURE);

        return null;
    }

    /**
     * @param HandlerInterface $handler
     */
    private function saveLog(HandlerInterface $handler)
    {
        $logType = SearchLog::SERVICE_SUCCESS;

        if ($handler instanceof InternalHandlerInterface) {
            $logType = SearchLog::SYSTEM_SUCCESS;
        }

        $this->logger->save($logType);
    }

}
