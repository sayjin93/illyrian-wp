var $ = jQuery;

$(document).ready(function () {
    $('input[name="limitAd1"]').keyup(function () {

        var limitAd1Value = $('input[name="limitAd1"]').val();
        if (limitAd1Value >= 100) {
            $('input[name="limitAd1"]').val(100);
            $('input[name="limitAd2"]').val('');
            $('input[name="limitAd3"]').val('');
            $('input[name="limitAd2"]').attr('disabled', 'disabled');
            $('input[name="limitAd3"]').attr('disabled', 'disabled');
        }
        else {
            $('input[name="limitAd2"]').removeAttr('disabled');
            $('input[name="limitAd3"]').removeAttr('disabled');
        }
    });

    $('input[name="limitAd2"]').keyup(function () {
        var limitAd1Value = parseInt($('input[name="limitAd1"]').val());
        var limitAd2Value = parseInt($('input[name="limitAd2"]').val());
        if (isNaN(limitAd1Value)) {
            limitAd1Value = 0;
        }

        if (isNaN(limitAd2Value)) {
            limitAd2Value = 0;
        }
        var total = limitAd1Value + limitAd2Value;

        if (total >= 100) {
            $('input[name="limitAd2"]').val(100 - limitAd1Value);
            $('input[name="limitAd3"]').val('');
            $('input[name="limitAd3"]').attr('disabled', 'disabled');
        }
        else {
            $('input[name="limitAd3"]').removeAttr('disabled');
            $('input[name="limitAd3"]').val(100 - total);
        }
    });

});