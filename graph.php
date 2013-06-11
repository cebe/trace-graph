<?php

//print_r($argv);
//

if (isset($argv[1])) {
	$dir = $argv[1];
} else {
	$dir = __DIR__ . '/traces';
}

$resolveHosts = true;
//$resolveHosts = false;

$edges = array();
foreach (glob($dir . '/*') as $filename) {
	$route = parseTrace(file($filename));
	$edges = array_merge($edges, route2graph($route, $resolveHosts));
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

function route2graph($route, $resolveHosts)
{
	$edges = array();
	$last = null;
	foreach($route as $layer) {
		if ($last !== null) {
			if (empty($last)) {
				$serverA = '???';
				foreach($layer as $serverB) {
					$serverB = $resolveHosts ? $serverB->getHostName() : $serverB->ip;
					$edges[] = array($serverA, $serverB);
				}
			}
			foreach($last as $serverA) {
				$serverA = $resolveHosts ? $serverA->getHostName() : $serverA->ip;
				if (empty($layer)) {
					$edges[] = array($serverA, '???');
				}
				foreach($layer as $serverB) {
					$serverB = $resolveHosts ? $serverB->getHostName() : $serverB->ip;
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
							$servers[$match[1]]->time = array_merge($servers[$match[1]]->time, parseTime($match[2]));
						} else {
							$server = new Server();
							$server->ip = $match[1];
							$server->time = parseTime($match[2]);
							$servers[$match[1]] = $server;
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

class Server
{
	public $ip;
	public $time = array();
	public $start = false;
	public $end = false;


	private static $cache = array();
	public function getHostName()
	{
		$ip = $this->ip;
		if (!isset(static::$cache[$ip])) {
			static::$cache[$ip] = gethostbyaddr($ip);
		}
		return static::$cache[$ip];
	}
}
