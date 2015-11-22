<?php

/*

Maze looks like this:
	.X.X......X
	.X*.X.XXX.X
	.XX.X.XM...
	......XXXX.

Maze symbols:
	M is the entrance.
	* is the exit.
	X is a wall.
	. is a navigable passage.

Notes:

Only up, down, left, and right moves are allowed.
Forks refer to forks-in-the-road where multiple choices are possible.

Sample output in debug mode:
	Current position: 2 7
	Current position: 2 8
	Current position: 2 9
	We are at a fork and the choice we make is UP
	Current position: 1 9
	Current position: 0 9
	Current position: 0 8
	Current position: 0 7
	Current position: 0 6
	Current position: 0 5
	We are at a fork and the choice we make is DOWN
	Current position: 1 5
	Current position: 2 5
	Current position: 3 5
	Current position: 3 4
	Current position: 3 3
	We are at a fork and the choice we make is UP
	Current position: 2 3
	Current position: 1 3
	Reached goal
	Number of forks-in-the-road faced when solving this maze: 3

*/

class Maze {

    private $matrix = array();
    private $start_position = array();
    private $current_position = array();
    private $forks = array();
        // 0: x position, 1: y position: 2: times visited before, 3: original direction approached
    private $last_direction = '';
    private $matrix_rows;
    private $matrix_columns;
    private $debug = 1;
    private $move_count = 0;

    function __construct($input_matrix) {
        $this->matrix = $input_matrix;
        $this->matrix_rows = count($input_matrix);
        $this->matrix_columns = count($input_matrix[0]);
        $this->start_position = $this->GetStartPosition();
        $this->current_position = $this->start_position;
    }

    private function GetStartPosition() {
        foreach ($this->matrix as $key => $row) {
            foreach ($row as $subkey => $subvalue) {
                if ($subvalue == 'M') return array($key, $subkey);
            }
        }
    }

    function GetRoute() {

        if ($this->CheckIfDone()) {
            if ($this->debug) echo "Reached goal\n";
            return count($this->forks);  // reached goal
        }

        if ($this->debug) echo "Current position: " . $this->current_position[0] . " " .
                $this->current_position[1] . "\n";

        $available_directions = $this->GetAvailableDirections();
        $num_available_directions = count($available_directions);
        if ($num_available_directions == 0) {  // dead end
            if ($this->debug) echo "We reached a dead end and ";
            $last_fork = count($this->forks) - 1;
            if ($last_fork == -1) {
                if ($this->debug) echo "we move back to start...\n";
                $this->current_position = $this->start_position;  // go back to start
                $this->last_direction = '';
            }
            else {
                if ($this->debug) echo "we move back to last fork...\n";
                $this->current_position = $this->forks[$last_fork];  // go to last fork
                $this->last_direction = '';
            }
        }
        else if ($num_available_directions == 1) {
            $this->MoveDirection($available_directions[0]);
        }
        else if ($num_available_directions >= 2) {  // fork
            if ($this->debug) echo "We are at a fork ";
            $fork_key = $this->GetForkKey($this->current_position);
            if ($fork_key == -1) {
                $this->forks[] = $this->current_position;  // save record of fork
                $fork_key = $this->GetForkKey($this->current_position);  // then get (newly-assigned) key
                $this->forks[$fork_key][2] = 0;  // and set initial number of times visited
                $this->forks[$fork_key][3] = $this->last_direction;  // and set initial number of times visited
            }
            $how_many_times_visited = $this->forks[$fork_key][2];
            if ($how_many_times_visited > $num_available_directions) { // maxed out options for this fork
                unset($this->forks[$fork_key]);  // remove fork
                $previous_fork = count($this->forks) - 1;
                if ($previous_fork == -1) {
                    if ($this->debug) echo "we are maxed out and move back to start...\n";
                    $this->current_position = $this->start_position;  // go back to start
                    $this->last_direction = '';
                }
                else {
                    if ($this->debug) echo "we are maxed out and move back to previous fork " .
                            $previous_fork . "...\n";
                    $this->current_position = $this->forks[$previous_fork];  // go to previous fork
                    $this->last_direction = '';
                }
            }
            else {
                $choice = $available_directions[$how_many_times_visited];
                if ($this->debug) echo "and the choice we make is " . $choice . "\n";
                $this->MoveDirection($choice);
                $this->forks[$fork_key][2]++;
            }
        }

        $this->move_count++;
        if ($this->move_count >= 999999) die("We are aborting because we completed 999999 moves");

        return $this->GetRoute();
    }

    private function GetForkKey($position) {
        $key = 0;
        foreach ($this->forks as $fork) {
            if ($fork[0] == $position[0] && $fork[1] == $position[1]) return $key;
            $key++;
        }
        return -1;
    }

    private function CheckIfDone() {
        $cell = $this->matrix[$this->current_position[0]][$this->current_position[1]];
        if ($cell == '*') return true;
        else return false;
    }

    private function GetAvailableDirections() {
        $y = $this->current_position[0];
        $x = $this->current_position[1];
        $temp_last_direction = $this->last_direction;

        // if this is a fork, get original direction approached
        $fork_key = $this->GetForkKey($this->current_position);
        if ($fork_key != -1) $temp_last_direction = $this->forks[$fork_key][3];

        $available_directions = array();
        if ($y > 0 && $this->matrix[$y - 1][$x] != 'X' && $temp_last_direction != 'DOWN')
                $available_directions[] = 'UP';
        if ($y < $this->matrix_rows - 1 && $this->matrix[$y + 1][$x] != 'X' && $temp_last_direction != 'UP')
                $available_directions[] = 'DOWN';
        if ($x > 0 && $this->matrix[$y][$x - 1] != 'X' && $temp_last_direction != 'RIGHT')
                $available_directions[] = 'LEFT';
        if ($x < $this->matrix_columns - 1 && $this->matrix[$y][$x + 1] != 'X' && $temp_last_direction != 'LEFT')
                $available_directions[] = 'RIGHT';
        return $available_directions;
    }

    private function MoveDirection($direction) {
        $this->last_direction = $direction;
        $y = $this->current_position[0];
        $x = $this->current_position[1];
        if ($direction == 'UP') $y--;
        else if ($direction == 'DOWN') $y++;
        else if ($direction == 'RIGHT') $x++;
        else if ($direction == 'LEFT') $x--;
        $this->current_position = array($y, $x);
    }

}

// define maze

$raw_matrix = array(
	".X.X......X",
	".X*.X.XXX.X",
	".XX.X.XM...",
	"......XXXX."
);
$num_lines = count($raw_matrix);
$num_characters = strlen($raw_matrix[1]);
for ($line = 0; $line < $num_lines; $line++) {
	$raw_line = $raw_matrix[$line];
	for ($char = 0; $char < $num_characters; $char++) {
		$raw_matrix_array[$line][$char] = substr($raw_line,$char,1);
	}
}

// begin

$f = new Maze($raw_matrix_array);
$number_of_forks = $f->GetRoute();
echo "Number of forks-in-the-road faced when solving this maze: {$number_of_forks} \n";    

?>
