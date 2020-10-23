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