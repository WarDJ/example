<?php

declare(strict_types=1);

namespace App\Services\Search\DTO;

use App\Helpers\ObjectTrait;

/**
 * Class SearchData
 * @package App\Services\Search\DTO
 */
class SearchData
{
    use ObjectTrait;

    /**
     * @var string
     */
    private $number;

    /**
     * @var UserData
     */
    private $user;

    /**
     * @var array
     */
    private $params;

    /**
     * @var int
     */
    private $countryId;

    /**
     * @var string
     */
    private $locale;

    /**
     * SearchData constructor.
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
    public function getNumber(): string
    {
        return (string)mb_strtoupper($this->number, 'UTF-8');
    }

    /**
     * @param mixed $number
     *
     * @return $this
     */
    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return UserData
     */
    public function getUser(): UserData
    {
        return $this->user;
    }

    /**
     * @param UserData $user
     *
     * @return $this
     */
    public function setUser(UserData $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return int
     */
    public function getCountryId(): int
    {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     *
     * @return $this
     */
    public function setCountryId(int $countryId): self
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

}