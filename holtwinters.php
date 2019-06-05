<?php

# Observed data example
$values = array(27,21.5,29.5,11.5,28,25.5,25.5,19,19,5.5,6.5,6,5.5,23,25.5,24,17.5,26,23,6,5,9.5,44.5,16,18);

#$values=array(773,529,756,766,1037,970);

/* Calculate holt-winters parameters
   alpha: (1-alpha): weight to place on the most recent actual value (0 < alpha < 1)
   beta: (1-beta): weight to place on most recent trend (0 < beta < 1)
   tw: weight to place on the overall trend
*/
$hw_alpha=0.5;
$hw_beta=0.5;
$holt_tw=0.5;

print "\nHolt-winters\n";
/* Init holt-winters params */
$a2 = $values[1];
$t2 = $values[1] - $values[0];
$f2 = $a2 + ($t2 * $holt_tw);
for ($i=0;$i<count($values)-1;$i++) {
	// a3 = alpha * f2 + (1-alpha) * X3
	$a3 = $hw_alpha * $f2 + (1-$hw_alpha) * $values[$i+1];
	// t3 = beta * t2 + (1-beta) * (a3 - a2)
	$t3 = $hw_beta * $t2 + (1-$hw_beta) * ($a3 - $a2);
	$f3 = $a3 + ($t3 * $holt_tw);
	$a2=$a3;
	$t2=$t3;
	$f2=$f3;
}

print "Predicted: ".round($f3,3)."  - stddev ".round(standard_deviation($values),3)."\n";


print "\nExponential Smothing\n";
$alpha=0.5;
for ($i=0;$i<count($values)-1;$i++) {
	// f1 = X1
	if ($i==0) {
		$value1=$values[$i];
		$value2=$values[$i+1];
	}
	else {
		$value2=$values[$i+1];
	}
	$value1=($alpha * $value1) + (1 - $alpha) * $value2;
}
print "Predicted: ".round($value1,3)."\n";

print "\nDouble Exponential Smothing\n";
$alpha=0.5;
$gama=0.5;
for ($i=0;$i<count($values)-1;$i++) {
	// f1 = X1
	if ($i==0) {
		$b = $values[$i+1] - $values[$i];
		$value1 = $values[$i];
		$value2 = $values[$i+1];
	}
	else {
		$value2 = $values[$i+1];
	}
	$last_calc = $value1;
	$value1 = ($alpha * $value2) + (1 - $alpha) * ($value1 + $b);
	$b = $gama * ($value1 - $last_calc) + (1 - $gama) * $b;
}
print "Predicted: ".round($value1,3)."\n";

exit(0);

function standard_deviation($aValues)
{
    $fMean = array_sum($aValues) / count($aValues);
    //print_r($fMean);
    $fVariance = 0.0;
    foreach ($aValues as $i)
    {
        $fVariance += pow($i - $fMean, 2);

    }       
    $size = count($aValues) - 1;
    return (float) sqrt($fVariance)/sqrt($size);
}
?>
