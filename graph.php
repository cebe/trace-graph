<?php

//print_r($argv);


$dir = __DIR__ . '/traces';

$edges = array();
foreach (glob($dir . '/*') as $filename) {
	$route = parseTrace(file($filename));
	$edges = array_merge($edges, route2graph($route));
}
renderDotFile($edges);



function renderDotFile($edges)
{
	echo "digraph network {\n";
	echo "\trankdir=LR;\n";
	echo "\toverlap=prism;\n";

	$arrows = array();
	foreach($edges as $e) {
		$arrows[] = "\t\"" . $e[0] . "\" -> \"" . $e[1] . "\";";
	}
	echo implode("\n", array_unique($arrows));

	echo "}\n";
}

function route2graph($route)
{
	$edges = array();
	$last = null;
	foreach($route as $layer) {
		if ($last !== null) {
			foreach($last as $serverA => $timeA) {
				foreach($layer as $serverB => $timeB) {
					$edges[] = array($serverA, $serverB);
				}
			}
		}
		$last = $layer;
	}
	return $edges;
}

function parseTrace($trace)
{
	$route = array();
	// http://www.regular-expressions.info/regexbuddy/ipaccurate.html
	$ip = '(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)';
	foreach($trace as $line) {
		$line = trim($line);

		if (($pos = strpos($line, ' ')) !== false) {
			$num = substr($line, 0, $pos);
			// ignore text lines
			if (is_numeric($num)) {
				$line = substr($line, $pos + 1);
				$servers = array();
				if (preg_match_all('/(' . $ip . ')\s+((?:\d+\.\d+\s+ms\s*|\*\s*)+)/', trim($line), $matches, PREG_SET_ORDER)) {
					foreach($matches as $match) {
						if (isset($servers[$match[1]])) {
							$servers[$match[1]] = array_merge($servers[$match[1]], parseTime($match[2]));
						} else {
							$servers[$match[1]] = parseTime($match[2]);
						}
					}
				}
				$route[$num] = $servers;
			}
		}
	}
	return $route;
}

function parseTime($time)
{
	return array_map('trim', explode(' ms', $time));
}
