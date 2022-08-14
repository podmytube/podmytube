<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Channel;
use App\Models\Playlist;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        if (App::environment('production')) {
            URL::forceScheme('https');
        }

        /*
         * those morphedNames are used in polymorphic relation (DB)
         * you can retrieve morphedName with $coverable->morphedName()
         * check App\Traits\hasCover
         * $channel->morphedName() => 'morphedChannel'
         * $playlist->morphedName() => 'morphedPlaylist'
         */

        Relation::enforceMorphMap([
            'morphedChannel' => Channel::class,
            'morphedPlaylist' => Playlist::class,
        ]);

        /*
        Collection::macro('recursive', function () {
            return $this->map(function ($value) {
                if (is_array($value) || is_object($value)) {
                    return collect($value)->recursive();
                }

                return $value;
            });
        });

        if (env('APP_DEBUG')) {
            DB::listen(function ($query) {
                File::append(
                    storage_path('/logs/query.log'),
                    $query->sql .
                        ' [' .
                        implode(', ', $query->bindings) .
                        ']' .
                        PHP_EOL
                );
            });
        } */
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (!App::environment('production')) {
            $this->app->register(
                \Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class
            );
        }
    }
}
