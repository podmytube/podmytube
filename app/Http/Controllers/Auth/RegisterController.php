<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Mail\WelcomeToPodmytube;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Log;

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

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

     /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $data_to_valid = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ];

        /**
         * if in dev mode there's no captcha displayed
         */
        if (env('APP_ENV' != 'dev')) {
            $data_to_valid['g-recaptcha-response'] = 'required|captcha';
        }

        return Validator::make($data, $data_to_valid);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        session()->flash('message', 'Account successfully updated !');
        session()->flash('alert-class', 'alert-success');

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        
        Mail::to($user)->send(new WelcomeToPodmytube($user));

        return Auth::attempt(['email' => $data['email'], 'password' => $data['password']]);        
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /*
    public function register(Request $request)
    {
        Log::info(__CLASS__.'::'.__METHOD__.' before validator');

        $this->validator($request->all())->validate();

        Log::info(__CLASS__.'::'.__METHOD__.' before registered');
        event(new Registered($user = $this->create($request->all())));

        Log::info(__CLASS__.'::'.__METHOD__.' before guard login');
        $this->guard()->login($user);

        Log::info(__CLASS__.'::'.__METHOD__.' before redirect');
        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }
    */

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
/*
    protected function registered(Request $request, $user)
    {
        if (Auth::loginUsingId($user->id)) {
            Log::info(__CLASS__.'::'.__METHOD__.' logging ok');
            return TRUE;
        } else {
            Log::info(__CLASS__.'::'.__METHOD__.' logging ko');
            return FALSE;
        }

    }
    */

}
