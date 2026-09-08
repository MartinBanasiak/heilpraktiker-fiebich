$(function () {
    //Auto-formsend
    var visibleInputs = $('input[type!=hidden]:visible'),
        inputForm,
        currInput,
        lastInputInForm;
    $(visibleInputs).each(function () {
        currInput = $(this);
        inputForm = currInput.parents('form');
        lastInputInForm = $(inputForm).find('input[type!=hidden]:visible').last();
        lastTextInputInForm = $(inputForm).find('input[type!=checkbox]:visible').last();
        if ((currInput[0] == lastInputInForm[0]) || (currInput[0] == lastTextInputInForm[0])) {
            $(currInput).on('keyup', function ( e ) {
                if (e.which === 13) {
                    $(this).parents('form').submit();
                    return false;
                }
            });
        }
    });
});