<?php

/*
 * Choose strategic Tic Tac Toe move for any given board arrangement.
 *
 * The AI models 1 subsequent (worst-case) opponent's move when selecting best move.
 */

class Board {
	// board values
	// 	0: slot is empty
	//	1: slot is occupied by opponent
	//	4: slot is occupied by us

	public $matrix;
	private $matrix_temp;
	private $matrix_temp_2;
	private $debug = 0;

	function __construct() {
		// initialize matrix
		for ($i = 0; $i < 3; $i++) {
			for ($j = 0; $j < 3; $j++) {
				$this->matrix[$i][$j] = 0;
			}
		}
	}

	function ChooseMove() {
		$best_option_so_far = array('move' => '', 'points' => -1000000);
		for ($i = 0; $i < 3; $i++) {
			for ($j = 0; $j < 3; $j++) {
				if ($this->matrix[$i][$j] == 0) {  // if empty slot
					$this->matrix_temp = $this->matrix;  // copy current matrix into temp matrix
					$this->matrix_temp[$i][$j] = 4;  // we take slot in temp matrix

					// evaluate what opponent would do next (worst-case/lowest points for us)
					$opponent_best_option_so_far_points = 1000000;
					for ($k = 0; $k < 3; $k++) {
						for ($l = 0; $l < 3; $l++) {
							$this->matrix_temp_2 = $this->matrix_temp;  // copy current temp matrix into 2nd temp matrix
							if ($this->matrix_temp_2[$k][$l] == 0) {  // if empty slot
								$this->matrix_temp_2[$k][$l] = 1;  // have opponent take slot in 2nd temp matrix
								$points = $this->RateBoard($this->matrix_temp_2);
								if ($points < $opponent_best_option_so_far_points)  // if worse for us
									$opponent_best_option_so_far_points = $points;
							}
						}
					}

					if ($this->debug)
						echo "move " . $i . "," . $j . " would be worth " . $opponent_best_option_so_far_points . " points\n";

					// assign new best move, if warranted
					if ($opponent_best_option_so_far_points > $best_option_so_far['points'])
						$best_option_so_far = array('move' => $i . ',' . $j, 'points' => $opponent_best_option_so_far_points);
				}
			}
		}
		return $best_option_so_far;
	}

	function RateBoard($matrix_checking) {
		// evaluate all rows, columns, and diagonals on board and calculate a total score

		$points = 0;

		// check 3 columns
		for ($i = 0; $i < 3; $i++) {
			$column_total = array_sum($matrix_checking[$i]);
			$points += $this->RateRow($column_total);
			if ($this->debug == 2) echo "(" . $column_total . "->" . $this->RateRow($column_total) . ")";
		}

		// check 3 rows
		for ($j = 0; $j < 3; $j++) {
			$row_total = $matrix_checking[0][$j] + $matrix_checking[1][$j] + $matrix_checking[2][$j];
			$points += $this->RateRow($row_total);
			if ($this->debug == 2) echo "(" . $row_total . "->" . $this->RateRow($row_total) . ")";
		}

		// check 2 diagonals

		$diagonal_1_total = $matrix_checking[0][0] + $matrix_checking[1][1] + $matrix_checking[2][2];
		$points += $this->RateRow($diagonal_1_total);
		if ($this->debug == 2) echo "(" . $diagonal_1_total . "->" . $this->RateRow($diagonal_1_total) . ")";

		$diagonal_2_total = $matrix_checking[0][2] + $matrix_checking[1][1] + $matrix_checking[2][0];
		$points += $this->RateRow($diagonal_2_total);
		if ($this->debug == 2) echo "(" . $diagonal_2_total . "->" . $this->RateRow($diagonal_2_total) . ")";

		return $points;
	}

	function RateRow($line_sum) {
		// evaluate a "row" (or column or diagonal) and assign it a score

		// potential values:
		// 	1000 for each 3-in-a-row
		// 	-1000 for each opponent 3-in-a-row
		//	100 for each 2-in-a-row
		//	-100 for each opponent 2-in-a-row
		//	10 for each 1-in-a-row
		//	-10 for each opponent 1-in-a-row

		if ($line_sum == 12) return 1000;
		else if ($line_sum == 3) return -1000;
		else if ($line_sum == 8) return 100;
		else if ($line_sum == 2) return -100;
		else if ($line_sum == 4) return 10;
		else if ($line_sum == 1) return -10;
		else return 0;
	}

	function Display() {
		for ($i = 0; $i < 3; $i++) {
			for ($j = 0; $j < 3; $j++) {
				echo $this->matrix[$i][$j] . " ";
			}
			echo "\n";
		}
	}

	function ShowCoordinateSystem() {
		echo "0,0  0,1  0,2 \n";
		echo "1,0  1,1  1,2 \n";
		echo "2,0  2,1  2,2 \n";
	}
}

$Board = new Board();

// assign initial board selections
$Board->matrix = array(
	array(4, 4, 1),
	array(0, 0, 0),
	array(0, 0, 1),
);

// display original board
echo "Original board:\n";
$Board->Display();

// choose best move
$move = $Board->ChooseMove();
echo "Best move would be: " . $move['move'] . "\n";

// assign best move to board
$parsed_move = explode(",", $move['move']);
$new_x = $parsed_move[0];
$new_y = $parsed_move[1];
$Board->matrix[$new_x][$new_y] = 4;

// display new board
echo "New board:\n";
$Board->Display();

/*
sample output:

Original board:
4 0 1 
4 0 0 
0 0 1 
Best move would be: 2,0
New board:
4 0 1 
4 0 0 
4 0 1 

Original board:
4 4 1 
0 0 0 
0 0 1 
Best move would be: 1,2
New board:
4 4 1 
0 0 4 
0 0 1 

*/

?>
