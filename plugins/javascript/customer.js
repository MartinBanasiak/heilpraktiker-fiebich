//$(function(){
//    $("#gadget_1").each(function(){
//	$(this).hover(function(){
//		$(this).animate({width: "250px"}, {queue:false, duration:450});
//      },function() {
//		  $(this).animate({width: "30px"}, {queue:false, duration:450});
//	  });
//    });
//});
//
//$(function(){
//    $("#gadget_2").each(function(){
//	$(this).hover(function(){
//		$(this).animate({width: "250px"}, {queue:false, duration:450});
//      },function() {
//		  $(this).animate({width: "30px"}, {queue:false, duration:450});
//	  });
//    });
//});


$(function(){
	var initWidth = $(window).width();
	if(initWidth > 1600) {
		$('.background_center>img').css('width', initWidth);
	}
	$(window).resize(function() {
	   var newWidth = $(window).width();
	   if (newWidth > 1600) {
		   $('.background_center>img').css('width', newWidth);
	   }
	   if (newWidth <= 1600) {
		   $('.background_center>img').css('width', '');
	   }
	});
});


$(document).ready(function() {
	$('.slide_content').hide().end();
	$('.slide_header').click(function() {
		var currClass = $(this).attr('class').split(' ')[0];
		$(this).toggleClass(currClass + '_open');
		$(this).next().slideToggle('slow');
	});
	var noOfImg = $('#menu_1 div.itemlist11>a>img').length,
	loadedImg = 0;
	
	$('#menu_1 div.itemlist11>a>img').on('load',function() {
		++loadedImg;
		if(loadedImg == noOfImg){
			$('#menu_1 div.itemlist11>a>img').each(function() {
				var pic = $(this),
					picHeight = $(this).outerHeight(),
					par = $(pic).parents('a'),
					parHeight = $(par).height(),
					margin = Math.ceil((parHeight-picHeight)/2);
				console.log('picHeight:'+picHeight+' parHeight:'+parHeight+' margin:'+margin);
				$(pic).css('margin-top',margin+'px');	
			});
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
    data.image_config = JSON.parse($(dataDiv).data("image_config"));
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
                    newSuggestion
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