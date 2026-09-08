
function setCurrentToolbarClicked(el) {
    return;
}

function showUl() {
    $(this).addClass('hoverintent');
}

function hideUl() {
    $(this).removeClass('hoverintent');
}

var config = {
    over: showUl,
    timeout: 200,
    out: hideUl
};

$(document).ready(function () {


    //Itemlist
    $('div.itemcard_list1 a.itemlist_order_button_link').on('click',function (e) {
        e.preventDefault();
        e.stopPropagation();

        if (!($(this).attr('data-disabled') == 'disabled')) {
            var doSubmit = false;
            var form = $(this).closest('form');
            form.find('.input_item_quantity_value').each(function () {
                if ($(this).val() > 0) {
                    doSubmit = true;
                }
            });
            if (doSubmit) {
                form.submit();
            }
        }
    });

    $('#primary_navigation li.level_1,#box,.user_account_link,#header_basket').hoverIntent(config);

    $('#toggle_navigation, #primary_navigation_mobile .close_button_navigation_mobile, #overlay').click(function () {
        toggle_mobile_menu();
    });

    $('.filterbox-mobilebutton').click(function () {
        $(this).toggleClass('active');
        $('.filterbox').slideToggle('fast');
    });

    $('.filter > .filter_headline').click(function () {
        var active = $(this).hasClass('clicked');

        $('.filterbox .filter_headline').removeClass('clicked');
        $('.filterbox .filter_toggle').slideUp('fast');

        if (active != false) {
            $(this).removeClass('clicked');
            $(this).next('.filter_toggle').slideUp('fast');
        } else {
            $(this).addClass('clicked');
            $(this).next('.filter_toggle').slideDown('fast');
        }
    })


    $('#primary_navigation_mobile ul li > a').click(function (event) {
        var nextUl = $(this).next('ul');
        if (nextUl.length > 0) {
            if (nextUl.is(':visible')) {
                nextUl.slideUp('slow');
                $(this).removeClass('active_tree');
                $(this).parent().removeClass('active_tree');
                nextUl.find('ul').slideUp('fast');
            } else {
                nextUl.slideDown('slow');
                $(this).addClass('active_tree');
                $(this).parent().addClass('active_tree');
            }
            event.preventDefault();
        }
    });

    $('#header_search_mobile').click(function () {
        var search = $('#header_search');
        if (search.is(':visible')) {
            search.fadeOut('fast');
        } else {
            search.fadeIn('fast');
        }
    });

    $('#input_search_b2b').on('keyup',function(e){
        if(e.keyCode == 13) {
            //$(this).closest('form').submit();
            document.getElementById("form_search").submit();
        }
    });

    $('.toggleTable').click(function (e) {
        if($(event.target).attr('class') !== "button")  {
            $(this).find('.toggleTableRow:not(.isfirst)').slideToggle('fast');
            $(this).toggleClass('active');
        }
    });

});

function toggle_mobile_menu() {
    var widthNavigation = $('#primary_navigation_mobile').width();
    var widthContainer = $('#container').width();
    if (!$('#container').hasClass('open_menu')) {
        $('#container').addClass('open_menu');
        $('#container').width(widthContainer);
        $('#header').width(widthContainer);
        $('#overlay').css('left', widthNavigation);
        $('#overlay').addClass('open_menu');
        $('#primary_navigation_mobile').show();

        $('#container').animate({
            left: widthNavigation
        }, 200, function () {
            $('#overlay').animate({
                opacity: 0.6
            }, 100);
        });
        if ($('#header').css('position') == "fixed") {
            $('#header').animate({
                left: widthNavigation
            }, 200);
        }
    } else {
        $('#overlay').animate({
            opacity: 0
        }, 100, function () {
            $('#container').animate({
                left: 0
            }, 200, function () {
                $('#container').removeClass('open_menu');
                $('#container').css('width', '');
                $('#overlay').css('left', '');
                $('#overlay').removeClass('open_menu');
            });
            if ($('#header').css('position') == "fixed") {
                $('#header').animate({
                    left: 0
                }, 200, function () {
                    $('#header').css('width', '');
                    $('#primary_navigation_mobile').hide();
                });
            }
        });
    }
}

function stickyHeader() {
    var mn = $('#header');
    var header = $('#header');
    var mns = "sticky";
    var hdr = $('#header_account').outerHeight();

    checkSticky(mn, mns, hdr);

    $(window).scroll(function () {
        checkSticky(mn, mns, hdr);
    });
    $(window).resize(function () {
        var hdr = $('#header_account').outerHeight();
        checkSticky(mn, mns, hdr);
    });
}

function checkSticky(mn, mns, hdr) {
    if ($(window).scrollTop() > hdr) {
        mn.addClass(mns);
        if (mn.css('position') != "fixed" && mn.prev('.sticky-helper').length < 1) {
            mn.before('<div class="sticky-helper" style="height:' + (mn.outerHeight()) + 'px"></div>');
        }
    } else {
        mn.removeClass(mns);
        $('.sticky-helper').remove();
    }
}

$(document).ready(function () {
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
                $(currInput).on('keyup', function (e) {
                    if (e.which === 13) {
                        $(this).parents('form').submit();
                        return false;
                    }
                });
            }
        }
    });

    //Toggle variants
    $('.hasvar td:first-child').on('click', function () {
        var parentTr = $(this).parent('.hasvar'),
            itemNo = parentTr[0].dataset.itemno,
            varTrs,
            initStringEl = $(parentTr).find('.replace_price_string_toggle'),
            initString,
            replacementString,
            i = 0;

        if (typeof initStringEl !== 'undefined' && initStringEl.length > 0) {
            initString = $(initStringEl).data('initstring');
            replacementString = $(initStringEl).data('replacementstring');
        }

        if (typeof itemNo !== 'undefined' && itemNo.length > 0) {
            varTrs = $('.isvar_' + itemNo);
            varTrscol = $('.iscolorvar_' + itemNo);
            if (typeof varTrs !== 'undefined' && varTrs.length > 0) {
                i = 0;
                $(varTrs).each(function () {
                    if ($(this).is(':visible')) {
                        $(this).hide();
                        if (i == 0) {
                            $(parentTr).removeClass('varsopen').addClass('varsclosed');
                            if (initString.length > 0 && replacementString.length > 0) {
                                $(initStringEl).empty().html(initString);
                            }
                        }

                    } else {
                        $(this).show();
                        if (i == 0) {
                            $(parentTr).removeClass('varsclosed').addClass('varsopen');
                            if (initString.length > 0 && replacementString.length > 0) {
                                $(initStringEl).empty().html(replacementString);
                            }
                        }
                    }
                    i++;
                });
            }
            if (typeof varTrscol !== 'undefined' && varTrscol.length > 0) {
                $(varTrscol).each(function () {
                    if ($(this).is(':visible')) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            }
        }
    });


    $("#input_item_no").change(function () {


        var data = {};
        var dataDiv = $('#search_data');

        data.item_number = $("#input_item_no").val();
        data.company = $(dataDiv).data("company");
        data.shop_code = $(dataDiv).data("shop_code");
        data.language_code = $(dataDiv).data("language_code");
        data.token_1 = $('#token_1').val();
        data.token_2 = $('#token_2').val();

        // jsonData = JSON.stringify(data);
        //
        $.ajax({
            type: 'POST',
            url: '/module/dcshop/get_variants.php',
            data: data,
            beforeSend: function () {

            }
        }).done(function (data) {

            $('#input_var_code').empty();

            $('#input_var_code').append(data);


        }).fail(function () {

        });

    });

    $('#addNewAddressOrderPage').click(function () {
        console.log("open123423423");

        $('#errorMessageDivModal').css('display','none');
        $('#saveModelFormButton').val('Add');
        $('#addEditAddressModel').children().find('input,select').each(function () {
            $(this).val('');
        });
    });

    $('#editAddressOrderPage').click(function () {
        console.log("edit");
        console.log(123);

        $('#errorMessageDivModal').css('display','none');
        $('#saveModelFormButton').val('Edit');

        $selectedAddress = $("#input_shipment_address_id").val();

        var data = {};
        data.shop_shipment_address_id = $selectedAddress;
        data.action = "getShipmentAddressData";
        data.secretToken = $('#secretKey').val();
        data.token_2 = $('#token_2').val();
        data.token_1 = $('#token_1').val();
        $.ajax({
            type: 'POST',
            url: '/module/dcshop/get_user_address.php',
            data: data,
            beforeSend: function () {

            }
        }).done(function (data) {

            var dataArray = JSON.parse(data);

            var id = dataArray[0]['id'];
            var name = dataArray[0]['name'];
            var name_2 = dataArray[0]['name_2'];
            var contact = dataArray[0]['contact'];
            var address = dataArray[0]['address'];
            var address_2 = dataArray[0]['address_2'];
            var post_code = dataArray[0]['post_code'];
            var city = dataArray[0]['city'];
            var phone_no = dataArray[0]['phone_no'];

            $('#input_id_Modal').val(id);
            $('#input_name_Modal').val(name);
            $('#input_name_2_Modal').val(name_2);
            $('#input_contact_Modal').val(contact);
            $('#input_address_Modal').val(address);
            $('#input_address_2_Modal').val(address_2);
            $('#input_post_code_Modal').val(post_code);
            $('#input_city_Modal').val(city);
            $('#input_telephone_Modal').val(phone_no);

        }).fail(function () {

        });

    });
    $('#closeModelHeaderButton').click(function () {
        $('#errorMessageDivModal').css('display','none');
        $('#addEditAddressModel').children().find('input,select').each(function () {
            $(this).val('');
        });
    });
    $('#closeModelFooterButton').click(function () {
        $('#errorMessageDivModal').css('display','none');
        $('#addEditAddressModel').children().find('input,select').each(function () {
            $(this).val('');
        });
    });

    $('#saveModelFormButton').click(function () {

        console.log("save");

        var actionValue = $('#saveModelFormButton').val();

        var data = $('#form_user_order').serializeArray();

        if ( $('#input_shipment_address_id').val() == '' || $('#input_name_Modal').val() == '' || $('#input_address_Modal').val() == '' || $('#input_post_code_Modal').val() == '' || $('#input_city_Modal').val() == '')
        {
            $('#errorMessageDivModal').css('display','block');
            return false;
        }


        var obj = {};
        obj['name'] = 'action';
        obj['value'] = actionValue;
        data.push(obj);
        var obj2 = {};
        var dataDiv = $('#search_data');
        obj2['name'] = 'company';
        obj2['value'] = $(dataDiv).data("company");
        data.push(obj2);
        var obj3 = {};
        obj3['name'] = 'customer_no';
        obj3['value'] = $(dataDiv).data("customer_no");
        data.push(obj3);
        var obj4 = {};
        obj4['name'] = 'secretToken';
        obj4['value'] = $('#secretKey').val();
        data.push(obj4);
        $.ajax({
            type: 'POST',
            url: '/module/dcshop/get_user_address.php',
            data: data,
            beforeSend: function () {

            }
        }).done(function (data) {

            if (data != '') {
                $('#input_shipment_address_id').append(data);
            }

            $('#form_user_order').submit();
            //location.reload();
        }).fail(function (data) {
            return false;
        });


    });
});


function itemsearchSuggest(searchString) {
    var data = {},
        dataDiv = $('#search_data'),
        jsonData;

    data.company = $(dataDiv).data("company");
    data.shop_code = $(dataDiv).data("shop_code");
    data.language_code = $(dataDiv).data("language_code");
    data.site_language = $(dataDiv).data("site_language");
    data.item_source = $(dataDiv).data("item_source");
    data.site_code = $(dataDiv).data("site_code");
    data.sid = $(dataDiv).data("sid");
    data.image_config = $(dataDiv).data("image_config");
    data.default_img = $(dataDiv).data("default_img");
    data.input_search = searchString;
    data.customer_no = $(dataDiv).data("customer_no");
    jsonData = JSON.stringify(data);
    $.ajax({
        type: 'POST',
        url: '/module/dcshop/b2c/ajax_itemsearch.php',
        data: jsonData,
        dataType: "json",
        converters: {
            'text json': true
        },
        beforeSend: function () {
            $('#itemsearch_suggestion_wrapper').hide().empty();
        }
    }).done(function (data) {
        $('#itemsearch_suggestion_wrapper').html(data).show(0, function () {
            var searchSuggestions = $('.search_suggestion');
            $(document).on('keydown', function (e) {
                var evt = e || window.event;
                if (evt.keyCode == 38 || evt.keyCode == 40) {
                    e.preventDefault();
                    return false;
                }
            });
            $('#input_search').on('keyup', function (e) {
                var evt = e || window.event,
                    code = (e.keyCode ? e.keyCode : e.which);
                if (evt.keyCode === 40 && typeof searchSuggestions !== 'undefined' && searchSuggestions.length) { //Pfeil nach unten
                    $(searchSuggestions).first().addClass('hasfocus').focus();
                }
            });
            $('.search_suggestion').on('click', function () {
                var newHref = $(this).data('newhref');
                if (typeof newHref !== 'undefined' && newHref.length) {
                    window.location.href = newHref;
                }
            });
            $('.search_suggestion').on('keyup', function (e) {
                var newHref = $(this).data('newhref'),
                    evt = e || window.event,
                    code = (evt.keyCode ? evt.keyCode : evt.which),
                    currSuggestion = $(this),
                    currIndex = $(currSuggestion).data('suggestionindex'),
                    newIndex,
                    newSuggestion;
                if (evt.keyCode == 13 && typeof newHref !== 'undefined' && newHref.length) {
                    window.location.href = newHref;
                } else if (evt.keyCode === 40 && typeof searchSuggestions !== 'undefined' && searchSuggestions.length) { //Pfeil nach unten
                    newIndex = currIndex + 1;
                    newSuggestion = $('#item_search_suggestions').find("[data-suggestionIndex='" + newIndex + "']");
                    if (typeof newSuggestion !== 'undefined' && newSuggestion.length) {
                        $(newSuggestion).siblings().removeClass('hasfocus');
                        $(newSuggestion).addClass('hasfocus').focus();
                    }
                } else if (evt.keyCode === 38 && typeof searchSuggestions !== 'undefined' && searchSuggestions.length) { //Pfeil nach oben
                    newIndex = currIndex - 1;
                    newSuggestion = $('#item_search_suggestions').find("[data-suggestionIndex='" + newIndex + "']");
                    if (typeof newSuggestion !== 'undefined' && newSuggestion.length) {
                        $(newSuggestion).siblings().removeClass('hasfocus');
                        $(newSuggestion).addClass('hasfocus').focus();
                    } else {
                        $('.search_suggestion').removeClass('hasfocus');
                        $('#input_search').focus();
                    }
                }
            });
        });
    }).fail(function () {
        $('#itemsearch_suggestion_wrapper').hide().empty();
    });
}

$(document).ready(function () {



    if ($('#search_data').length) {
        var queryDelay = 300,
            queryTimeout;

        $('#input_search').on('keyup', function (e) {
            queryString = $('#input_search').val();
            if (queryTimeout) {
                window.clearTimeout(queryTimeout);
                queryTimeout = null;
            }
            if (e.keyCode != 13 && e.keyCode != 40 && e.keyCode != 38 && e.keyCode != 8 && queryString.length > 3) {
                queryTimeout = window.setTimeout(function () {
                    itemsearchSuggest(queryString);
                }, queryDelay);
            }
        });

        $('html').on('click', function () {
            //hide elements if visible
            var searchSuggestWrapper = $('#itemsearch_suggestion_wrapper');

            if (typeof searchSuggestWrapper !== 'undefined' && $(searchSuggestWrapper).is(':visible')) {
                $(searchSuggestWrapper).empty().hide();
            }
        });

        $('#itemsearch_suggestion_wrapper,#form_search').on('click', function (event) {
            event.stopPropagation();
        });
    }
});

function checkFixedFooter(element) {
    element.css('position','');
    element.css('width','');
    element.css('bottom','');
    element.css('left','');
    if(element.position().top <= $(window).height() && $(window).width() > 768){
        element.css('position','fixed');
        element.css('width','100%');
        element.css('bottom','0');
        element.css('left','0');
    }

}

$(document).ready(function () {
    checkFixedFooter($('#footer'));
    $(window).resize(function () {
        checkFixedFooter($('#footer'));
    });
    $( window ).on( "orientationchange", function( event ) {
        checkFixedFooter($('#footer'));
    });
})