function set_background( imagelink ) {
    $('#coupon_background_image').css('background-image', 'url(' + imagelink + ')');
}

function set_background_active( background_id ) {
    $('.dc_background').removeClass('active');
    $('#dc_background_' + background_id).addClass('active');
    $('#input_background_image_id').val(background_id);
}

function set_amount_active( amount_id ) {
    $('.dc_amount').removeClass('active');
    $('#dc_amount_' + amount_id).addClass('active');
    $('#input_amount_id').val(amount_id);
    $('#input_coupon_amount').val('');
}

jQuery.fn.borderFlash = function ( color, duration, times ) {
    var current = this.css('border-color'),
        i = 1;
    do {
        this.animate({
                         borderTopColor   : 'rgb(' + color + ')',
                         borderLeftColor  : 'rgb(' + color + ')',
                         borderBottomColor: 'rgb(' + color + ')',
                         borderRightColor : 'rgb(' + color + ')'
                     }, duration / 2);
        this.animate({
                         borderTopColor   : current,
                         borderLeftColor  : current,
                         borderBottomColor: current,
                         borderRightColor : current
                     }, duration / 2);
        i++;
    } while (i <= times)
}

jQuery.fn.Flash = function ( color, duration, times ) {
    var current = this.css('background-color'),
        i = 1;
    do {
        this.animate({backgroundColor: 'rgb(' + color + ')'}, duration / 2);
        this.animate({backgroundColor: current}, duration / 2);
        i++;
    } while (i <= times)
}


$(document).ready(function () {
    $('#dc_shipping_option1').bind('click', function () {
        toggleOn('dc_shipping_option_inputs1');
        toggleOff('dc_shipping_option_inputs0');
    });
    $('#dc_shipping_option0').bind('click', function () {
        toggleOn('dc_shipping_option_inputs0');
        toggleOff('dc_shipping_option_inputs1');
    });

    $('#dc_message #message').on('keyup', function ( e ) {
        if (e.keyCode !== 8 && e.keyCode !== 46) {
            var text = $(this).val()
            textLen = text.length,
                totalLength = 0,
                newText = '';
            textCopy = text.replace(/\n/g, '~');
            ;
            console.log(textCopy);
            for (var i = 0, len = textCopy.length; i < len; i++) {
                if (textCopy[i] == '~') {
                    totalLength = totalLength + 85;
                } else {
                    totalLength++;
                }
                console.log("Total Length" + totalLength);
            }
            if (totalLength > 1000) {
                totalLength = 0;
                for (var i = 0, len = textCopy.length; (i < len) && (totalLength <= 1000); i++) {
                    if (textCopy[i] == '~') {
                        totalLength = totalLength + 85;
                        newText = newText + '\n';
                    } else {
                        totalLength++;
                        newText = newText + textCopy[i];
                    }
                }
                $('#dc_message #message').val(newText);
            }
        }
    });
});
