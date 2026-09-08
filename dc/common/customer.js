function setCurrentToolbarClicked( el ) {
    return;
}

function showUl() {
    $(this).addClass('hoverintent');
}

function hideUl() {
    $(this).removeClass('hoverintent');
}

function showItemlistExt() {
    $(this).addClass('hoverintent');
    $(this).find('.itemlist_extended').slideDown('fast');
}

function hideItemlistExt() {
    $(this).removeClass('hoverintent');
    $(this).find('.itemlist_extended').slideUp('fast');
}

var config = {
    over   : showUl,
    timeout: 200,
    out    : hideUl
};

var configItemlist = {
    over: showItemlistExt,
    timeout: 200,
    out: hideItemlistExt
};

$(document).ready(function () {
    $('#primary_navigation li.level_1,#box,.user_account_link,#header_basket').hoverIntent(config);
    $('.itemcard_list .itemlist').hoverIntent(configItemlist);

    var noOfImg = $('#menu_1 div.itemlist11>a>img').length,
        loadedImg = 0;

    $('#menu_1 div.itemlist11>a>img').on('load', function () {
        ++loadedImg;
        if (loadedImg == noOfImg) {
            $('#menu_1 div.itemlist11>a>img').each(function () {
                var pic = $(this),
                    picHeight = $(this).outerHeight(),
                    par = $(pic).parents('a'),
                    parHeight = $(par).height(),
                    margin = Math.ceil((parHeight - picHeight) / 2);
                console.log('picHeight:' + picHeight + ' parHeight:' + parHeight + ' margin:' + margin);
                $(pic).css('margin-top', margin + 'px');
            });
        }
    });

    var noOfImgItem = $('#itemcard_left div.item_images>a>img').length,
        loadedImgItem = 0;
    $('#itemcard_left div.item_images>a>img').on('load', function () {
        ++loadedImgItem;
        if (loadedImgItem == noOfImgItem) {
            $('#itemcard_left div.item_images>a>img').each(function () {
                var pic = $(this),
                    picHeight = $(this).outerHeight(),
                    par = $(pic).parents('a'),
                    parHeight = $(par).height(),
                    margin = Math.ceil((parHeight - picHeight) / 2);
                console.log('picHeight:' + picHeight + ' parHeight:' + parHeight + ' margin:' + margin);
                $(pic).css('margin-top', margin + 'px');
            });
        }
    });


    //MAB setze Span in Überschrift um Linien zu erzeugen
    $('.headline-lines').each(function () {
        var arr = [ "h1", "h2", "h3", "h4", "h5", "h6" ];
        var thisHeadline = $(this);

        jQuery.each(arr,function(i,val) {
            var text = thisHeadline.find(val).html();
            thisHeadline.find(val).html('<span>'+text+'</span>');
        });
    })

    stickyHeader();

    $('#toggle_navigation, #primary_navigation_mobile .close_button_navigation_mobile, #overlay').click(function () {
        toggle_mobile_menu();
    });

    $('.filterbox-mobilebutton').click(function () {
        $(this).toggleClass('active');
        $('.filterbox').slideToggle('fast');
    })

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
});

function toggle_mobile_menu () {
    var widthNavigation = $('#primary_navigation_mobile').width();
    var widthContainer = $('#container').width();
    if(!$('#container').hasClass('open_menu')) {
        $('#container').addClass('open_menu');
        $('#container').width(widthContainer);
        $('#header').width(widthContainer);
        $('#overlay').css('left',widthNavigation);
        $('#overlay').addClass('open_menu');

        $('#container').animate( {
            left: widthNavigation
        },200, function(){
            $('#overlay').animate({
                opacity: 0.6
            },100);
        });
        if ($('#header').css('position') == "fixed") {
            $('#header').animate({
                left: widthNavigation
            }, 200);
        }
    }else{
        $('#overlay').animate( {
            opacity: 0
        },100, function(){
            $('#container').animate({
                left: 0
            },200,function () {
                $('#container').removeClass('open_menu');
                $('#container').width('auto');
                $('#overlay').css('left', '120%');
                $('#overlay').removeClass('open_menu');
            });
            if ($('#header').css('position') == "fixed") {
                $('#header').animate({
                    left: 0
                }, 200, function () {
                    $('#header').width('100%');
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

    $(window).scroll(function() {
        checkSticky(mn, mns, hdr);
    });
    $( window ).resize(function() {
        var hdr = $('#header_account').outerHeight();
        checkSticky(mn, mns, hdr);
    });
}

function checkSticky(mn, mns, hdr) {
    if( $(window).scrollTop() > hdr ) {
        mn.addClass(mns);
        if(mn.css('position') != "fixed" && mn.prev('.sticky-helper').length < 1){
            mn.before('<div class="sticky-helper" style="height:'+(mn.outerHeight())+'px"></div>');
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
                $(currInput).on('keyup', function ( e ) {
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
        console.log(data);
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