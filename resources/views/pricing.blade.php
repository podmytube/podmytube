@extends('layouts.app')


@section('content')

<div class="markdown max-w-screen-lg mx-auto px-8 text-xl py-16">
    
    @include('partials.pricing_header')
    
    <div class="mt-12 block lg:flex lg:items-center lg:mt-16">
        <div class="bg-white shadow rounded-lg mt-2 max-w-xs mx-auto lg:max-w-full lg:rounded-lg lg:min-w-0 lg:w-1/3 lg:mt-0 overflow-hidden">
            @include ('partials.pricing_single_plan', 
            [
                'plan_title' => 'starter', 
                'plan_monthly_price'=> 9, 
                'plan_features'=> ['1 video by week'],
                'most_popular' => false,
            ])
        </div>
        <div class="bg-white shadow rounded-lg mt-2 max-w-md mx-auto lg:mx-1 lg:min-w-0 lg:w-1/3 relative lg:mt-0 overflow-hidden">
            @include ('partials.pricing_single_plan', 
                [
                    'plan_title' => 'Professionnal', 
                    'plan_monthly_price'=> 29, 
                    'plan_features'=> ['3 videos by week'],
                    'most_popular' => true,
                ])
        </div>
        <div class="bg-white shadow rounded-lg mt-2 max-w-xs mx-auto lg:max-w-full lg:rounded-lg lg:min-w-0 lg:w-1/3 lg:mt-0 overflow-hidden">
            @include ('partials.pricing_single_plan', 
                [
                    'plan_title' => 'Business', 
                    'plan_monthly_price'=> 79, 
                    'plan_features'=> ['1 video by day'],
                    'most_popular' => false,
                ])
        </div>
    </div>

</div>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function(event) {
        const MONTHLY=0;
        const YEARLY=1;
        const PER_YEAR_LABEL='/yr';
        const PER_MONTH_LABEL='/mo';
        var monthlyButton=document.getElementById("monthly-button");
        var yearlyButton=document.getElementById("yearly-button");
        monthlyButton.addEventListener("click", monthlyPricing);
        yearlyButton.addEventListener("click", yearlyPricing);

        function monthlyPricing(){
            activate(monthlyButton);
            deactivate(yearlyButton);
            changeLabels(MONTHLY);
            changePrices(MONTHLY);
        }

        function yearlyPricing(){
            activate(yearlyButton);
            deactivate(monthlyButton);
            changeLabels(YEARLY);
            changePrices(YEARLY);
        }

        function activate(element){
            addClass(element, "bg-gray-700");
            addClass(element, "text-white");
            removeClass(element, "text-gray-700");
        }

        function deactivate(element){
            removeClass(element, "bg-gray-700");
            removeClass(element, "text-white");
            addClass(element, "text-gray-700");
        }

        function changeLabels(periodActivated){
            var label = PER_MONTH_LABEL;
            if (periodActivated==YEARLY) {
                label = PER_YEAR_LABEL;
            }
            var labelsToChange = document.getElementsByClassName("billing-period");
            for (index = 0; index < labelsToChange.length; ++index) {
                labelsToChange[index].innerHTML=label;
            }
        }

        function changePrices(periodActivated){
            var pricesToChange = document.getElementsByClassName("billing-price");
            for (index = 0; index < pricesToChange.length; ++index) {
                price = parseInt(pricesToChange[index].innerHTML);
                var newPrice=0;
                if (periodActivated==YEARLY) {
                    newPrice=price*10;
                } else {
                    newPrice=price/10;
                }
                pricesToChange[index].innerHTML=newPrice.toString();
            }
            return true;
        }

        function hasClass(element,className) {
            return !!element.className.match(new RegExp('(\\s|^)'+className+'(\\s|$)'));
        }

        function addClass(element,className) { 
            if (!hasClass(element,className)) element.className += " "+className;
        }

        function removeClass(element,className) {
            if (hasClass(element,className)) {
                var reg = new RegExp('(\\s|^)'+className+'(\\s|$)');
                element.className=element.className.replace(reg,' ');
            }
        }
    });
</script>




@endsection