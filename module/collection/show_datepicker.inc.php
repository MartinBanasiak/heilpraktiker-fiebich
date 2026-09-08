<?php
require_once MODULE_PATH . "collection/setup_datepicker.inc.php";
?>
<div id="datepicker_<?php echo $collection_setup['id']; ?>"></div>
	
	<script>
		var dates_<?php echo $collection_setup['id']; ?> = [];
	</script>
	<?php
		// datums feld herausfinden
		$query = "SELECT id from main_collection_setup_content where main_collection_setup_id = " . $collection_setup['main_collection_setup_id'] . " and type_id = 3 order by sorting asc limit 1";
		$result           = @mysqli_query($GLOBALS['mysql_con'], $query);
		$row = @mysqli_fetch_array($result);
		$dateId = $row['id'];
		
		// felder zum anzeigen im tooltip. Es werden nur normale textfelder benutzt (keine siteparts) und nur die, die auch in der uebersicht angezeigt werden
		$tipFields = array();
		$query = "SELECT * FROM main_collection_setup_content where main_collection_setup_id = " . $collection_setup['main_collection_setup_id'] . " and code <> 'end-date' and (type_id = 1 or type_id = 3) and is_teaser = 1 and fieldtype = 'standard' order by sorting asc";
		$result           = @mysqli_query($GLOBALS['mysql_con'], $query);
		while ($row = @mysqli_fetch_array($result)) {
                    $tipFields[$row['id']] = $row;
                }

		// Text fuer die einzelnen Tage sammeln.
		// Pro Tag koennen mehrere Events vorkommen, also: im datepicker nur ein Datum aber im tooltip mehrere Events
		$jsOutput = array();
		foreach ($collections as $collection_id => $collectionData) {  
			$line = $collection_lines[$collection_id][$dateId];
			$zeit = strtotime($line['data']);
                        
			if(!is_array($jsOutput[$zeit])) {
				$jsOutput[$zeit] = array();
			}

			$fullviewLink = get_link_to_navigation($collection_setup['main_collection_page_list_id']) . '?collection_id=' . $collection_id;
			
			$tipContent = array();
                        $tipContent[] = "<a href=\"" . $fullviewLink . "\">";
			foreach($tipFields as $fieldId => $fieldRow) {
				$tipLine = $collection_lines[$collection_id][$fieldId];
                                if($fieldRow['code'] == "start-date"){
                                    $tipStartdate = "<div class=\"" . $fieldRow['code'] . "\">" . $tipLine['data'] . "</div>";     
                                }elseif($fieldRow['code'] == "collection-location"){
                                    $tipLocation = "<div class=\"" . $fieldRow['code'] . "\">" . $GLOBALS["tc"]["eventtip_location"] . ": " . $tipLine['data'] . "</div>";     
                                }elseif($fieldRow['code'] == "collection-headline"){
                                    $tipHeadline = "<div class=\"" . $fieldRow['code'] . "\">" . addslashes($tipLine['data']) . "</div>";     
                                }else{
                                    $tipContent[] = "<div class=\"" . $fieldRow['code'] . "\">" . addslashes($tipLine['data']) . "</div>";          
                                }
			}
                        $tipContent[] = $tipStartdate;
                        $tipContent[] = $tipHeadline;
                        $tipContent[] = $tipLocation;
 			$tipContent = join($tipContent, "");
			$tipContent = $tipContent . "</a><hr>";
			$jsOutput[$zeit][] = $tipContent;
		}
		
		// JS Array mit den Events und tooltips fuellen
		$datesOutput = array();
		foreach($jsOutput as $zeit => $data) {
			$datesOutput[] = "dates_" . $collection_setup['id'] . ".push([new Date('" . date('m/d/Y', $zeit) . "'),new Date('" . date('m/d/Y', $zeit) . "'),'" . join("<hr />", $data) . "']);";
		}
	?>

  <script>

	<?php echo join("\n", $datesOutput); ?>
  
	function highlightDays(date) {
		for (var i = 0; i < dates_<?php echo $collection_setup['id']; ?>.length; i++) {
			if (dates_<?php echo $collection_setup['id']; ?>[i][0] <= date && dates_<?php echo $collection_setup['id']; ?>[i][1] >= date) {
		    	return [true, 'ui-state-highlight', dates_<?php echo $collection_setup['id']; ?>[i][2]];
		    }
		}
		return [true, ''];
	}
  
	$(function() {
    	$( "#datepicker_<?php echo $collection_setup['id']; ?>" ).datepicker({
        	beforeShowDay: highlightDays,
        	dateFormat: 'dd.mm.YYYY',
        	onSelect: function(date, inst){
				inst.inline = false;
			    $(".ui-datepicker-calendar .ui-datepicker-current-day").removeClass("ui-datepicker-current-day").children().removeClass("ui-state-active");
			    $(".ui-datepicker-calendar TBODY A").each(function(){
			    	if ($(this).text() == inst.selectedDay) {
			        	$(this).addClass("ui-state-active");
			        	$(this).parent().addClass("ui-datepicker-current-day");
			      	}
			    });
			}
    	});
  	});
  
	$('#datepicker_<?php echo $collection_setup['id']; ?> td[title]').livequery(function() {
		$(this).qtip({
			content: {
				text: $(this).attr('title').replace(/\|/g, '"'),
				title: {
		           text: 'Events',
		           button: 'Close'
		        }
			},
			hide: {
				fixed: true,
				delay: 1000
			},
			show: {
				solo: true,
				event: 'click'
			}
		});
	});
  </script>