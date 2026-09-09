
$(document).ready(function () {
    $('.slidecontent_headline').click(function () {
        $(this).toggleClass('active');
        $(this).next('.slidecontent_content_container').slideToggle('fast');
    });

    $('.jumpmark').click(function () {
        scrolling($(this).attr('href'));
    });
});

function changeBg(a, img1, img2) {
    if (!a) {
        return true;
    }
    if (a.style.backgroundImage == "url(" + img1 + ")") {
        a.style.backgroundImage = "url(" + img2 + ")";
    } else {
        a.style.backgroundImage = "url(" + img1 + ")";
    }
    return true;
}
function toggleOn(a) {
    var e = document.getElementById(a);
    if (!e) {
        return true;
    }
    e.style.display = "block";
    return true;
}
function toggleOff(a) {
    var e = document.getElementById(a);
    if (!e) {
        return true;
    }
    e.style.display = "none";
    return true;
}
function toggle(a) {
    var e = document.getElementById(a);
    if (e.style.display != "block") {
        if (!e) {
            return true;
        }
        e.style.display = "block";
    } else {
        e.style.display = "none";
    }
}
function toggleOnnb(a) {
    var e = document.getElementById(a);
    if (!e) {
        return true;
    }
    e.style.display = "";
    return true;
}
function toggleOffnb(a) {
    var e = document.getElementById(a);
    if (!e) {
        return true;
    }
    e.style.display = "none";
    return true;
}
function togglenb(a) {
    var e = document.getElementById(a);
    if (e.style.display != "") {
        if (!e) {
            return true;
        }
        e.style.display = "";
    } else {
        e.style.display = "none";
    }
}
function toggleByClass(a) {
    var e = document.getElementByClassName(a);
    if (e.style.display != "block") {
        if (!e) {
            return true;
        }
        e.style.display = "block";
    } else {
        e.style.display = "none";
    }
}
function MM_jumpMenu(targ, selObj, restore) { //v3.0
    eval(targ + ".location='" + selObj.options[selObj.selectedIndex].value + "'");
    if (restore) {
        selObj.selectedIndex = 0;
    }
}
function openPopup(URL, WIDTH, HEIGHT) {
    if (!WIDTH) {
        WIDTH = 600;
    }
    if (!HEIGHT) {
        HEIGHT = 600;
    }
    WIDTH = 600;
    HEIGHT = 600;
    popup = window.open(URL, "popup", "width=" + WIDTH + ", height=" + HEIGHT + ", scrollbars=no");
}
function showLayer(lyr) {
    makeHistory(lyr);
    document.getElementById(currentLayer).className = 'hide';
    document.getElementById(lyr).className = 'show';
    currentLayer = lyr;
}
function showTab(lyr) {
    document.getElementById(currentTab).className = 'taboff';
    document.getElementById(lyr).className = 'tabon';
    currentTab = lyr;
}
function makeHistory(newHash) {
    window.location.hash = "_" + newHash;
    expectedHash = window.location.hash;
    return true;
}
function handleHistory() {
    if (window.location.hash != expectedHash) {
        expectedHash = window.location.hash;
        if (expectedHash.match('tab')) {
            showLayer(expectedHash.substring(2));
        }
    }
    return true;
}
function pollHash() {
    handleHistory();
    window.setInterval("handleHistory()", 200);
    return true;
}
function toggleDiv(id) {
    /*
     var obj=document.getElementsByTagName("div");
     for(i=0;i<obj.length;i++) {
     if(obj[i].collectionEntityClassName =="func") {
     obj[i].style.display="none";
     }
     }
     */
    if (document.getElementById(id).style.display == 'none') {
        document.getElementById(id).style.display = 'block';
        document.getElementById('head_' + id).className = 'function_cat open';
    } else {
        document.getElementById(id).style.display = 'none';
        document.getElementById('head_' + id).className = 'function_cat closed';
    }
}
function MM_swapImgRestore() { //v3.0
    var i, x, a = document.MM_sr;
    for (i = 0; a && i < a.length && (x = a[i]) && x.oSrc; i++) {
        x.src = x.oSrc;
    }
}
function MM_preloadImages() { //v3.0
    var d = document;
    if (d.images) {
        if (!d.MM_p) {
            d.MM_p = new Array();
        }
        var i, j = d.MM_p.length, a = MM_preloadImages.arguments;
        for (i = 0; i < a.length; i++) {
            if (a[i].indexOf("#") != 0) {
                d.MM_p[j] = new Image;
                d.MM_p[j++].src = a[i];
            }
        }
    }
}
function MM_findObj(n, d) { //v4.01
    var p, i, x;
    if (!d) {
        d = document;
    }
    if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
        d = parent.frames[n.substring(p + 1)].document;
        n = n.substring(0, p);
    }
    if (!(x = d[n]) && d.all) {
        x = d.all[n];
    }
    for (i = 0; !x && i < d.forms.length; i++) {
        x = d.forms[i][n];
    }
    for (i = 0; !x && d.layers && i < d.layers.length; i++) {
        x = MM_findObj(n, d.layers[i].document);
    }
    if (!x && d.getElementById) {
        x = d.getElementById(n);
    }
    return x;
}
function MM_swapImage() { //v3.0
    var i, j = 0, x, a = MM_swapImage.arguments;
    document.MM_sr = new Array;
    for (i = 0; i < (a.length - 2); i += 3) {
        if ((x = MM_findObj(a[i])) != null) {
            document.MM_sr[j++] = x;
            if (!x.oSrc) {
                x.oSrc = x.src;
            }
            x.src = a[i + 2];
        }
    }
}

function isTouchDevice() {
    var el = document.createElement('div');
    el.setAttribute('ontouchstart', 'return;');
    if(typeof el.ontouchstart == "function"){
        return true;
    }else {
        return false
    }
}


function scrolling(tabcontent_id) {
    var ziel = $(tabcontent_id);
    var top = ziel.offset().top;
    var heightHeader = $('#header').outerHeight();
    $('html,body').animate({
        scrollTop: top - heightHeader
    }, 800);
}

//mejs.i18n.language('de');

$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^]*)').exec(window.location.href);
    if (results==null){
        return null;
    }
    else{
        return results[1] || 0;
    }
}
/*
 * Klickbare Gruppen-Kacheln.
 *
 * show_group_content() in dc/frontend/frontend_functions.inc.php umschliesst
 * eine Kachel mit <div class="link" data-href="...">. Frueher stand dort ein
 * onClick: mit der Maus bedienbar, per Tastatur nicht erreichbar (WCAG 2.1.1).
 *
 * Enthaelt die Kachel bereits einen echten Link - auf der Startseite ist das
 * durchweg "mehr erfahren" -, bleibt dieser der einzige Tabstopp. Nur Kacheln
 * ohne eigenen Link werden selbst fokussierbar. So entstehen keine doppelten
 * Tabstopps und Screenreader lesen jedes Ziel genau einmal vor.
 */
$(function () {
    var $tiles = $('.link[data-href]');

    if (!$tiles.length) {
        return;
    }

    $tiles.each(function () {
        if ($(this).find('a[href]').length === 0) {
            $(this).attr({ 'tabindex': 0, 'role': 'link' });
        }
    });

    $tiles.on('click', function (event) {
        // Echte Bedienelemente behalten ihr eigenes Verhalten
        if ($(event.target).closest('a[href], button, input, select, textarea, label').length) {
            return;
        }
        window.location.href = $(this).data('href');
    });

    $tiles.on('keydown', function (event) {
        if (this !== event.target) {
            return;
        }
        if (event.key === 'Enter' || event.key === ' ' || event.key === 'Spacebar') {
            event.preventDefault();
            window.location.href = $(this).data('href');
        }
    });
});
