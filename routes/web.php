<?php
/**
* routes for PodMyTube web application (for now its dashboard)
* 
* @package PodMyTube Dashboard
* @author Frederick Tyteca <fred@podmytube.com>
*/

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/**
 * Route to check emails ==========================
 */
use App\Mail\ChannelIsRegistered;
if (env('APP_ENV') != 'prod') {
	Route::get('/mailable', function () {
		$user = App\User::find(1);
		$channel = $user->channels->first();
		return new App\Mail\ChannelIsRegistered($user, $channel);
	});

	Route::get('/sendmail', function () {
		$user = App\User::find(1);
		$channel = $user->channels->first();
		Mail::to($user)->send(new ChannelIsRegistered($user, $channel));
	});
}
// ================================================

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

Route::get('setlocale/{locale}', function ($locale) {
	if (in_array($locale, \Config::get('app.locales'))) {
		Session::put('locale', $locale);
	}
	return redirect()->back();
});

Route::get('/', 'HomeController@index')->name('root');

Route::post('/channel/', 'ChannelCreateController@store')->name('channel.store'); 
Route::get('/channel/create', 'ChannelCreateController@create')->name('channel.create'); 

Route::get('/channel/', 'ChannelsController@index')->name('channel.index'); 
Route::get('/channel/{channel}', 'ChannelsController@show')->name('channel.show'); 
Route::get('/channel/{channel}/edit', 'ChannelsController@edit')->name('channel.edit'); 
Route::patch('/channel/{channel}', 'ChannelsController@update'); 

Route::get('/change-password', 'Auth\UpdatePasswordController@index')->name('password.form');
Route::post('/change-password', 'Auth\UpdatePasswordController@update')->name('password.update');

/**
 * MediasStats
 */
Route::get('/channel/{channel}/medias_downloads', 'MediasStatsController@index')->name('medias_stats.index');

/**
 * AppStats
 */
Route::get('/channel/{channel}/app_stats', 'AppStatsController@index')->name('app_stats.index');

/**
 * Plans
 */
Route::get('/plans/{channel}', 'PlansController@index')->name('plans.index');
Route::get('/success', 'SubscriptionResultController@success');
Route::get('/canceled', 'SubscriptionResultController@failure');

Route::post('stripewebhook', 'StripeWebhookController@receive');

/**
 * Subscription
 */
Route::post('/subscribe', 'SubscribeController@store');


/**
 * Thumb
 */
Route::get('/channel/{channel}/thumbs', 'ThumbsController@index')->name('channel.thumbs.index');
Route::get('/channel/{channel}/thumbs/edit', 'ThumbsController@edit')->name('channel.thumbs.edit');
Route::get('/channel/{channel}/thumbs/create', 'ThumbsController@create')->name('channel.thumbs.create');
Route::post('/channel/{channel}/thumbs', 'ThumbsController@store')->name('channel.thumbs.store');
Route::patch('/channel/{channel}/thumbs', 'ThumbsController@update')->name('channel.thumbs.update');
Route::delete('/channel/{channel}/thumbs', 'ThumbsController@delete')->name('channel.thumbs.delete');


/**
 * User
 */
Route::get('/user/', 'UsersController@show')->name('user.index'); 
Route::get('/user/show', 'UsersController@show')->name('user.show'); 
Route::get('/user/edit', 'UsersController@edit')->name('user.edit'); 
Route::patch('/user/', 'UsersController@update')->name('user.patch'); 
