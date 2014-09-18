<?php

/*
 * Rotate a square matrix of any size clockwise 90 degrees
 */

function RotateMatrixClockwise90Degrees($LENGTH_OF_SIDE) {
	// show length of side
	echo "Length of side is: " . $LENGTH_OF_SIDE . "\n";

	// calculate and show center point
	$center_x = ($LENGTH_OF_SIDE - 1) / 2;
	$center_y = $center_x;
	echo "Center is: " . $center_x . "," . $center_y . "\n";

	// initialize original matrix with values, counting left-to-right then top-to-bottom
	$xcount = 0;
	for ($i = 0; $i < $LENGTH_OF_SIDE; $i++) {
		for ($j = 0; $j < $LENGTH_OF_SIDE; $j++) {
			$xcount++;
			$matrix[$i][$j] = $xcount;
		}
	}
	
	// transform original matrix into new matrix
	for ($i = 0; $i < $LENGTH_OF_SIDE; $i++) {
		for ($j = 0; $j < $LENGTH_OF_SIDE; $j++) {
			// calculate distance of x and y from center point
			$x_distance = $center_x - $i;
			$y_distance = $center_y - $j;
			// transform by exchanging x and y distances and then negating the new x distance
			//    for example, a point 3 spaces behind the center and 2 spaces below the center will be moved to the new point
			//    that is 2 spaces above the center and 3 spaces behind the center
			$matrix_new[$center_x + ($y_distance * -1)][$center_y + $x_distance] = $matrix[$i][$j];
		}
	}
	
	// display matrices
	echo "Original matrix:\n";
	for ($i = 0; $i < $LENGTH_OF_SIDE; $i++) {
		for ($j = 0; $j < $LENGTH_OF_SIDE; $j++) {
			if ($matrix[$i][$j] <= 9) echo " ";
			echo $matrix[$i][$j] . "  ";
		}
		echo "\n";
	}
	echo "New matrix:\n";
	for ($i = 0; $i < $LENGTH_OF_SIDE; $i++) {
		for ($j = 0; $j < $LENGTH_OF_SIDE; $j++) {
			if ($matrix_new[$i][$j] <= 9) echo " ";
			echo $matrix_new[$i][$j] . "  ";
		}
		echo "\n";
	}
}

RotateMatrixClockwise90Degrees(7);

/*
sample output:

Length of side is: 7
Center is: 3,3
Original matrix:
 1   2   3   4   5   6   7  
 8   9  10  11  12  13  14  
15  16  17  18  19  20  21  
22  23  24  25  26  27  28  
29  30  31  32  33  34  35  
36  37  38  39  40  41  42  
43  44  45  46  47  48  49  
New matrix:
43  36  29  22  15   8   1  
44  37  30  23  16   9   2  
45  38  31  24  17  10   3  
46  39  32  25  18  11   4  
47  40  33  26  19  12   5  
48  41  34  27  20  13   6  
49  42  35  28  21  14   7 
*/

?>
