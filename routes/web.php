<?php

/**
 * routes for PodMyTube web application (for now its dashboard)
 *
 * @package PodMyTube Dashboard
 * @author Frederick Tyteca <fred@podmytube.com>
 */

// ================================================
// Home page is the login screen
Route::get('/', function () {
    return view("auth.login");
})->name('root');
Route::get('terms', function () {
    return view('terms');
})->name('terms');
Route::get('privacy', function () {
    return view('privacy');
})->name('privacy');

// patie-stripe-webhooks
Route::stripeWebhooks('/stripe/capture');

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

    /* Route::get('/channel/', 'ChannelsController@index')->name('channel.index');
Route::get('/channel/{channel}', 'ChannelsController@show')->name('channel.show')->middleware(\App\Http\Middleware\UserMustOwnChannel::class);
Route::get('/channel/{channel}/edit', 'ChannelsController@edit')->name('channel.edit');
Route::patch('/channel/{channel}', 'ChannelsController@update'); */

    Route::resource('channel', 'ChannelsController')->only([
        'index',
        'show',
        'edit',
        'update',
    ]);

    Route::get('/change-password', 'Auth\UpdatePasswordController@index')->name(
        'password.form'
    );
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

    //Route::post('stripewebhook', 'StripeWebhookController@receive');

    /**
     * Subscription
     */
    Route::post('/subscribe', 'SubscribeController@store');

    /**
     * Medias
     */
    Route::resources([
        'channel.medias' => 'MediasController',
    ]);

    /**
     * Thumb
     */
    Route::get('/channel/{channel}/thumbs', 'ThumbsController@index')->name(
        'channel.thumbs.index'
    );
    Route::get('/channel/{channel}/thumbs/edit', 'ThumbsController@edit')->name(
        'channel.thumbs.edit'
    );
    Route::post('/channel/{channel}/thumbs', 'ThumbsController@store')->name(
        'channel.thumbs.store'
    );
    Route::patch('/channel/{channel}/thumbs', 'ThumbsController@update')->name(
        'channel.thumbs.update'
    );

    /**
     * User
     */
    Route::get('/user/', 'UsersController@show')->name('user.index');
    Route::get('/user/show', 'UsersController@show')->name('user.show');
    Route::get('/user/edit', 'UsersController@edit')->name('user.edit');
    Route::patch('/user/', 'UsersController@update')->name('user.patch');
});
