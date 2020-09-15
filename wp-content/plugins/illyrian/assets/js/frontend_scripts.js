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

/*  Change position of ad   */
function PositionAd(selector, opacity) {
    setTimeout(function () {
        let myPosition = jQuery(selector).offset();

        let myPositionTop = myPosition.top - Math.floor(Math.random() * 125);
        let myPositionLeft = myPosition.left - Math.floor(Math.random() * 150);

        jQuery('.illyrian_div').attr('style', 'display: block; opacity: ' + opacity + '; top:' + myPositionTop + 'px;left:' + myPositionLeft + 'px;');
    }, 1000);
}
