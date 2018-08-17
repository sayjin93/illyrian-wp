/*  Remove jkdiv if it is not gallery post  */
/**
 * @return {boolean}
 */
function RemoveIfNotGallery(selector) {
    if (jQuery(selector).length < 1) {
        jQuery('.illyrian_div').remove();
        return true;
    }
}

/*  Add cookie when mouse over div  */
function PlaceCookieClicked(time) {
    jQuery(window).blur(function () {
        createHourCookie('clicked_ad', 'clicked', time);
        setTimeout(function () {
            jQuery('.illyrian_div').remove();
        }, 2000);
    });
}

/*  Change position of ad   */
function PositionAd(selector, opacity) {
    setTimeout(function () {
        var myposition = jQuery(selector).offset();

        var mypositionTop = myposition.top - Math.floor(Math.random() * 125);
        var mypositionLeft = myposition.left - Math.floor(Math.random() * 150);

        jQuery('.illyrian_div').attr('style', 'display: block; opacity: ' + opacity + '; top:' + mypositionTop + 'px;left:' + mypositionLeft + 'px;');
    }, 1000);
}