<?php

declare(strict_types=1);

namespace App\Modules;

use App\Exceptions\InvalidUrlException;
use App\Exceptions\QueryFailureException;
use CurlHandle;

class Query
{
    /** @var string */
    protected $sslPath;
    /** @var string */
    protected $referer;
    /** @var string */
    protected $results;
    /** @var string */
    protected $jsonResult;
    /** @var int */
    protected $errorCode;
    /** @var string */
    protected $errorMessage;
    /** @var string */
    protected $urlToQuery;
    /** @var CurlHandle resource */
    protected $curlHandler;

    private function __construct(string $url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidUrlException(
                "the url to query ({$url}) is not valid."
            );
        }
        $this->urlToQuery = $url;
    }

    public static function create(...$params)
    {
        return new static(...$params);
    }

    public function run()
    {
        $this->curlHandler = curl_init();

        $this->addSSL();

        curl_setopt($this->curlHandler, CURLOPT_URL, $this->urlToQuery);

        curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, 1);

        $this->results = curl_exec($this->curlHandler);
        $this->errorCode = curl_errno($this->curlHandler);
        $this->errorMessage = curl_error($this->curlHandler);
        if ($this->errorCode) {
            throw new QueryFailureException(
                "Query Error : {$this->errorMessage}",
                $this->errorCode
            );
        }

        return $this;
    }

    public function results(): string
    {
        return $this->results ?? '';
    }

    public function errorCode()
    {
        return $this->errorCode;
    }

    protected function addSSL(): void
    {
        if ($this->sslPath !== null) {
            curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($this->curlHandler, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt(
                $this->curlHandler,
                CURLOPT_CAINFO,
                __DIR__ . '/cert/cacert.pem'
            );
            curl_setopt(
                $this->curlHandler,
                CURLOPT_CAPATH,
                __DIR__ . '/cert/cacert.pem'
            );
        }
    }
}
