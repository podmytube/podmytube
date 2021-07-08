<?php

declare(strict_types=1);

namespace App\Factories;

use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class GoogleSpreadsheetFactory
{
    /** @var string */
    protected $spreadsheetId;

    /** @var \Google\Service\Sheets */
    protected $service;

    private function __construct(string $spreadsheetId)
    {
        $client = GoogleFactory::init()->withScope(Sheets::SPREADSHEETS)->client();
        $this->service = new Sheets($client);
        $this->spreadsheetId = $spreadsheetId;
    }

    public static function forSpreadsheetId(string $spreadsheetId)
    {
        return new static($spreadsheetId);
    }

    public function service()
    {
        return $this->service;
    }

    public function getRange(string $range): array
    {
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);

        return $response->getValues();
    }

    public function updateRange(string $range, array $values): int
    {
        $params = ['valueInputOption' => 'RAW'];

        $result = $this->service->spreadsheets_values->update(
            $this->spreadsheetId,
            $range,
            new ValueRange(['values' => $values]),
            $params
        );

        return $result->getUpdatedCells();
    }
}
