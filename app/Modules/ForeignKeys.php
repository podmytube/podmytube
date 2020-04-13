<?php

namespace App\Modules;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ForeignKeys
{
    protected $foreignKeys;

    /**
     * This function will return foreign keys.
     *
     * @return Collection list of foreign keys
     */
    private function __construct(string $table, string $database = null)
    {
        if (is_null($database)) {
            $database = env('DB_DATABASE') ?? null;
            if (is_null($database)) {
                throw new ForeignKeysNoDatabaseNameException(
                    'Database name is not set, hard to get schema without'
                );
            }
        }

        $this->foreignKeys = DB::table('INFORMATION_SCHEMA.KEY_COLUMN_USAGE')
            ->select([
                'TABLE_NAME',
                'COLUMN_NAME',
                'CONSTRAINT_NAME',
                'REFERENCED_TABLE_NAME',
                'REFERENCED_COLUMN_NAME',
            ])
            ->where([
                ['REFERENCED_TABLE_SCHEMA', $database],
                ['REFERENCED_TABLE_NAME', $table],
            ])
            ->get();
    }

    /**
     * public constructor.
     *
     * @param string $table the table we want the foreign keys
     * @param string $database the database
     *
     * @return ForeignKeys
     */
    public static function create(string $table, string $database = null)
    {
        return new static($table, $database);
    }

    /**
     * This function will return foreign keys.
     *
     * @return Collection list of foreign keys
     */
    public function get(): Collection
    {
        return $this->foreignKeys;
    }
}
