/**
 * Created by bauer on 20.02.2017.
 */

//Track active page view time
function trackActivePageViewTime() {

    var div = document.createElement('div'),
        dataset = div.dataset,
        hidden,
        visibilityChange,
        counterCallback;

    if (typeof document.hidden !== "undefined") { // Opera 12.10 and Firefox 18 and later support
        hidden = "hidden";
    } else if (typeof document.msHidden !== "undefined") {
        hidden = "msHidden";
    } else if (typeof document.webkitHidden !== "undefined") {
        hidden = "webkitHidden";
    }

    div.setAttribute('id', 'active_page_view_time');
    div.setAttribute('style', 'display:none;');
    document.getElementsByTagName('body')[0].appendChild(div);

    counterCallback = function () {
        return function () {
            if (!document[hidden]) {
                dataset.active_page_view_time = parseInt(dataset.active_page_view_time) + 1;
            }
        }();
    };

    div.style.display = 'none';
    dataset.active_page_view_time = 0;

    window.setInterval(counterCallback, 1000);
}


function getPageActiveViewDuration() {
    var activePageViewTime = 0,
        viewTimeDOMEl = $('#active_page_view_time');
    if (viewTimeDOMEl.length) {
        activePageViewTime = viewTimeDOMEl.data("active_page_view_time");
    }
    return activePageViewTime;
}

function collectAndSendTrackingData() {

    var eventDataDiv = $('#page_event_data'),
        trackingDataDiv = $('#tracking_data');

    if (trackingDataDiv.length && eventDataDiv.length) {

        var data = {},
            uniqueId = $(eventDataDiv).data("event_unique_id"),
            sessionIDName = $(trackingDataDiv).data('session_id_name'),
            nextEventUUID = $(eventDataDiv).data('next_event_unique_id'),
            visitorID = $(trackingDataDiv).data('visitor_id').toString().length > 0 ? parseInt($(trackingDataDiv).data('visitor_id')) : null,
            userID = $(trackingDataDiv).data('user_id').toString().length > 0 ? parseInt($(trackingDataDiv).data('user_id')) : null,
            customerID = $(trackingDataDiv).data('customer_id').toString().length > 0 ? parseInt($(trackingDataDiv).data('customer_id')) : null,
            itemID = $(trackingDataDiv).data('item_id').toString().length > 0 ? parseInt($(trackingDataDiv).data('item_id')) : null,
            categoryID = $(trackingDataDiv).data('category_id').toString().length > 0 ? parseInt($(trackingDataDiv).data('category_id')) : null,
            trackingAuthToken = $(trackingDataDiv).data("tracking_auth_token"),
            trackingApiRoot = $(trackingDataDiv).data("tracking_api_root"),
            trackingApiVersion = $(trackingDataDiv).data("tracking_api_version"),
            trackingApiResourceName = $(trackingDataDiv).data("tracking_api_resource_name"),
            url = '/' + trackingApiRoot + '/' + trackingApiVersion + '/' + trackingApiResourceName + '/';

        data.uuid = nextEventUUID;
        data.session_id_name = sessionIDName;
        data.event_type = 'PAGEVIEW_COMPLETE';
        data.event_data = {};
        data.event_data.referenced_pageview_event_uuid = uniqueId;
        data.event_data.active_page_view_time = getPageActiveViewDuration();
        data.event_data.linked_item_ids = collectItemLinksFromPage();
        data.visitor_id = visitorID;
        data.user_id = userID;
        data.customer_id = customerID;
        data.item_id = itemID;
        data.category_id = categoryID;


        $.ajax({
                type: 'POST',
                url: url,
                data: JSON.stringify(data),
                contentType: "application/json",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorize', 'Bearer ' + trackingAuthToken);
                }
            }
        ).done(function (data) {
            console.log(data);
        }).fail(function (xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
        });
    }

}
function collectItemLinksFromPage() {
    var itemLinkDIVSelector = '.itemlist3_top,.itemlist_image',
        itemLinkDIVs = $(itemLinkDIVSelector),
        re = /-p([0-9]+)\//,
        ids = [];

    itemLinkDIVs.each(function() {
        try {
            if ($(this)[0].hasAttribute("onclick")) {
                var clickTrigger = $(this)[0].attributes.onclick.nodeValue,
                    matches,
                    id;
                matches = clickTrigger.match(re);
                if (null == matches || typeof matches[1] === undefined) {
                    //do nothing
                } else {
                    id = parseInt(matches[1]);
                    ids.push(id);
                }
            }
        } catch(err) {
            console.log(err);
        }
    });
    return ids;
}

$(document).ready(function() {
    trackActivePageViewTime();
    window.addEventListener("beforeunload", function (e) {
        collectAndSendTrackingData();
    });
});


