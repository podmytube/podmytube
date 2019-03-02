<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionResultController extends Controller
{
    public function success(Request $request) 
    {
        return view('subscription.success');
    }

    public function failure(Request $request) 
    {
        return view('subscription.failure');
    }
}