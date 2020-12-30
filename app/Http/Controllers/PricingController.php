<?php

namespace App\Http\Controllers;

class PricingController extends Controller
{
    public function index()
    {
        $plans = [
            'Business' => [
                'title' => 'Business',
                'monthly_price' => 79,
                'features' => [
                    '30 episodes / month',
                    'Add exclusive content',
                    'Live customer support',
                    'Episodes are kept unlimited time',
                ],
            ],
            'Professionnal' => [
                'title' => 'Professionnal',
                'monthly_price' => 29,
                'features' => [
                    '12 episodes / month',
                    'All episodes kept',
                    'Add exclusive content',
                    'Live customer support',
                    'Episodes are kept unlimited time',
                ],
            ],
            'Starter' => [
                'title' => 'Starter',
                'monthly_price' => 9,
                'features' => [
                    '5 videos / month',
                    'All episodes kept',
                    'Add exclusive content',
                    'Live customer support',
                    'Episodes are kept unlimited time',
                ],
            ],
            'Free' => [
                'title' => 'Free',
                'monthly_price' => 0,
                'features' => [
                    '1 video / month',
                    'Episodes are kept 3 monthes',
                ],
            ]
        ];
        return view('pricing', compact('plans'));
    }
}
