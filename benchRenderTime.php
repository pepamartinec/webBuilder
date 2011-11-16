<?php

$url     = 'http://dev-local/webBuilder/index_builder.php?item=/products';
$loopsNo = 100;
$results = array();

// run bench
$curl = curl_init( $url );
curl_setopt( $curl , CURLOPT_RETURNTRANSFER, true );

for( $i = 0; $i < $loopsNo; ++$i ) {
	$startTime = microtime( true );
	curl_exec( $curl );
	$endTime   = microtime( true );
	
	$results[] = $endTime - $startTime;
	sleep( 1 );
}

curl_close( $curl );



// compute some interesting values
$min = $results[0];
$max = 0;
$avg = 0;

foreach( $results as $result ) {
	$min = min( $result, $min );
	$max = max( $result, $max );
	$avg += $result;
}

$avg /= sizeof( $results );




// output results
echo "<table>\n";
foreach( $results as $i => $result ) {
	$time = formatTime( $result );
	
	echo "\t<tr><td>{$i}</td><td>{$time}</td></tr>\n";
}
echo "\t<tr><td colspan=\"2\" style=\"border-bottom: 2px solid #000;\"></td></tr>\n";

$time = formatTime( $min );
echo "\t<tr><td>min</td><td>{$time}</td></tr>\n";

$time = formatTime( $max );
echo "\t<tr><td>max</td><td>{$time}</td></tr>\n";

$time = formatTime( $avg );
echo "\t<tr><td>avg</td><td>{$time}</td></tr>\n";

echo "</table>";

function formatTime( $time )
{
	return number_format( $time*1000, 2, ',', ' ' ).' msec';
}