<?php
/*
 * Function requested by Ajax
 */
if (isset($_POST['func']) && !empty($_POST['func'])) {

	getCalender($_POST['year'], $_POST['month']);
}

/*
 * Get calendar full HTML
 */
function getCalender($year = '', $month = '')
{
	$dateYear = ($year != '') ? $year : date("Y");
	$dateMonth = ($month != '') ? $month : date("m");
	$date = $dateYear . '-' . $dateMonth . '-01';
	$currentMonthFirstDay = date("N", strtotime($date));
	$totalDaysOfMonth = cal_days_in_month(CAL_GREGORIAN, $dateMonth, $dateYear);
	$totalDaysOfMonthDisplay = ($currentMonthFirstDay == 7) ? ($totalDaysOfMonth) : ($totalDaysOfMonth + $currentMonthFirstDay);
	$boxDisplay = ($totalDaysOfMonthDisplay <= 35) ? 35 : 42;
?>
	<div id="calender_section">
		<div id="calender_section_top">
			<h2>
				<a href="javascript:void(0);" onclick="getCalendar('calendar_div','<?php echo date('Y', strtotime($date . ' - 1 Month')); ?>','<?php echo date('m', strtotime($date . ' - 1 Month')); ?>');">&#8249</a>
				<?php echo date("F", mktime(0, 0, 0, $dateMonth + 1, 0, 0));
				echo " " . $dateYear; ?>
				<a href="javascript:void(0);" onclick="getCalendar('calendar_div','<?php echo date('Y', strtotime($date . ' + 1 Month')); ?>','<?php echo date('m', strtotime($date . ' + 1 Month')); ?>');">&#8250</a>
			</h2>

			<ul>
				<li>Sun</li>
				<li>Mon</li>
				<li>Tue</li>
				<li>Wed</li>
				<li>Thu</li>
				<li>Fri</li>
				<li>Sat</li>
			</ul>
		</div>
		<div id="calender_section_bot">
			<ul>
				<?php
				$dayCount = 1;
				for ($cb = 1; $cb <= $boxDisplay; $cb++) {
					if (($cb >= $currentMonthFirstDay + 1 || $currentMonthFirstDay == 7) && $cb <= ($totalDaysOfMonthDisplay)) {
						//Current date
						$currentDate = $dateYear . '-' . $dateMonth . '-' . $dayCount;

						//Include db configuration file
						include 'dbConfig.php';
						//Get number of events based on the current date
						$total = $db->query("SELECT *  FROM rooms")->num_rows;

						$booked = $db->query("SELECT DISTINCT room_id FROM guests WHERE room_id>0 AND expected_checkin <='" . $currentDate . "' AND expected_checkout >='" . $currentDate . "'")->num_rows;
						$free = $total - $booked;
						$percent = ($booked / $total) * 100;
						//Define date cell color

						if (strtotime($currentDate) == strtotime(date("Y-m-d"))) {
							echo '<li date="' . $currentDate . '" class="current date_cell"   >';
						} elseif (strtotime($currentDate) < strtotime(date("Y-m-d"))) {
							echo '<li date="' . $currentDate . '" class="date_cell">';
						} elseif ($percent >= 80) {
							echo '<li date="' . $currentDate . '" class="red date_cell" >';
						} elseif ($percent >= 40) {
							echo '<li date="' . $currentDate . '" class="yellow date_cell" >';
						} else {
							echo '<li date="' . $currentDate . '" class="green date_cell" >';
						}
						//Date cell
						echo '<span>';
						echo $dayCount;
						echo '</span>';

						//Hover event popup
						echo  '<div id="date_popup_' . $currentDate . '" class="date_popup_wrap none">';
						echo '<div class="date_window">';
						echo (strtotime($currentDate) > strtotime(date("Y-m-d"))) ? '<div class="popup_event">Rooms Available:' . $free . ' </div>' : '';


						echo '</li>';
						$dayCount++;
				?>
					<?php } else { ?>
						<li><span>&nbsp;</span></li>
				<?php }
				}

				?>

			</ul>
		</div>
	</div>


	<script type="text/javascript">
		function getNextday(date) {
			var tomorrow = new Date(date);
			tomorrow.setDate(tomorrow.getDate() + 1);
			return tomorrow.toJSON().slice(0, 10);
		}

		function getCalendar(target_div, year, month) {
			$.ajax({
				type: 'POST',
				url: 'functions.php',
				data: 'func=getCalender&year=' + year + '&month=' + month,
				success: function(html) {
					$('#' + target_div).html(html);
				}
			});
		}




		$(document).ready(function() {
			$('.date_cell').mouseenter(function() {
				date = $(this).attr('date');
				$(".date_popup_wrap").fadeOut();
				$("#date_popup_" + date).fadeIn();
			});
			$('.date_cell').mouseleave(function() {
				$(".date_popup_wrap").fadeOut();
			});

			$(document).click(function() {
				$('#event_list').slideUp('slow');
			});
		});
	</script>
<?php
}

/*
 * Get months options list.
 */
function getAllMonths($selected = '')
{
	$options = '';
	for ($i = 1; $i <= 12; $i++) {
		$value = ($i < 10) ? '0' . $i : $i;
		$selectedOpt = ($value == $selected) ? 'selected' : '';
		$options .= '<option value="' . $value . '" ' . $selectedOpt . ' >' . date("F", mktime(0, 0, 0, $i + 1, 0, 0)) . '</option>';
	}
	return $options;
}

/*
 * Get years options list.
 */
function getYearList($selected = '')
{
	$options = '';
	for ($i = date('Y'); $i <= date('Y') + 10; $i++) {
		$selectedOpt = ($i == $selected) ? 'selected' : '';
		$options .= '<option value="' . $i . '" ' . $selectedOpt . ' >' . $i . '</option>';
	}
	return $options;
}



?>