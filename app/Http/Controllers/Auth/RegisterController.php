<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    public const SUCCESS_MESSAGE = 'Thank you for subscribing !';

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showRegistrationForm(Request $request)
    {
        $referralCode = $request->query('referral_code') ?? null;

        return view('auth.register', compact('referralCode'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $dataToValid = [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'terms' => 'required|boolean',
        ];

        // if in dev mode there's no captcha displayed
        if (App::environment('production')) {
            $dataToValid['g-recaptcha-response'] = 'required|captcha';
        }

        return Validator::make($data, $dataToValid);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data): User
    {
        $referrer = null;

        if (Arr::get($data, 'referral_code') !== null) {
            $referrer = User::byReferralCode(Arr::get($data, 'referral_code'));
        }

        return User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'referral_code' => User::createReferralCode(),
            'referrer_id' => $referrer !== null ? $referrer->id : null,
        ]);
    }

    /**
     * The user has been registered.
     *
     * @param mixed $user
     *
     * @return mixed
     */
    protected function registered(Request $request)
    {
        $request->session()->flash('success', self::SUCCESS_MESSAGE);
    }
}
