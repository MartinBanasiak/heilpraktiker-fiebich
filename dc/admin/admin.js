var openCardOnStart = false;
var openCardAction = 'edit';
var reloadAfterClose = true;

jQuery(function ($) {
    $.datepicker.regional['de'] = {
        clearText: 'löschen', clearStatus: 'aktuelles Datum löschen',
        closeText: 'schließen', closeStatus: 'ohne Änderungen schließen',
        prevText: '<zurück', prevStatus: 'letzten Monat zeigen',
        nextText: 'Vor>', nextStatus: 'nächsten Monat zeigen',
        currentText: 'heute', currentStatus: '',
        monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni',
            'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
        monthNamesShort: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun',
            'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
        monthStatus: 'anderen Monat anzeigen', yearStatus: 'anderes Jahr anzeigen',
        weekHeader: 'Wo', weekStatus: 'Woche des Monats',
        dayNames: ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'],
        dayNamesShort: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
        dayNamesMin: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
        dayStatus: 'Setze DD als ersten Wochentag', dateStatus: 'Wähle D, M d',
        dateFormat: 'dd.mm.yy', firstDay: 1,
        initStatus: 'Wähle ein Datum', isRTL: false
    };
    $.datepicker.setDefaults($.datepicker.regional['de']);
});

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

function MM_jumpMenu(targ, selObj, restore) { //v3.0
    eval(targ + ".location='" + selObj.options[selObj.selectedIndex].value + "'");
    if (restore) {
        selObj.selectedIndex = 0;
    }
}

function LangAJAXreq1() {
    var company = document.getElementById('company').value;

    $.ajax({
        type: "POST",
        url: "/dc/admin/edit_language_ajax.php",
        data: "company=" + company,
        success: function (resp) {
            // we have the response
            $('#shopcode').empty().append(resp);
        },
        error: function (e) {
            alert('Error: ' + e);
        }
    });
}


function LangAJAXreq2() {
    var shop = document.getElementById('shopcode').value;

    $.ajax({
        type: "POST",
        url: "/dc/admin/edit_language_ajax.php",
        data: "shop=" + shop,
        success: function (resp) {
            // we have the response
            $('#language_code').empty().append(resp);
        },
        error: function (e) {
            alert('Error: ' + e);
        }
    });
}


function LoginAJAXreq1() {
    var site = document.getElementById('site_id').value;

    $.ajax({
        type: "POST",
        url: "/dc/admin/edit_login_ajax.php",
        data: "site=" + site,
        success: function (resp) {
            // we have the response
            $('#main_language_id').empty().append(resp);
        },
        error: function (e) {
            alert('Error: ' + e);
        }
    });
}

function LoginAJAXreq2() {
    var language = document.getElementById('main_language_id').value;
    var siteId = document.getElementById('site_id').value;

    $.ajax({
        type: "POST",
        url: "/dc/admin/edit_login_ajax.php",
        data: "language=" + language + "&site_code=" + siteId,
        success: function (resp) {
            // we have the response
            $('#main_navigation_id').empty().append(resp);
        },
        error: function (e) {
            alert('Error: ' + e);
        }
    });
}

function dctest() {
    alert("zum glück geht das");
}

// linklist functions
var dc = {};
dc.overlay_mode = false; // Karte offen ja nein
dc.linklist_marked = null; // markierte Zeile in einer linkiste
dc.mouse_in_linklist = false; // wenn der mauszeiger sich ueber einer linkliste befindet, dann true
dc.current_linklist = null; // aktuelle linkliste - entweder direkt im content oder im overlay
dc.current_formname = null;
dc.files;
dc.reorder_call = null; // ajax request zum aendern der reihenfolge in den linklisten

// Grab the files and set them to our variable
function prepareUpload(event) {
    dc.files = event.target.files;
}

function centerElement(_el) {
    _el.css({
        position: 'absolute',
        left: ($(window).width() - _el.outerWidth()) / 2,
        top: ($(window).height() - _el.outerHeight()) / 4
    });
}

function setFormname(_formname) {
    dc.current_formname = _formname;
}

function setCurrentToolbarClicked(_el) {
    setFormname(jQuery(_el).data('formname'));
}

function initLinklist() {

    dc.linklist_marked = null;
    dc.mouse_in_linklist = false;

    jQuery('.linklist').each(function () {
        if (jQuery(this).data('linklist_init') === true) {
            return;
        }
        var thiz = this;

        if ($(this).hasClass("sortableTable")) {
            makeSortable($(this));
        }

        jQuery('.linklist_content', this).click(function () {
            if (dc.linklist_marked !== null) {
                jQuery(dc.linklist_marked).removeClass("linklist_active");
            }
            jQuery(this).addClass("linklist_active");
            dc.linklist_marked = this;
            dc.current_linklist = thiz;
        }).mouseenter(function () {
            dc.mouse_in_linklist = true;
        }).mouseleave(function () {
            dc.mouse_in_linklist = false;
        }).dblclick(function (e) {
            if (jQuery(this).data('function') !== undefined) {
                var userFunc = jQuery(this).data('function');
                var funcCall = userFunc + "(this);";
                eval(funcCall);
                return;
            }

            var target = jQuery(e.target);
            setFormname(target.closest('form').attr("id"));
            loadCard(jQuery(this).data("action"));
        }).each(function () {
            if (jQuery(this).data('checked') == true) {
                jQuery(this).addClass("linklist_active");
                dc.linklist_marked = this;
                dc.current_linklist = thiz;
                setFormname(jQuery(this).closest('form').attr("id"));
            }
        });

        jQuery(this).data('linklist_init', true);
    });

    if (openCardOnStart === true) {
        loadCard(openCardAction);
        openCardOnStart = false;
    }
}

function sortableHelper(e, tr) {
    var $originals = tr.children();
    var $helper = tr.clone();
    $helper.children().each(function (index) {
        // Set helper cell sizes to match the original sizes
        $(this).width($originals.eq(index).width());
    });
    return $helper;
}

function makeSortable(_linklist) {
    _linklist.sortable({
        'axis': 'y',
        'items': '.linklist_content',
        handle: '.sorticon',
        stop: function (event, ui) {
            if (dc.reorder_call !== null) {
                dc.reorder_call.abort();
            }

            serialized = _linklist.sortable('serialize');
            reorderCall = jQuery.post("?action=" + _linklist.data("sortableupdateaction"), serialized, function (output, status, xhr) {
                if (xhr.getResponseHeader('REQUIRES_AUTH') == 1) {
                    window.location.replace("/dc/");
                }
            });
        },
        helper: sortableHelper
    });
}

function initForm() {
    $('#overlayContent input.date,#overlayContent .datepicker').each(function () {
        if (jQuery(this).data('datepicker_init') === true) {
            return;
        }

        jQuery(this).datepicker();

        jQuery(this).data('datepicker_init', true);
    });

    $('#overlayContent form').submit(function () {
        return false;
    });
}

function initDatepicker() {
    $('#mainContent input.date, #mainContent .datepicker').datepicker();
}

function scrollSiteNavigation() {
    var h = $(window).height();
    var actualHeight = $('.site_dropdown').actual("height");
    var newHeight = (h / 100 * 75);
    if (actualHeight > newHeight) {
        $('.site_dropdown').css('height', newHeight + 'px');
        $('.site_dropdown').css('overflow-y', 'scroll');
    }
}

var addEvent = function (object, type, callback) {
    if (object == null || typeof(object) == 'undefined') return;
    if (object.addEventListener) {
        object.addEventListener(type, callback, false);
    } else if (object.attachEvent) {
        object.attachEvent("on" + type, callback);
    } else {
        object["on" + type] = callback;
    }
};

function scrollSiteNavigationOnResize() {
    addEvent(window, "resize", scrollSiteNavigation);
}


jQuery(document).ready(function () {
    initLinklist();
    initDatepicker();

    scrollSiteNavigation();
    scrollSiteNavigationOnResize();

    CKEDITOR.dtd.$removeEmpty['span'] = false;

    jQuery( ".datepicker" ).datepicker();

// click auf document
}).click(function (e) {
    if (dc.current_linklist !== null && dc.mouse_in_linklist === false && e.target.nodeName.toLowerCase() != 'a') {
        // unmark all linklist
        jQuery('.linklist_content', dc.current_linklist).removeClass("linklist_active");
        dc.linklist_marked = null;
    }

// keyboardtaste druecken und halten (steuerung der linkliste mit pfeiltasten
}).keydown(function (event) {
    switch (event.keyCode) {
        //arrow down
        case 40:
            var nextRow = jQuery(dc.linklist_marked).nextAll('.linklist_content:visible:first');
            if (nextRow.length == 0) {
                return;
            }
            nextRow.addClass("linklist_active");
            jQuery(dc.linklist_marked).removeClass("linklist_active");
            dc.linklist_marked = nextRow;
            break;
        //arrow up
        case 38:
            var prevRow = jQuery(dc.linklist_marked).prevAll('.linklist_content:visible:first');
            if (prevRow.length == 0) {
                return;
            }
            prevRow.addClass("linklist_active");
            jQuery(dc.linklist_marked).removeClass("linklist_active");
            dc.linklist_marked = prevRow;
            break;

    }

// keyboardtaste druecken
}).keypress(function (event) {
    if (dc.linklist_marked === null) {
        return;
    }

    if (event.keyCode == 13) {
        jQuery(dc.linklist_marked).dblclick();
    }
}).keyup(function (event) {
    if (dc.overlay_mode === true && event.keyCode == 27) { // ESC
        if (jQuery('body').hasClass("ckeditor_maximized")) {
            return;
        }
        event.preventDefault();
        disableOverlay();
        return;
    }
});

function reloadList() {
    jQuery('#mainContent').find('.requestLoader').show();

    // reload
    //document.location = String(document.location).replace(/\?.*$/, '');
    setTimeout(function () {
        document.location = replaceUrlParam(String(document.location), 'action', "");
    }, 10);

    return;
}

function replaceUrlParam(url, paramName, paramValue) {
    var pattern = new RegExp('(' + paramName + '=).*?(&|$)')
    var newUrl = url
    if (url.search(pattern) >= 0) {
        newUrl = url.replace(pattern, '$1' + paramValue + '$2');
    }
    else {
        newUrl = newUrl + (newUrl.indexOf('?') > 0 ? '&' : '?') + paramName + '=' + paramValue
    }
    return newUrl
}

function gotoLocation(_href) {
    document.location = _href;
}

/* ==========================================================================
 Overlay Functions
 ========================================================================== */
function createOverlay() {
    dc.linklist_marked = null;
    dc.current_linklist = null;

    if (dc.overlay_mode === true) {
        return;
    }

    dc.overlay_mode = true;
    jQuery('#overlay').show();

    // overlay wrapper
    jQuery('#overlayWrapper').show();

    jQuery('#overlayLoader').show();
}

function disableOverlay() {
    dc.overlay_mode = false;
    jQuery('#overlay').hide();
    jQuery('#overlayContent').html("").hide();
    jQuery('#overlayWrapper').hide();
    jQuery('#overlayLoader').hide();
    jQuery('body').removeClass("ckeditor_maximized");

    if (reloadAfterClose === true) {
        reloadList();
        return;
    }

    jQuery('.linklist_content').removeClass("linklist_active");
    dc.linklist_marked = null;
    dc.current_linklist = null;
}

function getFormData(_form) {
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }

    return _form.serialize();
}

/**
 * Absenden von requests ausserhalb vom overlay
 *
 * @param _action
 * @param _load_without_check
 * @param _confirmtext
 * @returns {Boolean}
 */
function sendRequest(_action, _load_without_check, _confirmtext) {
    // variable zum pruefen ob in einer linkliste ein element markiert wurde
    var load_without_check = _load_without_check || false;

    if (load_without_check === false) {
        var currentMarkedLinklistRow = jQuery('.linklist_active', jQuery('#' + dc.current_formname));

        if (dc.current_linklist === null || currentMarkedLinklistRow.length <= 0) {
            return;
        }
        var input_id = currentMarkedLinklistRow.data('inputid');
        jQuery('input.selected_linklist_row', jQuery('#' + dc.current_formname)).val(input_id);
    }

    var confirmtext = _confirmtext || false;
    if (confirmtext !== false && confirmtext != "") {
        if (!confirm(confirmtext)) {
            return false;
        }
    }

    if (dc.overlay_mode === true) {
        jQuery('#overlayLoader').show();
    } else {
        jQuery('#mainContent').find('.requestLoader').show();
    }

    jQuery('#' + dc.current_formname).attr("action", "?action=" + _action);
    jQuery('#' + dc.current_formname).submit();
}

/**
 * Karte im overlay laden
 *
 * @param _action
 * @param _load_without_check
 * @param _confirmtext
 * @param _closeOverlay
 * @param _customFormData
 * @returns {Boolean}
 */
function loadCard(_action, _load_without_check, _confirmtext, _closeOverlay, _customFormData) {
    var confirmtext = _confirmtext || false;
    var closeOverlay = _closeOverlay || false;
    var action = _action || "";
    var currentMarkedLinklistRow = jQuery('.linklist_active', jQuery('#' + dc.current_formname));
    var customFormData = _customFormData || false;

    // variable zum pruefen ob in einer linkliste ein element markiert wurde
    var load_without_check = _load_without_check || false;

    // wenn ein listenelement markiert werden MUSS und keins markiert ist, dann nichts machen
    if (load_without_check === false) {
        if (dc.current_linklist === null || currentMarkedLinklistRow.length <= 0) {
            return;
        }
    }

    // wenn bestaetigungstext vorhanden ist user auf Abbrechen klickt, dann return
    if (confirmtext !== false && confirmtext != "") {
        if (!confirm(confirmtext)) {
            return false;
        }
    }

    // Wenn listenelement martkiert ist, dann die inputid uebergeben
    if (currentMarkedLinklistRow.length > 0) {
        var input_id = currentMarkedLinklistRow.data('inputid');
        jQuery('input.selected_linklist_row', jQuery('#' + dc.current_formname)).val(input_id);
    }

    // wird benoetigt damit die Inhalte des fckeditors uebertragen werden koennen
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }

    // heller overlay ueber die ganze seite wird erstellt
    createOverlay();

    // loader wird engezeigt
    jQuery('#overlayLoader').show();

    // Sammeln von formulardaten
    if (customFormData !== false) {
        formData2 = customFormData;
    } else {
        var formData2 = new FormData($('#' + dc.current_formname)[0]);
        if (currentMarkedLinklistRow.length > 0) {
            $.each(currentMarkedLinklistRow.data(), function (idx, el) {
                formData2.append("data-" + idx, el);
            });
        }
    }

    if (closeOverlay === true) {
        formData2.append("force_close", 1);
    } else {
        formData2.append("force_close", 0);
    }

    // AJAX POST request abschicken. Formulardaten werden mit HTML5 FormData gesammelt und verschickt.
    // Dadurch ist es moeglich auch Dateien zu ubertragen (Bildupload moeglich).
    // Fuer den Dateiupload muessen die variablen processData, cache und contentType auf false gesetzt werden
    $.ajax({
        url: "?action=" + action,
        type: 'POST',
        data: formData2,
        mimeType: "multipart/form-data",
        cache: false,
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function (output, status, xhr) {
            if (xhr.getResponseHeader('REQUIRES_AUTH') == 1) {
                window.location.replace("/dc/");
            } else {
                if (closeOverlay === true && xhr.getResponseHeader('EDIT_ERROR') != 1) {
                    disableOverlay();
                } else {
                    jQuery('#overlayContent').html(output);
                    jQuery('#overlayLoader').hide();
                    jQuery('#overlayContent').show();

                    initForm();
                    initLinklist();
                }

            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            //console.log('ERRORS: ' + textStatus);
        }
    });
}