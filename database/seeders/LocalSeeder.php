<?php

declare(strict_types=1);

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LocalSeeder extends Seeder
{
    public const JEANVIET_CHANNEL_ID = 'UCu0tUATmSnMMCbCRRYXmVlQ';
    public const FTYTECA_CHANNEL_ID = 'UCw6bU9JT_Lihb2pbtqAUGQw';

    /**
     * Run the database seeds.
     */
    public function __construct()
    {
        if (App::environment('production')) {
            throw new Exception('This seeder ' . static::class . ' should only be run on local/testing environment.');
        }
    }

    protected function truncateTables(string|array $tables): void
    {
        $tables = Arr::wrap($tables);

        Schema::disableForeignKeyConstraints();
        array_map(fn (string $tableName) => DB::table($tableName)->truncate(), $tables);
        Schema::enableForeignKeyConstraints();
    }
}
