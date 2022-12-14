<?php

declare(strict_types=1);

/**
 * routes for PodMyTube web application (for now its dashboard).
 *
 * @author Frederick Tyteca <fred@podmytube.com>
 */

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ThumbsController;
use App\Jobs\SendVerificationEmailJob;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('test', function () {
    return view('test');
})->name('terms');

Route::get('terms', function () {
    return view('terms');
})->name('terms');

Route::get('privacy', function () {
    return view('privacy');
})->name('privacy');

Route::domain('cockpit.' . config('app.domain'))->group(function (): void {
    Route::get('/', 'CockpitController@index')->name('cockpit.index');
});

// =======================================
//         !! Stripe Route !!
// =======================================
Route::stripeWebhooks('/stripe/webhooks');

// =======================================
//             public part
// =======================================
Route::domain('www.' . config('app.domain'))->group(function (): void {
    Route::get('/', 'IndexController@index')->name('www.index');

    Route::get('pricing', 'PricingController@index')->name('pricing');

    Route::get('faq', function () {
        return view('faq');
    })->name('faq');

    Route::get('about', function () {
        return view('about');
    })->name('about');

    Route::get('thumb', function () {
        return view('thumb');
    });

    Route::resource('post', 'PostController')->only(['index', 'show']);

    Route::get('test', function () {
        return view('test');
    })->name('test');
});

// =======================================
//          authenticated part
// =======================================
Route::domain('dashboard.' . config('app.domain'))->group(function (): void {
    // ================================================
    // user registration and login
    Auth::routes();

    // ================================================
    // Dash homepage is the login screen
    Route::get('/', fn () => view('auth.login'))
        ->name('root')
    ;

    Route::middleware(['auth'])->group(function (): void {
        Route::get('/home', 'HomeController@index')->name('home');

        // ================================================
        // registering channel
        Route::get('/create', 'ChannelCreateController@step1')->name('channel.step1');
        Route::post('/create', 'ChannelCreateController@step1Validate')->name('channel.step1.validate');
        Route::get('/create/{channel}/step2', 'ChannelCreateController@step2')->name('channel.step2');
        Route::post('/create/{channel}/step2', 'ChannelCreateController@step2Validate')->name('channel.step2.validate');

        Route::resource('channel', 'ChannelsController')
            ->only(['show', 'edit', 'update', 'destroy'])
        ;

        Route::get('/change-password', 'Auth\UpdatePasswordController@index')
            ->name('password.form')
        ;

        Route::post('/change-password', 'Auth\UpdatePasswordController@update')
            ->name('password.update')
        ;

        // ================================================
        // Plans
        Route::get('/plans/{channel}', 'PlansController@index')
            ->name('plans.index')
        ;
        Route::get('/success', 'SubscriptionResultController@success');
        Route::get('/canceled', 'SubscriptionResultController@failure');

        // ================================================
        // Subscription
        Route::post('/subscribe', 'SubscribeController@store');

        // ================================================
        // Medias
        Route::patch('/media/{media}/enable', 'MediasController@enable')->name('media.enable');
        Route::patch('/media/{media}/disable', 'MediasController@disable')->name('media.disable');
        Route::resource('channel.medias', 'MediasController')
            ->only(['index', 'create', 'edit', 'store', 'update'])
        ;

        // ================================================
        // Playlist
        Route::resource('playlist', 'PlaylistController')
            ->only(['edit', 'update'])
        ;

        // ================================================
        // Cover
        Route::get('channel/{channel}/cover/edit', [ThumbsController::class, 'channelCoverEdit'])->name('channel.cover.edit');
        Route::patch('channel/{channel}/cover/update', [ThumbsController::class, 'channelCoverUpdate'])->name('channel.cover.update');
        Route::get('playlist/{playlist}/cover/edit', [ThumbsController::class, 'playlistCoverEdit'])->name('playlist.cover.edit');
        Route::patch('playlist/{playlist}/cover/update', [ThumbsController::class, 'playlistCoverUpdate'])->name('playlist.cover.update');

        // ================================================
        // User profile
        Route::resource('user', 'UserController')->only(['index', 'update', 'destroy']);

        Route::get('analytics/{channel}', [AnalyticsController::class, 'show'])->name('analytics');

        // ================================================
        // Impersonate
        Route::get('/{user}/impersonate', 'UsersController@impersonate')->name('users.impersonate');
        Route::get('/leave-impersonate', 'UsersController@leaveImpersonate')->name('users.leave-impersonate');
        Route::resource('users', 'UsersController')->only(['index']);

        // =======================================
        // User should verify email
        Route::get('/email/verify', fn () => view('auth.verify-email'))
            ->name('verification.notice')
        ;

        // user has verified his/her mail
        Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();

            return redirect()->route('home')->with('success', 'Email has been verified. Thank you. ????');
        })->middleware(['signed'])->name('verification.verify');

        Route::post('/email/verification-notification', function (Request $request) {
            SendVerificationEmailJob::dispatch($request->user());

            session()->put('verification_sent', true);

            return redirect()
                ->route('home')
                ->with('success', 'Verification link has been sent ????.')
            ;
        })->middleware(['throttle:6,1'])->name('verification.send');
    });
});
