/**
 * Created by Bauer on 6/29/2015.
 */


function normalizePrice(price,separator) {
    var pdp = Math.floor(price),
        dp = (price - pdp) * 100,
        dps = '',
        pdps = '',
        priceStr = '';
    dps = (dp == 0) ? '-' : Math.round(dp).toString();
    pdps = pdp.toString();
    priceStr = pdps + separator + dps;
    return priceStr;
}
$(document).ready(function() {
    var qtyEl = $('div.itemcard_subscription_order_inner_qty input[type=number]'),
        noOfIntervalsEl = $('div.itemcard_subscription_order_inner_no_of_turns_select input[type=number]'),
        combined = qtyEl.add(noOfIntervalsEl);
    $(combined).on('keyup change',function() {
        var currEl = $(this),
            form = $(this).parents('form'),
            pricePerUnitRawEl = $(form).find('input[name=subscription_unit_price]'),
            initPriceEl = $(form).find('input[name=subscription_init_price]'),
            pricePerIntervalDescEl = $(form).find('.itemcard_subscription_order_price_per_interval_text_right'),
            totalPriceDescEl = $(form).find('.itemcard_subscription_order_price_total_text_right'),
            savingsTotalDescEl = $(form).find('.itemcard_subscription_order_savings_total_text_right'),
            pricePerUnitDescEl = $(form).find('.itemcard_subscription_order_price_per_unit_text_right'),
            savingsPerUnitDescEl = $(form).find('.itemcard_subscription_order_savings_per_unit_text_right'),
            qty = 1,
            noOfIntervals = 2,
            multiplier = 2,
            qtyEl = $('div.itemcard_subscription_order_inner_qty input[type=number]'),
            noOfIntervalsEl = $('div.itemcard_subscription_order_inner_no_of_turns_select input[type=number]'),
            initPrice = 0,
            priceStringRegex = new RegExp('[0-9]+[,.](?:-|[0-9]{2})'),
            decSeparatorRegex = new RegExp('(?:[0-9])([,.])(?=(?:-|[0-9]))');

        if(currEl.val() && currEl.val() > 0) {
            initPrice = initPriceEl ? parseFloat($(initPriceEl).val()) : 0.00;
            unitPrice = pricePerUnitRawEl ? parseFloat($(pricePerUnitRawEl).val()) : 0.00;
            qty = qtyEl ? parseInt($(qtyEl).val()) : 1;
            noOfIntervals = noOfIntervalsEl ? parseInt($(noOfIntervalsEl).val()) : 2;
            multiplier = qty * noOfIntervals;

            currPricePerIntervalAmntStr = pricePerIntervalDescEl ? priceStringRegex.exec($(pricePerIntervalDescEl).text()) ? priceStringRegex.exec($(pricePerIntervalDescEl).text())[0] : '' : '';
            currTotalAmntStr = totalPriceDescEl ? priceStringRegex.exec($(totalPriceDescEl).text())[0] : '';
            currSavingsTotalAmntStr = savingsTotalDescEl ? $(savingsTotalDescEl).text().match(priceStringRegex)[0] : '';
            currPricePerUnitAmntStr = pricePerUnitDescEl ? $(pricePerUnitDescEl).text().match(priceStringRegex) ? $(pricePerUnitDescEl).text().match(priceStringRegex)[0] : '' : '';
            currSavingsPerUnitDescEl = savingsPerUnitDescEl ? $(savingsPerUnitDescEl).text().match(priceStringRegex)[0] : '';
            decSeparator = currPricePerIntervalAmntStr ? currPricePerIntervalAmntStr.match(decSeparatorRegex)[1] : ',';

            newPricePerIntervalStr = normalizePrice(Math.round((qty * unitPrice), 2), decSeparator);
            newPriceTotal = Math.round((multiplier * unitPrice), 2);
            newPriceTotalStr = normalizePrice(newPriceTotal, decSeparator);
            unreducedTotal = Math.round((initPrice * multiplier), 2);
            newSavingsTotalStr = normalizePrice(Math.round((unreducedTotal - newPriceTotal), 2), decSeparator);

            newPricePerIntervalsInnerHTML = $(pricePerIntervalDescEl).text().replace(currPricePerIntervalAmntStr, newPricePerIntervalStr);
            pricePerIntervalDescEl.text(newPricePerIntervalsInnerHTML);

            newPriceTotalInnerHTML = $(totalPriceDescEl).text().replace(currTotalAmntStr, newPriceTotalStr);
            totalPriceDescEl.text(newPriceTotalInnerHTML);

            newSavingsTotalInnerHTML = $(savingsTotalDescEl).text().replace(currSavingsTotalAmntStr, newSavingsTotalStr);
            savingsTotalDescEl.text(newSavingsTotalInnerHTML);
        }
    });
});
