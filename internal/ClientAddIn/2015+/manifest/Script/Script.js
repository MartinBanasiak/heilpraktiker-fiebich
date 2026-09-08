function LoadUrl(url) {
    //$('#controlAddIn').append('<button value="Test" onclick="SendMessage();">Test</button>');
    $('#controlAddIn').append('<iframe width="100%" height="100%" id="iframe" border="0" frameBorder="0" style="display: none;">');
    $('iframe#iframe').attr('src', url);

    $('iframe#iframe').load(function () {
        $('.spinner').hide();
        $('iframe#iframe').show();
        Microsoft.Dynamics.NAV.InvokeExtensibilityMethod('URLLoaded', null);
    });
}

var _stretchContainer = function () {
    var scrollbarWidth = 17;
    var $scrollContainer = $(parent.document).find('.ms-core-overlay');

    if ($scrollContainer.length !== 0) {
        var containerHeight = $scrollContainer[0].getBoundingClientRect().height;
        var oldContainerScrollHeight = $scrollContainer.prop('scrollHeight');

        // Determine the current additional height which lets appear the
        // vertical scrollbar, if any.
        var additionalHeight = 0;
        if (oldContainerScrollHeight > containerHeight)
            additionalHeight = oldContainerScrollHeight - containerHeight;

        // Set the height of the iframe which contains the control add-in
        // to a very high value so that a vertical scrollbar will appear
        // in any case.
        var $iframe = $(parent.document).find("iframe:first");
        $iframe.css('height', '10000px');

        // Calculate the available height.
        // With (containerScrollHeight - 10000) we calculate the space which is
        // used by all the other elements above and below the control add-in.
        var containerScrollHeight = $scrollContainer.prop('scrollHeight');
        var availableHeight = containerHeight - (containerScrollHeight - 10000)
            - scrollbarWidth
            + additionalHeight;

        // Ensure that the height will not go below the given minimum
        var minHeight = $iframe.css("min-height").replace("px", "");
        if (availableHeight < minHeight)
            availableHeight = minHeight;

        // Set the available height
        $iframe.css('height', availableHeight + 'px');
    }

    // Reduce the height of the VPS Editor by the height of the tool bar ribbon
    $('iframe#iframe').css('height', '100%')
        .css('height', '-=' + $('#AppRibbon').outerHeight(true)
            + 'px');
}

$(top.window).bind("resize", function () {
    _stretchContainer();
});
$(window).bind("resize", function () {
    _stretchContainer();
});

function SendMessage_(data) {
    var win = document.getElementById("iframe").contentWindow;
    if (win == null || !window['postMessage'])
        alert("oh crap");
    else
        win.postMessage(JSON.stringify(data), "*");
}

function SendMessage(dest, event, data, source) {
    var sendData = {Event: event, Data: data};
    if (dest == null || !window['postMessage'])
        alert("oh crap");
    else
        dest.postMessage(JSON.stringify(sendData), source);
}


function ReceiveMessage(evt) {
    
    action = evt.data.Event;
    data = evt.data.Data;
        
    if (action == "collectionSelected") {
        Microsoft.Dynamics.NAV.InvokeExtensibilityMethod('WebshopCollectionSelected', [data]);
    }
    if (action == "saved") {
        Microsoft.Dynamics.NAV.InvokeExtensibilityMethod('SaveCompleted', [action,data]);
    }
    if (action == "changes") {
        Microsoft.Dynamics.NAV.InvokeExtensibilityMethod('UnsavedChanges', [true]);
    }
    if (action == "nochanges") {
        Microsoft.Dynamics.NAV.InvokeExtensibilityMethod('UnsavedChanges', [false]);
    }
    if (action == "changedetected") {
        Microsoft.Dynamics.NAV.InvokeExtensibilityMethod('ChangeDetected', null);
    }
    if (action == "fileuploaded") {
        Microsoft.Dynamics.NAV.InvokeExtensibilityMethod('WebshopItemFileUploaded', [data]);
    }
    if (action == "ChangeActionButton") {
        Microsoft.Dynamics.NAV.InvokeExtensibilityMethod('ChangeActionButton', [data.ActionButton,data.Enabled]);
    }

} // End Function ReceiveMessage


if (!window['postMessage'])
    alert("oh crap");
else {
    if (window.addEventListener) {
        window.addEventListener("message", ReceiveMessage, false);
    }
    else {
        window.attachEvent("onmessage", ReceiveMessage);
    }
}

function RequestSave() {
    SendMessage(document.getElementById("iframe").contentWindow,"save","","*");
}

function RequestClose() {
    SendMessage(document.getElementById("iframe").contentWindow,"changes","","*");
}

function ExecuteAction(Action, Data) {
    SendMessage(document.getElementById("iframe").contentWindow,Action,Data,"*");
}

