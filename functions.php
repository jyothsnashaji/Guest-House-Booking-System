<?php
include 'header.php';
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
				<a href="javascript:void(0);" style="float:left;" onclick="getCalendar('calendar_div','<?php echo date('Y', strtotime($date . ' - 1 Month')); ?>','<?php echo date('m', strtotime($date . ' - 1 Month')); ?>');">&#8249</a>
				<?php echo date("F", mktime(0, 0, 0, $dateMonth + 1, 0, 0));
				echo " ".$dateYear; ?>
				<a href="javascript:void(0);"  style="float:right;" onclick="getCalendar('calendar_div','<?php echo date('Y', strtotime($date . ' + 1 Month')); ?>','<?php echo date('m', strtotime($date . ' + 1 Month')); ?>');">&#8250</a>
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
						if ($dayCount < 10)
							$currentDate = $dateYear . '-' . $dateMonth . '-0' . $dayCount;
						else
							$currentDate = $dateYear . '-' . $dateMonth . '-' . $dayCount;


						include 'dbConfig.php';
						//Get number of events based on the current date
						$total = $db->query("SELECT *  FROM rooms")->num_rows;

						$booked = $db->query("SELECT DISTINCT room_id FROM guests WHERE room_id>0 AND expected_checkin <='" . $currentDate . "' AND expected_checkout >='" . $currentDate . "'")->num_rows;
						$free = $total - $booked;
						$percent = ($booked / $total) * 100;
						//Define date cell color

						if (strtotime($currentDate) == strtotime(date("Y-m-d"))) {
							echo '<li date="' . $currentDate . '"  id="' . $currentDate . '" class="current date_cell" >';
						} elseif (strtotime($currentDate) < strtotime(date("Y-m-d"))) {
							echo '<li date="' . $currentDate . '"  id="' . $currentDate . '" class="date_cell">';
						} elseif ($percent >= 80) {
							echo '<li date="' . $currentDate . '"  id="' . $currentDate . '" class="red date_cell" onclick="update(this)">';
						} elseif ($percent >= 40) {
							echo '<li date="' . $currentDate . '"  id="' . $currentDate . '" class="yellow date_cell" onclick="update(this)">';
						} else {
							echo '<li date="' . $currentDate . '"  id="' . $currentDate . '" class="green date_cell" onclick="update(this)">';
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
		function update(ele) {
			var date = new Date();
			date = ele.getAttribute("date", 0);
			if (ele.classList.contains("selected")) {
				nextDay = getNextday(date);
				if (update.checkout == getNextday(update.checkin)) {
					document.getElementById(update.checkin).classList.remove("selected");
					document.getElementById(update.checkout).classList.remove("selected");
					document.getElementById("checkin").value = null;
					document.getElementById("checkout").value = null;
					update.checkin = null;
					update.checkout = null;
				} else {
					if (date == update.checkin) {
						update.checkin = nextDay;
						document.getElementById(date).classList.remove("selected");
						document.getElementById("checkin").value = nextDay;
					} else {
						if (date == getNextday(update.checkin)) {
							document.getElementById("checkin").value = null;
							document.getElementById("checkout").value = null;
							document.getElementById(update.checkin).classList.remove("selected");
							update.checkin = null;
							prevDay = null;
						} else {
							prevDay = getPrevday(date);
							document.getElementById("checkout").value = prevDay;

						}
						while (date <= update.checkout) {
							document.getElementById(date).classList.remove("selected");
							date = getNextday(date);

						}
						update.checkout = prevDay;
					}
				}
			} else {
				if (typeof update.checkin == 'undefined' || update.checkin == null) {
					nextDay = getNextday(date);
					document.getElementById("checkin").value = date;
					document.getElementById("checkout").value = nextDay;
					update.checkin = date;
					update.checkout = nextDay;
					ele.classList.add("selected");
					document.getElementById(nextDay).classList.add("selected");
				} else {
					if (date < update.checkin) {
						document.getElementById("checkin").value = date;
						nextDay = date;
						while (nextDay < update.checkin) {
							document.getElementById(nextDay).classList.add("selected");
							nextDay = getNextday(nextDay);
						}
						update.checkin = date;
					} else {
						document.getElementById("checkout").value = date;
						nextDay = getNextday(update.checkout);
						while (nextDay <= date) {
							document.getElementById(nextDay).classList.add("selected");
							nextDay = getNextday(nextDay);
						}
						update.checkout = date;
					}
				}
			}
			validateCheckin(28); ////////make variable
			validateCheckout(7);
		}

		function getNextday(date) {
			var tomorrow = new Date(date);
			tomorrow.setDate(tomorrow.getDate() + 1);
			return tomorrow.toJSON().slice(0, 10);
		}

		function getPrevday(date) {
			var yesterday = new Date(date);
			yesterday.setDate(yesterday.getDate() - 1);
			return yesterday.toJSON().slice(0, 10);
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


		});
	</script>
<?php
}



?>