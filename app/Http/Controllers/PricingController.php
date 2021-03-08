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
                    ['desc' => '<span class="text-2xl font-extrabold">33</span> episodes / month', 'value' => true],
                    ['desc' => '<span class="text-2xl font-extrabold">30</span> are included from the start', 'value' => true],
                    ['desc' => 'Live customer support', 'value' => true],
                    ['desc' => 'Secure feed & no tracking', 'value' => true],
                    ['desc' => 'All episodes kept', 'value' => true],
                    ['desc' => 'Add exclusive content', 'value' => true],
                ],
            ],
            'Professionnal' => [
                'title' => 'Professionnal',
                'monthly_price' => 29,
                'features' => [
                    ['desc' => '<span class="text-2xl font-extrabold">12</span> episodes / month', 'value' => true],
                    ['desc' => '<span class="text-2xl font-extrabold">12</span> are included from the start', 'value' => true],
                    ['desc' => 'Live customer support', 'value' => true],
                    ['desc' => 'Secure feed & no tracking', 'value' => true],
                    ['desc' => 'All episodes kept', 'value' => true],
                    ['desc' => 'Add exclusive content', 'value' => true],
                ],
            ],
            'Starter' => [
                'title' => 'Starter',
                'monthly_price' => 9,
                'features' => [
                    ['desc' => '<span class="text-2xl font-extrabold">5</span> episodes / month', 'value' => true],
                    ['desc' => '<span class="text-2xl font-extrabold">5</span> are included from the start', 'value' => true],
                    ['desc' => 'Live customer support', 'value' => true],
                    ['desc' => 'Secure feed & no tracking', 'value' => true],
                    ['desc' => 'All episodes kept', 'value' => true],
                    ['desc' => 'Add exclusive content', 'value' => true],
                ],
            ],
            'Free' => [
                'title' => 'Free',
                'monthly_price' => 0,
                'features' => [
                    ['desc' => 'Only <span class="text-2xl font-extrabold">1</span> episode / month', 'value' => true],
                    ['desc' => 'Only <span class="text-2xl font-extrabold">1</span> episode is included at start', 'value' => true],
                    ['desc' => 'Email customer support', 'value' => true],
                    ['desc' => 'Secure feed & no tracking', 'value' => true],
                    ['desc' => 'Only the 3 last', 'value' => false],
                    ['desc' => 'Add exclusive content', 'value' => false],
                ],
            ],
        ];
        return view('pricing', compact('plans'));
    }
}
