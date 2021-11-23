<?php

declare(strict_types=1);

namespace App\Services\Search\DTO;

use App\Helpers\ObjectTrait;

/**
 * Class UserData
 * @package App\Services\Search\DTO
 */
class UserData
{
    use ObjectTrait;

    /**
     * @var string
     */
    private $ip;

    /**
     * UserData constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->fill($data);
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }
}