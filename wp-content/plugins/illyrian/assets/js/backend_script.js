jQuery(document).ready(function ($) {

    var limitAd1 = $('input[name="limitAd1"]');
    var limitAd2 = $('input[name="limitAd2"]');
    var limitAd3 = $('input[name="limitAd3"]');

    // var codeAd2 = $('textarea[name="codeAd2"]');
    // var codeAd3 = $('textarea[name="codeAd3"]');


    function checkAd2and3() {
        var limitAd1Value = limitAd1.val();

        if (limitAd1Value >= 100) {
            limitAd1.val(100);

            limitAd2.val(0).prop('disabled', true);
            limitAd3.val(0).prop('disabled', true);

            // codeAd2.prop('disabled', true);
            // codeAd3.prop('disabled', true);
        }
        else {
            limitAd2.prop('disabled', false);
            limitAd3.prop('disabled', false);

            // codeAd2.prop('disabled', false);
            // codeAd3.prop('disabled', false);
        }
    }

    function checkAd3() {
        var limitAd1Value = parseInt(limitAd1.val());
        var limitAd2Value = parseInt(limitAd2.val());

        if (isNaN(limitAd1Value)) {
            limitAd1Value = 0;
        }

        if (isNaN(limitAd2Value)) {
            limitAd2Value = 0;
        }

        var total = limitAd1Value + limitAd2Value;

        if (total >= 100) {
            limitAd2.val(100 - limitAd1Value);

            limitAd3.val(0).prop('disabled', true);
            // codeAd3.prop('disabled', true);
        }
        else {
            limitAd3.val(100 - total).prop('disabled', false);
            // codeAd3.prop('disabled', false);
        }
    }

    limitAd1.keyup(function () {
        checkAd2and3();

    });

    limitAd2.keyup(function () {
        checkAd3();
    });

    checkAd2and3();
    checkAd3();

});