/**
 * Created by lorenz on 18.07.2016.
 */
(function ($) {
    $.fn.progressbar = function (options) {
        var settings = $.extend({
            width: '300px',
            height: '25px',
            color: '#0ba1b5',
            padding: '0px',
            border: '1px solid #ddd',
            display: 'block'
        }, options);

        //Set css to container
        $(this).css({
            'width': settings.width,
            'border': settings.border,
            'overflow': 'hidden',
            'display': 'inline-block',
            'padding': settings.padding,
            'margin': '0px 10px 5px 5px',
            'display': settings.display
        });

        // add progress bar to container
        var progressbar = $("<div></div>");
        var valueSpan = $("<span></span>");
        progressbar.append(valueSpan);
        progressbar.css({
            'height': settings.height,
            'text-align': 'right',
            'vertical-align': 'middle',
            'color': '#fff',
            'width': '0px',
            'background-color': settings.color
        });

        valueSpan.css({
            'box-sizing' : 'border-box',
            'padding' : '2px',
            'line-height' : '24px'
        });

        valueSpan.html("0%");

        $(this).append(progressbar);

        this.progress = function (value) {
            var width = $(this).width() * value / 100;
            progressbar.stop().animate({
                width: width
            }, {
                duration: 2000,
                specialEasing: {
                    width: "linear"
                },
                step: function () {
                    valueSpan.html((parseFloat($(this).width()) / parseInt(settings.width) * 100).toFixed(2) + "%");
                },
                complete: function () {
                    valueSpan.html(value.toFixed(2) + "%");
                }
            });
        }
        return this;
    };

}(jQuery));