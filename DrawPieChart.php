<?php

/*
 *
 * Draw a pie chart using Scalable Vector Graphics (SVG).
 * Pass it data values and it will automatically calculate and render the chart.
 *
 * This is a component in a custom library for a data analytics application that I built for work.
 * I spent about 8 hours writing and testing this to make a workable version.
 *
 * The production version of this allows passing the container size in a responsive design approach.
 *
 */

class PieChart {

	// data elements
	private $data;
	private $total_amount;
	private $html;

	// geometric elements
	private $container_height = 400;
	private $container_width = 400;
	private $radius = 100;

	// geometric elements, internal
	private $center_x;
	private $center_y;
	private $position;  // point on perimeter, expressed as % of circumference, used when iterating items

	function __construct($data) {
		// parameter "data" is multi-dimensional array
		// 	field 0: item description (ex. "Whatever")
		//	field 1: item amount (ex. 12345)
		$this->data = $data;		

		// extract amounts into separate array for sorting and totalling
		foreach ($this->data as $k => $fields) {
			$amounts[] = $fields[1];
		}
		
		// sort data highest to lowest by amounts
		array_multisort($amounts, SORT_DESC, SORT_NUMERIC, $this->data);
		
		// calculate total of amounts
		$this->total_amount = array_sum($amounts);

		// calculate center of circle
		$this->center_x = ($this->container_width / 2);
		$this->center_y = ($this->container_height / 2);
	}

	function Draw($title) {
		$this->html = "<svg height=" . $this->container_height . " width=" . $this->container_width . " >";
		$this->html .= "<circle class='pie' r=" . $this->radius . " cx=" . $this->center_x . " cy=" . $this->center_y . "' />";

		$this->position = 0;  // start at 0%		
		foreach ($this->data as $k => $fields) {  // each slice
			// calculate size of slice - ex. 0.25 (25%)
			$slice_size = $fields[1] / $this->total_amount;
	
			// calculate endpoint based on size of slice
			$position_to = $this->position + $slice_size;
			
			if ($slice_size >= 0.01) {  // suppress slices less than 1% in size
				$this->DrawSlice($this->position, $position_to, $fields[0] . ' ' . $fields[1]);
			}
			
			// set position to endpoint for next slice
			$this->position = $position_to;
		}

		$this->WriteTitle($title);
		$this->html .= "</svg>";
		return $this->html;
	}
	
	function DrawSlice($from, $to, $label) {
		$size = ($to - $from);
		$mid = ($from + $to) / 2;

		// calculate points for slice
		$points_x = array();
		$points_y = array();
		$count = 0;
		// points on the perimeter bordered by slice
		for ($i = $from; $i <= $to; $i = $i + 0.01) {  // iterate through span of slice
			$radians = $i * pi() * 2;
			$points_x[$count] = $this->center_x + ($this->radius * cos($radians));
			$points_y[$count] = $this->center_y + ($this->radius * sin($radians));
			$count++;
		}

		// calculate position for label on perimeter in center of slice
		$radians = $mid * pi() * 2;
		$label_x = $this->center_x + ($this->radius * cos($radians));
		$label_x = $label_x - ((strlen($label) / 2) * 6);
		$label_y = $this->center_y + ($this->radius * sin($radians));

		// draw slice
		$this->html .= "<polygon class='pie_slice' " .
			" points='" . $this->center_x . "," . $this->center_y . " ";
		for ($i = 0; $i <= $count; $i++) {
			$this->html .= $points_x[$i] . "," . $points_y[$i] . " ";
		}
		// fade opacity to visually distinguish slices
		// based on distance around curve from slice's midpoint (x%) to the start point (0%). 
		$this->html .= "' style='opacity:" . (1.00 - ( $mid * 1.0)) . ";' />";

		// draw label
		$this->html .= "<text class=pie_chart_slice_label " .
			" x=" . $label_x . " y=" . $label_y .
			">" . $label . "</text>";
	}
	
	function WriteTitle($title) {
		// center title below chart
		$this->html .= "<text class=pie_chart_title " .
			" x=" . ( ($this->container_width / 2) - (strlen($title) / 2) * 9 ) .
			" y=" . ($this->container_height - 9) . " " .
			">" . $title . "</text>";
	}
}


// define sample data
$data = array(
	array('Alaska', 849),
	array('New York', 1122),
	array('California', 2345),
	array('Texas', 445),
	array('Colorado', 1833),
);

$piechart = new PieChart($data);
$html = $piechart->Draw('Sales Month-to-Date by State');
echo $html;

?>

<style>
/* sample style definitions for illustration purposes */
.pie {
	fill: #FFFFFF;
	opacity: 0.66;
}
.pie_chart_title {
	font-family: 'Verdana';
	font-size: 18px;
	fill: #224E00;
	font-weight: 600;
}
.pie_slice {
	fill: #BD854B;
	stroke-width: 0;
	stroke: #2F5B00;
}
.pie_chart_slice_label {
	font-family: 'Verdana';
	font-size: 12px;
	fill: #2F5B00;
}
</style>
