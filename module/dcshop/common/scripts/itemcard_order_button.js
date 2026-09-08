
$(document).ready(function() {

    $('.orderbox', '#itemcard_right').height($('.orderbox', '#itemcard_right').height());

    var itemcardOrderButtonWrappers = $('.itemcard_order_button_form_outer'),
        itemcardOrderRadioButtonWrappers = $('.itemcard_order_button_radio_wrapper_outer'),
        elsToHide = $('.itemcard_order_button_inactive .display_switch_block_itemcard_order_button_activ0e'),
        select = $('#itemcard_order_button_subsc_select.itemcard_select_clickable'),
        clickable = itemcardOrderButtonWrappers;

	/*$(elsToHide).each(function() {
	 $(this).show();
	 $(this).css('height',$(this).height());
	 $(this).hide();
	 });*/

    clickable = itemcardOrderButtonWrappers;
    if (typeof select != "undefined") {
        $(clickable).add(select);
    }
    if(select) $(clickable).add(select);



    var URL = window.location.href;
    if(URL.indexOf('customized=true') != -1 )
    {
        var forms =  $(clickable).find("form");
        $(forms).each(function(){
            var dataObject = $(this).data();
            if(dataObject.customized != null && dataObject.customized == 1)
            {
               // var neededClass=  $(this).find('div.itemcard_order_button_subsc_radio_wrapper_outer');
               // var divId =  $(neededClass)
              $('#itemcard_order_button_subsc_radio_wrapper_outer_1').trigger('click');
            }
        })
    }

    $(clickable).on('click',function() {

        var thisWrapper = $(this),
            thisWrapperActive = $(thisWrapper).hasClass('itemcard_order_button_active'),
            siblings = $('.itemcard_order_button_form_outer').not(thisWrapper),
            thisRadioButton = $(thisWrapper).find('input[type=radio]').first(),
            siblingRadios = $(siblings).find('input[type=radio]'),
            thisSelect = $(thisWrapper).find('select'),
            siblingSelects = $(siblings).find('select'),
            thisAnimationTargets = $(thisWrapper).find('.itemcard_order_button_input_wrapper_outer, ' +
                'itemcard_order_button_vat_ship_notice_wrapper, .itemcard_order_subsc_select_wrapper'),
            siblingAnimationTargets = $(siblings).find('.itemcard_order_button_input_wrapper_outer, ' +
                'itemcard_order_button_vat_ship_notice_wrapper, .itemcard_order_subsc_select_wrapper'),
            customizationWrapper = $('*[data-customizable="1"]');


        if(!thisWrapperActive) {
            $(siblingRadios).removeAttr('checked');
            $(thisRadioButton).prop('checked','true');
            $(siblingAnimationTargets).slideUp('slow', function() {
                $(thisAnimationTargets).slideDown('slow', function() {
                    $(siblings).removeClass('itemcard_order_button_active').addClass('itemcard_order_button_inactive');
                    $(siblings).stop(true,true).addClass('itemcard_order_button_inactive').removeClass('itemcard_order_button_active');
                    $(siblings).find('.display_switch_block_itemcard_order_button_active').attr( "style", "" );
                    $(thisWrapper).removeClass('itemcard_order_button_inactive').addClass('itemcard_order_button_active');
                    $(thisWrapper).stop(true,true).addClass('itemcard_order_button_active').removeClass('itemcard_order_button_inactive');
                });
            });


            
            $(thisWrapper).find('.display_switch_block_itemcard_order_button_active').attr( "style", "" );
            if(thisSelect) {
                $(thisSelect).removeClass('itemcard_select_clickable');
                $(thisSelect).removeAttr('disabled');
            }
            if(siblingSelects) {
                $(siblingSelects).each(function() {
                    $(this).attr('disabled','disabled');
                    $(this).addClass('itemcard_select_clickable');
                });
            }
        }
         var form = $(thisWrapper).find('form');
          var dataObject = form.data();
          if(dataObject.customized != null && dataObject.customized == 1)
          {
              $(".itemcard_middle_container").hide();
              $(".itemcard_middle_container_customize").show();

              /*var URL =  window.location.href;
              if(URL.indexOf('customized=true') <= 0 )
              {
                  URL =  window.location.href.split('?')[0]
                  URL += "?card="+$("[name='item_id']").val();
                  URL += '&customized=true';
                  window.location.href = URL;
              }*/
          }
          else
          {
              $(".itemcard_middle_container_customize").hide();
              $(".itemcard_middle_container").show();

              /*var URL =  window.location.href;

              if(URL.indexOf('customized=true') != -1)
              {
                  URL =  window.location.href.split('?')[0]
                  URL += "?card="+$("[name='item_id']").val();
                  URL = URL.replace('&customized=true','');
                  window.location = URL;
              }*/

          }

    });
});

$(document).ready(function() {

    $('.item_qty_input_wrapper_inner').off('click','.spinner_up[data-disabled!="disabled"]').on('click','.spinner_up[data-disabled!="disabled"]',function(e) {
        currInput = $(this).closest('.item_qty_input_wrapper_inner').find('#item_qty');
        currMin = parseFloat(currInput.attr('min'));
        currMax = parseFloat(currInput.attr('max'));
        currStep = parseFloat(currInput.attr('step'));
        currVal = parseFloat(currInput.val());
        newVal = currVal + currStep;
        if(!isNaN(newVal) && (newVal <= currMax) && (newVal >= currMin)) {
            currInput.val(newVal);
        }
    });

    $('.item_qty_input_wrapper_inner').off('click','.spinner_down[data-disabled!="disabled"]').on('click','.spinner_down[data-disabled!="disabled"]',function(e) {
        currInput = $(this).closest('.item_qty_input_wrapper_inner').find('#item_qty');
        currMin = parseFloat(currInput.attr('min'));
        currMax = parseFloat(currInput.attr('max'));
        currStep = parseFloat(currInput.attr('step'));
        currVal = parseFloat(currInput.val());
        newVal = currVal - currStep;
        if(!isNaN(newVal) && (newVal <= currMax) && (newVal >= currMin)) {
            currInput.val(newVal);
        }
    });

    $('.subscription_item_qty_input_wrapper_inner').off('click','.spinner_up[data-disabled!="disabled"]').on('click','.spinner_up[data-disabled!="disabled"]',function(e) {
        currInput = $(this).closest('.subscription_item_qty_input_wrapper_inner').find('#subscription_item_qty');
        currMin = parseFloat(currInput.attr('min'));
        currMax = parseFloat(currInput.attr('max'));
        currStep = parseFloat(currInput.attr('step'));
        currVal = parseFloat(currInput.val());
        newVal = currVal + currStep;
        if(!isNaN(newVal) && (newVal <= currMax) && (newVal >= currMin)) {
            currInput.val(newVal);
        }
    });

    $('.subscription_item_qty_input_wrapper_inner').off('click','.spinner_down[data-disabled!="disabled"]').on('click','.spinner_down[data-disabled!="disabled"]',function(e) {
        currInput = $(this).closest('.subscription_item_qty_input_wrapper_inner').find('#subscription_item_qty');
        currMin = parseFloat(currInput.attr('min'));
        currMax = parseFloat(currInput.attr('max'));
        currStep = parseFloat(currInput.attr('step'));
        currVal = parseFloat(currInput.val());
        newVal = currVal - currStep;
        if(!isNaN(newVal) && (newVal <= currMax) && (newVal >= currMin)) {
            currInput.val(newVal);
        }
    });

});

$(document).ready(function() {

    var select = $('#itemcard_order_button_subsc_select'),
        outer = $('#itemcard_order_button_form_subsc_outer'),
        qtyInput = $(outer).find('.item_qty_input_wrapper_inner input[type=number]'),
        savingsDiv = $('.itemcard_order_button_subscription_savings_wrapper .itemcard_order_button_subscription_savings_text'),
        priceDiv = $(outer).find('.itemcard_order_button_cust_price_wrapper');

    if(select) {
        $(select).on('change',function() {
            var minQty = parseFloat($(this).find(':selected').data('minqty')),
                maxQty = parseFloat($(this).find(':selected').data('maxqty')),
                qtyStep = parseFloat($(this).find(':selected').data('qtystep')),
                savingsStr = $(this).find(':selected').data('savings'),
                priceStr = $(this).find(':selected').data('price');

            $(qtyInput).attr('min',minQty);
            $(qtyInput).attr('max',maxQty);
            $(qtyInput).attr('step',qtyStep);
            $(savingsDiv).text(savingsStr);
            $(priceDiv).text(priceStr);
        });
    }

});