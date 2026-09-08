$(document).ready(function() {
	$('.slidecontent').find('.slidecontent_content_container').hide().end().find('.slidecontent_headline').click(function() {
		var currClass = $(this).attr('class').split(' ')[0];
		$(this).toggleClass(currClass + '_open');
		$(this).next().slideToggle('fast');
	});
});