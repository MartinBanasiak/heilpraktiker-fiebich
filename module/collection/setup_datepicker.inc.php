<!--script type="text/javascript" src="/plugins/jquery/jquery.livequery.min.js"></script>

<script type="text/javascript" src="/plugins/qtip/jquery.qtip.min.js"></script>

<script type="text/javascript" src="/plugins/jqueryui/1.11/jquery-ui.js"></script>
<link href="/plugins/jqueryui/1.11/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="/plugins/qtip/jquery.qtip.min.css" rel="stylesheet" type="text/css" /-->

<script>
	jQuery(function($){
        $.datepicker.regional['de'] = {clearText: 'löschen', clearStatus: 'aktuelles Datum löschen',
                closeText: 'schließen', closeStatus: 'ohne Änderungen schließen',
                currentText: 'heute', currentStatus: '',
                monthNames: ['Januar','Februar','März','April','Mai','Juni',
                'Juli','August','September','Oktober','November','Dezember'],
                monthNamesShort: ['Jan','Feb','Mär','Apr','Mai','Jun',
                'Jul','Aug','Sep','Okt','Nov','Dez'],
                monthStatus: 'anderen Monat anzeigen', yearStatus: 'anderes Jahr anzeigen',
                weekHeader: 'Wo', weekStatus: 'Woche des Monats',
                dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
                dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
                dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],
                dayStatus: 'Setze DD als ersten Wochentag', dateStatus: 'Wähle D, M d',
                dateFormat: 'dd.mm.yy', firstDay: 1, 
                initStatus: 'Wähle ein Datum', isRTL: false};
        $.datepicker.setDefaults($.datepicker.regional['<?php echo strtolower($GLOBALS["language"]['code']); ?>']);
});
</script>