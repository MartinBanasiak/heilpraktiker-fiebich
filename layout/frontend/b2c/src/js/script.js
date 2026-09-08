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

    $('#primary_navigation li.level_1 > a').click(function(event) {
        if(isTouchDevice()){
            event.preventDefault();
        }
    });

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

    if($(window).width() < 768) {
        $('.textcontent table').wrap('<div class=\"table_wrapper\"></div>');
    }

    initScrolltopbutton($('#scrolltop_button'),$('#footer_3'));
});

function initScrolltopbutton (scrolltop,footer) {
    if($(window).scrollTop() > 0) {
        scrolltop.addClass('sticky');
        var scrolltopButtonBottom = scrolltop.offset().top + scrolltop.outerHeight();
        if(scrolltopButtonBottom > footer.offset().top ){
            scrolltop.css('bottom',footer.outerHeight());
        }else {
            scrolltop.css('bottom','');
        }
    }else{
        scrolltop.removeClass('sticky');
    }

    $(window).scroll(function () {
        if($(window).scrollTop() > 0) {
            scrolltop.addClass('sticky');
            var scrolltopButtonBottom = scrolltop.offset().top + scrolltop.outerHeight();
            if(scrolltopButtonBottom > footer.offset().top ){
                scrolltop.css('bottom',footer.outerHeight());
            }else {
                scrolltop.css('bottom','');
            }
        }else{
            scrolltop.removeClass('sticky');
        }
    });

    scrolltop.click(function () {
        $('html,body').animate({
            scrollTop: 0
        }, 800);
    })
}

function toggle_mobile_menu() {
    var widthNavigation = $('#primary_navigation_mobile').width();
    var widthContainer = $('#container').width();
    if (!$('#container').hasClass('open_menu')) {
        $('.modal').modal('hide');
        $('#primary_navigation_mobile').show();
        $('#container').addClass('open_menu');
        $('#container').width(widthContainer);
        $('#header').width(widthContainer);
        $('#overlay').css('left', widthNavigation);
        $('#overlay').addClass('open_menu');

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
                $('#container').width('auto');
                $('#overlay').css('left', '120%');
                $('#overlay').removeClass('open_menu');
            });
            if ($('#header').css('position') == "fixed") {
                $('#header').animate({
                    left: 0
                }, 200, function () {
                    $('#header').width('100%');
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
        var position = mn.css('position');
        mn.addClass(mns);
        if (position != "fixed" && mn.prev('.sticky-helper').length < 1) {
            mn.before('<div class="sticky-helper" style="height:' + (mn.outerHeight()) + 'px"></div>');
        }
    } else {
        mn.removeClass(mns);
        $('.sticky-helper').remove();
    }
}