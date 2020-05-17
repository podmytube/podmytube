<?php

namespace App\Modules;

use App\Exceptions\InvalidUrlException;
use App\Exceptions\QueryFailureException;

class Query
{
    /** @var string $sslpath */
    protected $sslPath;
    /** @var string $referer */
    protected $referer;
    /** @var string $results */
    protected $results;
    /** @var string $jsonResult */
    protected $jsonResult;
    /** @var int $errorCode */
    protected $errorCode;
    /** @var string $errorMessage */
    protected $errorMessage;
    /** @var string $urlToQuery */
    protected $urlToQuery;
    /** @var curl resource */
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

    protected function addSSL()
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

    public function results()
    {
        return $this->results;
    }

    public function errorCode()
    {
        return $this->errorCode;
    }
}
