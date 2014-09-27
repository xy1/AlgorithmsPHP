<?php

/*
 * Choose strategic Tic Tac Toe move for any given board arrangement.
 * This would be the core strategy logic of a computer Tic Tac Toe game.
 *
 * I spent one long evening on this.
 *
 * The artificial intelligence not only examines the current board, but also models
 * all possible moves that the opponent could take (after each of our possible moves),
 * and factors those into the decision.
 *
 * I got the idea for using octal notation (4 for an X and 1 for an O) based on
 * Linux file permissions (4 for Read, 2 for Write, and 1 for Execure), allowing
 * me to mathematically sum any row or column without having to check each slot.
 *
 * If I get more time at some point, I might have it model 2, 3, or X moves into the
 * future, as well as build it into a full-fledged game.  It would also be fun to
 * allow 4x4 or other custom board sizes.  We could also have custom symbols other
 * than Xs or Os (or in this case, 4s and 1s).
 *
 */

class Board {
	// board values (use octal notation)
	// 	0: slot is empty
	//	1: slot is occupied by opponent
	//	4: slot is occupied by us

	private $matrix;
	private $matrix_1st_move;
	private $matrix_2nd_move;
	private $debug = 0;

	function __construct($matrix) {
		$this->matrix = $matrix;

		// initialize matrix if not passed as parameter
		if (! $matrix) {
			for ($i = 0; $i < 3; $i++) {
				for ($j = 0; $j < 3; $j++) {
					$this->matrix[$i][$j] = 0;
				}
			}
		}
	}

	function ChooseMove() {
		$best_option_so_far = array('move' => '', 'points' => -1000000);
		for ($i = 0; $i < 3; $i++) {
			for ($j = 0; $j < 3; $j++) {
				if ($this->matrix[$i][$j] == 0) {  // if empty slot
					$this->matrix_1st_move = $this->matrix;  // copy current matrix into temp matrix
					$this->matrix_1st_move[$i][$j] = 4;  // we take slot in temp matrix

					// evaluate what opponent would do next (worst-case/lowest points for us)
					$opponent_best_option_so_far_points = 1000000;
					for ($k = 0; $k < 3; $k++) {
						for ($l = 0; $l < 3; $l++) {
							// copy current temp matrix into 2nd temp matrix
							$this->matrix_2nd_move = $this->matrix_1st_move;
							if ($this->matrix_2nd_move[$k][$l] == 0) {  // if empty slot
								// have opponent take slot in 2nd temp matrix
								$this->matrix_2nd_move[$k][$l] = 1;
								$points = $this->RateBoard($this->matrix_2nd_move);
								// if worse for us
								if ($points < $opponent_best_option_so_far_points)
									$opponent_best_option_so_far_points = $points;
							}
						}
					}

					if ($this->debug)
						echo "move " . $i . "," . $j . " would be worth " .
							$opponent_best_option_so_far_points . " points\n";

					// assign new best move, if warranted
					if ($opponent_best_option_so_far_points > $best_option_so_far['points'])
						$best_option_so_far = array('move' => $i . ',' . $j,
							'points' => $opponent_best_option_so_far_points);
				}
			}
		}
		return $best_option_so_far;
	}

	function RateBoard($matrix_checking) {
		// evaluate all rows, columns, and diagonals on board and calculate a total score

		$points = 0;

		// check all 3 columns
		for ($i = 0; $i < 3; $i++) {
			$column_total = array_sum($matrix_checking[$i]);
			$points += $this->RateRow($column_total);
			if ($this->debug == 2) echo "(" . $column_total . "->" . $this->RateRow($column_total) . ")";
		}

		// check all 3 rows
		for ($j = 0; $j < 3; $j++) {
			$row_total = $matrix_checking[0][$j] + $matrix_checking[1][$j] + $matrix_checking[2][$j];
			$points += $this->RateRow($row_total);
			if ($this->debug == 2) echo "(" . $row_total . "->" . $this->RateRow($row_total) . ")";
		}

		// check both diagonals

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

	function TakeMove($move) {
		$parsed_move = explode(",", $move['move']);
		$new_x = $parsed_move[0];
		$new_y = $parsed_move[1];
		$this->matrix[$new_x][$new_y] = 4;
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

$Board = new Board(
	array(
		array(4, 4, 1),
		array(0, 0, 0),
		array(0, 0, 1),
	)
);

// display original board
echo "Original board:\n";
$Board->Display();

// choose best move
$move = $Board->ChooseMove();
echo "Best move would be: " . $move['move'] . "\n";

// assign best move to board
$Board->TakeMove($move);

// display new board
echo "New board:\n";
$Board->Display();

/*

sample output 1:

	Original board:
	4 0 1 
	4 0 0 
	0 0 1 
	Best move would be: 2,0
	New board:
	4 0 1 
	4 0 0 
	4 0 1 

sample output 2:

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
