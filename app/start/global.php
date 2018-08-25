<?php
/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
 */

Log::useDailyFiles(storage_path().'/logs/laravel-'.get_current_user().'.log');
