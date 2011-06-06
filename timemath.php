#!/usr/bin/php
<?php

if (count($argv) < 4) {
	print "Usage: {$argv[0]} datetime1 OP datetime2\n";
	exit(1);
}
list(, $d1, $op, $d2) = $argv;


if ($op == '+') {
	// treat d2 as duration
	print "$d1 + ";
	$d1 = strtotime($d1) - strtotime('today');
	$d2 = toSec($d2);
	print "$d2 seconds\n";
	$d1 += $d2;
	print "\t => " . secToHMS($d1) . "\n";
} else {
	$d1 = strtotime($d1);
	$d2 = strtotime($d2);
	$diff = 0;

	eval("\$diff = $d1 $op $d2;");
	print "Difference = $diff seconds\n";
	print "\t=> " . secToHMS($diff) . "\n";
}

function toSec($hms) {
	$sec = 0;
	$parts = array_reverse(explode(':', $hms));
	if (count($parts) == 2) {
		// assume 1:00 is 1hr, and 1:00:00 is 1hr too..., so 1 min is 00:01:00
		array_unshift($parts, '00');
	}
	$mult = 1;
	foreach($parts as $p) {
		$sec += intval($p) * $mult;
		$mult *= 60;
	}
	return $sec;
}
function sprintMin($x) { return sprintf("%02d", $x); }

function secToHMS($sec) {
	$parts = array();
	$sperday = (24*60*60);
	$days = intval($sec / $sperday);
	$sec %= $sperday;
	for($i = 0; $i < 3; $i++) {
		$parts[] = $sec % 60;
		$sec /= 60;
	}
	if($days) {
		// rest divides into days
		$parts[] = $days . 'd';
	}
	$parts = array_reverse($parts);
	$parts = array_map('sprintMin', $parts);
	return implode(':', $parts);
}

#assert(toHMS(50) == '00:50');
#assert(toHMS(120) == '00:02:00');

?>
