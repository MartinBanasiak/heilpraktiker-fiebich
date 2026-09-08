function setCurrentToolbarClicked(el) {
    return;
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