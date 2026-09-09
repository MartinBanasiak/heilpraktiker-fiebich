function setCurrentToolbarClicked(el) {
    return;
}

function showUl() {
    $(this).addClass('hoverintent');
}

function hideUl() {
    $(this).removeClass('hoverintent');
}

function showUlNavigation() {
    $(this).addClass('hoverintent');
    $(this).children('ul').fadeIn('fast');
}

function hideUlNavigation() {
    $(this).removeClass('hoverintent');
    $(this).children('ul').fadeOut('fast');
}

var config = {
    over: showUl,
    timeout: 200,
    out: hideUl
};

var configNavigation = {
    over: showUlNavigation,
    timeout: 200,
    out: hideUlNavigation
};

$(document).ready(function () {

    stickyHeader();

    $('#toggle_navigation, #primary_navigation_mobile .close_button_navigation_mobile, #overlay').click(function () {
        toggle_mobile_menu();
    });

    // Escape schliesst das mobile Menue - erwartetes Verhalten fuer alles,
    // was sich ueber die Seite legt.
    $(document).on('keydown', function (event) {
        if (event.key === 'Escape' && $('#container').hasClass('open_menu')) {
            toggle_mobile_menu();
        }
    });

    $('#primary_navigation > ul > li').hoverIntent(configNavigation);


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

    initScrolltopbutton($('#scrolltop_button'));

    checkMilestones();
});

$(window).scroll(function () {
   checkMilestones();
});

$(window).resize(function () {
   checkMilestones();
});

$(window).load(function () {
   $('#banner h1').each(function () {
       var thisHeadline = $(this);
       setTimeout(function(){
           thisHeadline.closest('#banner').addClass('active');
       },1000);
   });

    $(".stickyBox").pin({
        containerSelector: ".stickyArea",
        minWidth: 1024,
        diff: 90
    });
});

function checkMilestones(){
    var $cards = $('.milestones__item');

    var time = 500;

    $cards.each(function() {
        var t = $(this);
        setTimeout( function(){ checkSingleMilestone(t); }, time)
        time += 500;
    });
}

function checkSingleMilestone(el){
    if(el.isInViewport()){
        el.addClass('active');
    }
}

function initScrolltopbutton (scrolltop) {
    if($(window).scrollTop() > 0) {
        scrolltop.addClass('sticky');
        var scrolltopButtonBottom = scrolltop.offset().top + scrolltop.outerHeight();
        scrolltop.css('bottom','');
    }else{
        scrolltop.removeClass('sticky');
    }

    $(window).scroll(function () {
        if($(window).scrollTop() > 0) {
            scrolltop.addClass('sticky');
            var scrolltopButtonBottom = scrolltop.offset().top + scrolltop.outerHeight();
            scrolltop.css('bottom','');
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
    if (!$('#container').hasClass('open_menu')) {
        $('.modal').modal('hide');
        $('#primary_navigation_mobile').fadeIn('fast');
        $('#container').addClass('open_menu');
        $('#overlay').addClass('open_menu');

        // Zustand fuer Screenreader mitfuehren und den Fokus mitnehmen,
        // sonst tabbt man nach dem Oeffnen weiter durch die Seite dahinter.
        $('#toggle_navigation').attr('aria-expanded', 'true');
        $('#primary_navigation_mobile .close_button_navigation_mobile').focus();

        $('#overlay').animate({
            opacity: 0.6
        }, 100);
    } else {
        $('#toggle_navigation').attr('aria-expanded', 'false');

        $('#overlay').animate({
            opacity: 0
        }, 100, function () {
            $('#container').removeClass('open_menu');
            $('#overlay').removeClass('open_menu');
            $('#primary_navigation_mobile').fadeOut('fast');
            $('#toggle_navigation').focus();
        });
    }
}

function stickyHeader() {
    var mn = $('header');
    var mns = "sticky";
    var hdr = $('header').outerHeight();

    checkSticky(mn, mns, hdr);

    $(window).scroll(function () {
        checkSticky(mn, mns, hdr);
    });
    $(window).resize(function () {
        var hdr = $('header').outerHeight();
        checkSticky(mn, mns, hdr);
    });
}

function checkSticky(mn, mns, hdr) {
    if ($(window).scrollTop() > hdr) {
        mn.addClass(mns);
    } else {
        mn.removeClass(mns);
    }
}

$.fn.isInViewport = function() {
    var elementTop = $(this).offset().top;
    var elementBottom = elementTop + $(this).outerHeight();
    var viewportTop = $(window).scrollTop();
    var viewportBottom = viewportTop + $(window).height();
    return elementBottom > viewportTop && elementTop < viewportBottom;
};