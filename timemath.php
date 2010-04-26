#!/usr/bin/php
<?php

if (count($argv) < 4) {
	print "Usage: {$argv[0]} datetime1 OP datetime2\n";
	exit(1);
}
list(, $d1, $op, $d2) = $argv;


$d1 = strtotime($d1);
$d2 = strtotime($d2);
$diff = 0;

eval("\$diff = $d1 $op $d2;");
print "Diff = $diff s\n";
print "\t=> " . toHMS($diff) . "\n";

function toHMS($sec) {
	$parts = array();
	print "(mod)" . $sec % 60 . "\n";
	$parts[] = $sec % 60;
	for($count = 0;$sec > 1 && $count < 3; $count++) {
		print "$sec / 60 => ";
		$sec /= 60.0;
		print "(mod) " . ($sec % 60) . "\n";
		$parts[] = $sec % 60;
	}
	$parts = array_map(create_function('$x', 'return sprintf("%02d", $x);'), $parts);
	$ret = implode(':', array_reverse($parts));
	print "-> $ret\n";
	return $ret;
}

#assert(toHMS(50) == '00:50');
#assert(toHMS(120) == '00:02:00');

?>
