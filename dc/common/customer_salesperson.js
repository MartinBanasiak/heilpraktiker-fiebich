Number.prototype.formatMoney = function ( c, d, t ) {
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "," : d,
        t = t == undefined ? "." : t,
        s = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

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
            if (typeof varTrs !== 'undefined' && varTrs.length > 0) {
                $(varTrs).each(function () {
                    if ($(parentTr).hasClass('vars_open')) {
                        $(this).hide().removeClass('open_var').addClass('closed_var');
                    } else {
                        $(this).show().removeClass('closed_var').addClass('open_var');
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
    var forms = $('#account_body').find('form').has('table.linklist');
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

    var salespersonDiscountInputs = $('.salesperson_discount input'),
        totalValueSpan = $('table.order_sum tr:first-child>td:first-child').next().find('h3>span'),
        totalValue = parseFloat($('totalValueSpan').text().replace(/[^0-9\,]+/g, ""));
    $(salespersonDiscountInputs).on('change', function () {
        var runningTotal = 0.00;
        $(salespersonDiscountInputs).each(function () {
            var discountValue = parseFloat($(this).val()),
                discountLine = $(this).parents('tr'),
                lineTotalSpan = $(discountLine).find('td.list5_amt>div>span'),
                lineTotalValue = parseFloat($(discountLine).find('td.list5_amt>div').data('initvalue')),
                maxdiscountValue = parseFloat($(this).data('maxdiscount')),
                discountMultiplier = 0.00,
                discountedLineTotal = 0.00,
                formattedDiscountedLineTotal = '',
                fraction = 0.00,
                lineTotalText = '';

            if (discountValue && discountLine && lineTotalSpan && lineTotalValue && discountValue > 0 && ((maxdiscountValue && maxdiscountValue > 0 && (discountValue <= maxdiscountValue)))) {
                discountMultiplier = discountValue / 100;
                fraction = (lineTotalValue * discountMultiplier);
                discountedLineTotal = lineTotalValue - fraction;
                runningTotal += discountedLineTotal;
                formattedDiscountedLineTotal = discountedLineTotal.formatMoney(2);

                $(lineTotalSpan).text(formattedDiscountedLineTotal);
            } else if (maxdiscountValue && maxdiscountValue > 0 && (discountValue > maxdiscountValue)) {
                $(this).val('');
                alert('Sie können maximal ' + maxdiscountValue + ' % Rabatt gewähren');
                lineTotalText = lineTotalValue.formatMoney(2);
                $(lineTotalSpan).text(lineTotalText);

                runningTotal += lineTotalValue;
            } else {
                lineTotalText = lineTotalValue.formatMoney(2);
                $(lineTotalSpan).text(lineTotalText);

                runningTotal += lineTotalValue;

            }
        });
        if (runningTotal > 0) {
            $(totalValueSpan).text(runningTotal.formatMoney(2));
        }
    });
});