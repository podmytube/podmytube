document.addEventListener("DOMContentLoaded", function (event) {

    const MONTHLY = 0;
    const YEARLY = 1;
    const PER_YEAR_LABEL = '/yr';
    const PER_MONTH_LABEL = '/mo';

    var currentPeriod = MONTHLY;
    var withYearlyPrices = document.getElementById("withYearlyPrices");
    var monthlyButton = document.getElementById("monthly-button");
    var yearlyButton = document.getElementById("yearly-button");
    if (monthlyButton && yearlyButton && withYearlyPrices) {
        monthlyButton.addEventListener("click", monthlyPricing);
        yearlyButton.addEventListener("click", yearlyPricing);
    }

    function hasClass(element, className) {
        return !!element.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'));
    }

    function addClass(element, className) {
        if (!hasClass(element, className)) element.className += " " + className;
    }

    function removeClass(element, className) {
        if (hasClass(element, className)) {
            var reg = new RegExp('(\\s|^)' + className + '(\\s|$)');
            element.className = element.className.replace(reg, ' ');
        }
    }

    function monthlyPricing() {
        activate(monthlyButton);
        deactivate(yearlyButton);
        changeLabels(MONTHLY);
        if (currentPeriod != MONTHLY) {
            changePrices(MONTHLY);
            currentPeriod = MONTHLY;
        }
    }

    function yearlyPricing() {
        activate(yearlyButton);
        deactivate(monthlyButton);
        changeLabels(YEARLY);
        if (currentPeriod != YEARLY) {
            changePrices(YEARLY);
            currentPeriod = YEARLY;
        }
    }

    function activate(element) {
        addClass(element, "bg-gray-700");
        addClass(element, "text-white");
        removeClass(element, "text-gray-700");
    }

    function deactivate(element) {
        removeClass(element, "bg-gray-700");
        removeClass(element, "text-white");
        addClass(element, "text-gray-700");
    }

    function changeLabels(periodActivated) {
        var label = PER_MONTH_LABEL;
        if (periodActivated == YEARLY) {
            label = PER_YEAR_LABEL;
        }
        var labelsToChange = document.getElementsByClassName("billing-period");
        for (index = 0; index < labelsToChange.length; ++index) {
            labelsToChange[index].innerHTML = label;
        }
    }

    function changePrices(periodActivated) {
        var pricesToChange = document.getElementsByClassName("billing-price");
        for (index = 0; index < pricesToChange.length; ++index) {
            price = parseInt(pricesToChange[index].innerHTML);
            var newPrice = 0;
            if (periodActivated == YEARLY) {
                newPrice = price * 10;
            } else {
                newPrice = price / 10;
            }
            pricesToChange[index].innerHTML = newPrice.toString();
        }
        return true;
    }
});