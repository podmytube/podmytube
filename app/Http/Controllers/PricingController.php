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
                    ['desc' => 'Your podcast begin with <span class="text-2xl font-extrabold">30</span> episodes', 'value' => true],
                    ['desc' => 'Live customer support', 'value' => true],
                    ['desc' => 'Secured feed & no tracking', 'value' => true],
                    ['desc' => 'All episodes kept', 'value' => true],
                    ['desc' => 'Add exclusive content', 'value' => true],
                ],
            ],
            'Professionnal' => [
                'title' => 'Professionnal',
                'monthly_price' => 29,
                'features' => [
                    ['desc' => '<span class="text-2xl font-extrabold">12</span> episodes / month', 'value' => true],
                    ['desc' => 'Your podcast begin with <span class="text-2xl font-extrabold">12</span> episodes', 'value' => true],
                    ['desc' => 'Live customer support', 'value' => true],
                    ['desc' => 'Secured feed & no tracking', 'value' => true],
                    ['desc' => 'All episodes kept', 'value' => true],
                    ['desc' => 'Add exclusive content', 'value' => true],
                ],
            ],
            'Starter' => [
                'title' => 'Starter',
                'monthly_price' => 9,
                'features' => [
                    ['desc' => '<span class="text-2xl font-extrabold">5</span> episodes / month', 'value' => true],
                    ['desc' => 'Your podcast begin with <span class="text-2xl font-extrabold">5</span> episodes', 'value' => true],
                    ['desc' => 'Live customer support', 'value' => true],
                    ['desc' => 'Secured feed & no tracking', 'value' => true],
                    ['desc' => 'All episodes kept', 'value' => true],
                    ['desc' => 'Add exclusive content', 'value' => true],
                ],
            ],
        ];
        return view('pricing', compact('plans'));
    }
}
