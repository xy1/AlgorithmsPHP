<?php

/*
 *
 * Rotate a square matrix, of any size, clockwise or counterclockwise 90 degrees
 *
 * This was a fun exercise inspired by an idea that I saw online.
 * I spent 3 or 4 hours writing this one evening.
 *
 * I originally tried a "layers" approach, but then stumbled onto a quicker approach
 * using simple formulas to transform the X and Y coordinates.
 *
 */

class Matrix {
	private $matrix;
	private $matrix_new;
	private $debug = 1;
	private $LENGTH_OF_SIDE;
	private $center_x;
	private $center_y;

	function __construct($matrix) {
		$this->matrix = $matrix;

		// assign length of side from parameter
		$this->LENGTH_OF_SIDE = count($matrix);

		// show length of side
		if ($this->debug) echo "Length of side is: " . $this->LENGTH_OF_SIDE . "\n";

		// if not passed in instantiation, initialize matrix with values counting left-to-right/top-to-bottom
		if (! $matrix) {
			$xcount = 0;
			for ($i = 0; $i < $this->LENGTH_OF_SIDE; $i++) {
				for ($j = 0; $j < $this->LENGTH_OF_SIDE; $j++) {
					$xcount++;
					$this->matrix[$i][$j] = $xcount;
				}
			}
		}

		// calculate center point
		$this->center_x = ($this->LENGTH_OF_SIDE - 1) / 2;
		$this->center_y = $this->center_x;

		// show center point
		if ($this->debug) echo "Center is: " . $this->center_x . "," . $this->center_y . "\n";
	}

	function RotateClockwise90Degrees() {
		for ($i = 0; $i < $this->LENGTH_OF_SIDE; $i++) {
			for ($j = 0; $j < $this->LENGTH_OF_SIDE; $j++) {

				// calculate distance of x and y from center point
				$x_distance = $this->center_x - $i;
				$y_distance = $this->center_y - $j;

				// transform by exchanging x and y distances and then negating the new x distance
				//    for example, a point 3 spaces behind the center and 2 spaces below the center
				//    will be moved to the new point that is 2 spaces above the center and 3 spaces
				//    behind the center
				$this->matrix_new[$this->center_x + ($y_distance * -1)][$this->center_y + $x_distance] =
					$this->matrix[$i][$j];
			}
		}
		$this->matrix = $this->matrix_new;
	}

	function RotateCounterclockwise90Degrees() {
		for ($i = 0; $i < $this->LENGTH_OF_SIDE; $i++) {
			for ($j = 0; $j < $this->LENGTH_OF_SIDE; $j++) {

				// calculate distance of x and y from center point
				$x_distance = $this->center_x - $i;
				$y_distance = $this->center_y - $j;

				// transform by exchanging x and y distances and then negating the new y distance
				//    for example, a point 3 spaces behind the center and 2 spaces below the center
				//    will be moved to the new point that is 2 spaces above the center and 3 spaces
				//    behind the center
				$this->matrix_new[$this->center_x + $y_distance][$this->center_y + ($x_distance * -1)] =
					$this->matrix[$i][$j];
			}
		}
		$this->matrix = $this->matrix_new;
	}

	function Display() {
		for ($i = 0; $i < $this->LENGTH_OF_SIDE; $i++) {
			for ($j = 0; $j < $this->LENGTH_OF_SIDE; $j++) {
				if ($this->matrix[$i][$j] <= 9) echo " ";
				echo $this->matrix[$i][$j] . "  ";
			}
			echo "\n";
		}
	}
}

/*
$Matrix = new Matrix(
	array(
		array(1, 2, 3),
		array(4, 5, 6),
		array(7, 8, 9),
	)
);
*/
$Matrix = new Matrix(
	array(
		array(9, 8, 7),
		array(6, 5, 4),
		array(3, 2, 1),
	)
);
echo "Original matrix:\n";
$Matrix->Display();
$Matrix->RotateClockwise90Degrees();
echo "Rotated clockwise:\n";
$Matrix->Display();
echo "Rotated Counterclockwise (back to original):\n";
$Matrix->RotateCounterclockwise90Degrees();
$Matrix->Display();
echo "Rotated Counterclockwise again:\n";
$Matrix->RotateCounterclockwise90Degrees();
$Matrix->Display();


/*
sample output:

Original matrix:
 1   2   3   4   5   6   7  
 8   9  10  11  12  13  14  
15  16  17  18  19  20  21  
22  23  24  25  26  27  28  
29  30  31  32  33  34  35  
36  37  38  39  40  41  42  
43  44  45  46  47  48  49  
Rotated clockwise:
43  36  29  22  15   8   1  
44  37  30  23  16   9   2  
45  38  31  24  17  10   3  
46  39  32  25  18  11   4  
47  40  33  26  19  12   5  
48  41  34  27  20  13   6  
49  42  35  28  21  14   7  
Rotated Counterclockwise (back to original):
 1   2   3   4   5   6   7  
 8   9  10  11  12  13  14  
15  16  17  18  19  20  21  
22  23  24  25  26  27  28  
29  30  31  32  33  34  35  
36  37  38  39  40  41  42  
43  44  45  46  47  48  49  
Rotated Counterclockwise again:
 7  14  21  28  35  42  49  
 6  13  20  27  34  41  48  
 5  12  19  26  33  40  47  
 4  11  18  25  32  39  46  
 3  10  17  24  31  38  45  
 2   9  16  23  30  37  44  
 1   8  15  22  29  36  43  
*/

?>
