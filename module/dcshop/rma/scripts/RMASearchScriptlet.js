/**
 * Created by Bauer on 22.01.2015.
 */
if(typeof jQuery == 'undefined'){
    document.write('<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></'+'script>');
}

$(document).ready(function() {
	var form = $('#form_rma_result_list'),
		trs = form.find('tr'),
		idInput,
		idInputs = $(trs).find('input.id_input'),
		inputID = 0,
		searchField = $('#input_rma_request'),
		searchFieldPrefill = $(searchField).attr('data-prefill'),
		searchFieldInitVal = $(searchField).val();
	if(!searchFieldInitVal) {
		$(searchField).val(searchFieldPrefill);
	}
		
	$(trs).find('td').each(function() {
		var thisTD  = $(this),
			thisTR = $(this).parent('tr'),
			idInput = $(thisTR).find('input.id_input').first(),
			inputID = $(idInput).val();
		$(thisTD).on('click',function() {
			$(idInput).prop("checked", "checked");
		});
		$(thisTD).on('dblclick',function() {		
			$(idInput).prop("checked", "checked");
			$(form).submit();
		});
	});
	$(searchField).on('focus',function() {
		var currVal = $(this).val();
		if(currVal == searchFieldPrefill) {
			$(searchField).val('');
		}
	}).on('blur',function() {
		var currVal = $(this).val();
		if(!currVal) {
			$(searchField).val(searchFieldPrefill);
		}
	});
});