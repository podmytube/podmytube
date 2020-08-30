<?php

/**
 * routes for PodMyTube web application (for now its dashboard).
 *
 * @package PodMyTube Dashboard
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

Route::get('terms', function () {
    return view('terms');
})->name('terms');
Route::get('privacy', function () {
    return view('privacy');
})->name('privacy');

Route::domain(env('WWW_DOMAIN'))->group(function () {
    Route::get('/', 'IndexController@index')->name('index');
});

Route::domain(env('APP_DOMAIN'))->group(function () {
    // ================================================
    // Dash homepage is the login screen
    Route::get('/', function () {
        return view('auth.login');
    })->name('root');

    /**
     * not a user interaction
     */
    Route::stripeWebhooks('/stripe/webhooks');
    Auth::routes();

    Route::middleware(['auth'])->group(function () {
        Route::get('/home', 'HomeController@index')->name('home');

        Route::get('setlocale/{locale}', function ($locale) {
            if (in_array($locale, \Config::get('app.locales'))) {
                Session::put('locale', $locale);
            }
            return redirect()->back();
        });

        Route::post('/channel/', 'ChannelCreateController@store')->name(
            'channel.store'
        );
        Route::get('/channel/create', 'ChannelCreateController@create')->name(
            'channel.create'
        );

        Route::resource('channel', 'ChannelsController')->only([
            'index',
            'show',
            'edit',
            'update',
        ]);

        Route::get(
            '/change-password',
            'Auth\UpdatePasswordController@index'
        )->name('password.form');

        Route::post(
            '/change-password',
            'Auth\UpdatePasswordController@update'
        )->name('password.update');

        /**
         * Plans
         */
        Route::get('/plans/{channel}', 'PlansController@index')->name(
            'plans.index'
        );
        Route::get('/success', 'SubscriptionResultController@success');
        Route::get('/canceled', 'SubscriptionResultController@failure');

        /**
         * Subscription
         */
        Route::post('/subscribe', 'SubscribeController@store');

        /**
         * Medias
         */
        Route::resource('channel.medias', 'MediasController')->only(['index']);

        /**
         * Thumb
         */
        Route::get('/channel/{channel}/thumbs', 'ThumbsController@index')->name(
            'channel.thumbs.index'
        );
        Route::get(
            '/channel/{channel}/thumbs/edit',
            'ThumbsController@edit'
        )->name('channel.thumbs.edit');
        Route::post(
            '/channel/{channel}/thumbs',
            'ThumbsController@store'
        )->name('channel.thumbs.store');
        Route::patch(
            '/channel/{channel}/thumbs',
            'ThumbsController@update'
        )->name('channel.thumbs.update');

        /**
         * User profile
         */
        Route::resource('user', 'UsersController')->only([
            'show',
            'edit',
            'update',
        ]);
    });
});
