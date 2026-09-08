$(document).ready(function () {

    //Globale Variablen
    var company = $('#company').data('company'),
        shopCode = $('#shop_code').data('shop-code'),
        langCode = $('#lang_code').data('lang-code'),
        itemSrc = $('#item_src').data('item-src');
    if (typeof itemSrc == 'undefined' || !(itemSrc.len > 0)) {
        itemSrc = shopCode;
    }

    //Auto-formsend
    var visibleInputs = $('input[type!=hidden]:visible'),
        inputForm,
        currInput,
        lastInputInForm;
    $(visibleInputs).each(function () {
        currInput = $(this);
        inputForm = currInput.parents('form');
        formID = $(inputForm).attr('id');
        if (formID !== 'form_itemcard') {
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
        }
    });

    //Itemlist button
    $('div.itemcard_list a.itemlist_order_button_link').on('click',function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var doSubmit = false;
        form.children('.input_item_quantity_value').each(function() {
           if ($(this).value() > 0) {
               doSubmit = true;
           }
        });
        if (doSubmit) {
            form.submit();
        }
    });

    /*
     var formEls = $('form'),
     thisForm,
     finalInput;
     $(formEls).each(function(){
     thisForm = $(this);
     finalInput = $(thisForm).find('input[type!=hidden]').last();
     $(finalInput).on('keyup',function(e) {
     if(e.which === 13){
     $(thisForm).submit();
     return false;
     }
     });
     });
     */

    //Toggle variants
    $('.hasvar td:first-child').on('click', function () {
        var parentTr = $(this).parent('.hasvar'),
            itemNo = String($(parentTr).data('itemno')),
            varTrs;
        if (typeof itemNo !== 'undefined' && itemNo.length > 0) {
            varTrs = $('.isvar_' + itemNo);
            varTrscol = $('.iscolorvar_' + itemNo);
            if (typeof varTrs !== 'undefined' && varTrs.length > 0) {
                $(varTrs).each(function () {
                    if ($(parentTr).hasClass('vars_open')) {
                        $(this).hide().removeClass('open_var').addClass('closed_var');
                    } else {
                        $(this).show().removeClass('closed_var').addClass('open_var');
                    }
                });
                $(varTrscol).each(function () {
                    if ($(parentTr).hasClass('vars_open')) {
                        parentTr.find('td:first-child').find('span:first-child').removeClass('fa-minus').addClass('fa-plus');
                        $(this).hide().removeClass('open_var').addClass('closed_var');
                    } else {
                        $(this).show().removeClass('closed_var').addClass('open_var');
                        parentTr.find('td:first-child').find('span:first-child').removeClass('fa-plus').addClass('fa-minus');
                    }
                });

                if ($(parentTr).hasClass('vars_open')) {
                    $(parentTr).removeClass('vars_open').addClass('vars_closed');
                } else {
                    $(parentTr).removeClass('vars_closed').addClass('vars_open');
                }

            }
        }
    });

    var queryTime;
    //Direktbestellung
    $('div.direct_order_field #input_item_no').on('keyup', function ( e ) {
        var itemNo = $(this).val();
        if (typeof queryTimeout !== 'undefined') {
            window.clearTimeout(queryTimeout);
        }
        if (e.keyCode != 13 && e.keyCode != 40 && e.keyCode != 38) {
            queryTimeout = window.setTimeout(function () {
                if (itemNo.length > 3) {
                    $.post("/module/dcshop/b2b/get_variants.php", {
                        company      : company,
                        shop_code    : itemSrc,
                        language_code: langCode,
                        item_no      : itemNo
                    }).done(function ( data ) {
                        if (typeof data !== 'undefined' && data.length > 0 && /option/.test(data)) {
                            $('#input_var_code').removeProp('disabled').empty().append(data)
                        }
                    });
                }
            }, 300);
        }
    });
    $('div.direct_order_field #input_item_no').on('autocompleteselct', function () {
        var itemNo = $(this).val();
        if (typeof queryTimeout !== 'undefined') {
            window.clearTimeout(queryTimeout);
        }
        queryTimeout = window.setTimeout(function () {
            if (itemNo.length > 3) {
                $.post("/module/dcshop/b2b/get_variants.php", {
                    company      : company,
                    shop_code    : itemSrc,
                    language_code: langCode,
                    item_no      : itemNo
                }).done(function ( data ) {
                    if (typeof data !== 'undefined' && data.length > 0 && /option/.test(data)) {
                        $('#input_var_code').removeProp('disabled').empty().append(data)
                    }
                });
            }
        }, 300);
    });

    //Doppelklick in Kundenkonto-listen:
    var forms = $('#account_body form').has('table.linklist');
    $(forms).each(function () {
        var currForm = this,
            formid = $(currForm).attr('id'),
            formtrs = $(currForm).find('tr'),
            formtrs = $(currForm).find('table.linklist tr'),
            formbutton = $(currForm).find('a.button_edit'),
            clickPropText = $(formbutton).prop('onclick').toString(),
            buttonAction = clickPropText.match(/action\=(.*)&/i)[1],
            buttonActionid = clickPropText.match(/action_id\=(.*)'/i)[1],
            formaction = '/account/?action=' + buttonAction + '&action_id=' + buttonActionid;

        $(formtrs).each(function () {
            $(this).on('dblclick', function () {
                document.forms[formid].action = formaction;
                document.forms[formid].submit();
                return false;
            });
        });
    });

    //$( document ).tooltip();
    //$('*[title]').qtip({ style: { name: 'dark', tip: true } })

});

function submitenter( myfield, e ) {
    var keycode;
    if (window.event) {
        keycode = window.event.keyCode;
    } else if (e) {
        keycode = e.which;
    } else {
        return true;
    }

    if (keycode == 13) {
        myfield.form.submit();
        return false;
    }
    else {
        return true;
    }
}

