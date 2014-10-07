<?php

/*
 *
 * Calculate infinite fraction that estimates the square root of 2
 *
 * This 6/2014 HackerRank challenge asks to list the expansions where the number of digits in the numerator
 * exceeds the number of digits in the denominator.  I completed this in an hour or two.
 *
 * The formula for the fraction that estimates the square root of 2 is:
 *
 * 1 +        1
 *     ---------------
 *     2 +      1
 *         -----------
 *         2 +    1
 *             -------
 *             2 + 1/2
 *
 *             ... etc.
 *
 * For the fractions, I used a 2-element array (kind of like a tuple in Python).
 *
 */

$handle = fopen("php://stdin", "r");
$N = trim(fgets($handle));

/*
my algebra worksheet:
    x = 2 + 1/2 = 5/2
    y = 2 + 1/x = 2 + 1/(5/2) = 2 + 2/5 = 12/5
    z = 2 + 1/y = 2 + 1/(12/5) = 2 + 5/12 = 29/12
    S = 1 + 1/z = 1 + 1/(29/12) = 1 + 12/29 = 41/29
*/

function CalculateExpansion($target) {
    $debug = 0;
    if ($debug) echo "*****Calculating expansion " . $target . "*****\n";
    $solution = [5, 2];
    if ($debug) Show($solution);
    for ($i = $target - 1; $i >= 2; $i--) {
        /* algebraically:  x = 2 + 1/y */
        $solution = [$solution[1], $solution[0]];  // reverse numerator and denominator
        $solution = [(2 * $solution[1]) + $solution[0], $solution[1]];  // add 2
        if ($debug) Show($solution);
    }
    /* algebraically:  x = 1 + 1/y */
    $solution = [$solution[1], $solution[0]];  // reverse numerator and denominator
    $solution = [$solution[1] + $solution[0], $solution[1]];  // add 1
    if ($debug) Show($solution);
    if (NumeratorLonger($solution)) echo $target . "\n";
}

function NumeratorLonger($solution) {
     return strlen($solution[0]) > strlen($solution[1]);
}

function Show($solution) {
    echo $solution[0] . "/" . $solution[1] . "\n";
}

for ($n = 2; $n <= $N; $n++) {
    CalculateExpansion($n);
}

?>
