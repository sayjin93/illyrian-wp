/*  Remove ad_div if it is not gallery post  */
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
        var myPosition = jQuery(selector).offset();

        var myPositionTop = myPosition.top - Math.floor(Math.random() * 125);
        var myPositionLeft = myPosition.left - Math.floor(Math.random() * 150);

        jQuery('.illyrian_div').attr('style', 'display: block; opacity: ' + opacity + '; top:' + myPositionTop + 'px;left:' + myPositionLeft + 'px;');
    }, 1000);
}