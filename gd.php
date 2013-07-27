<?php
/*
Interesting Scribbles 

Modelled on dual pendulums with a rigid stick coming from each, joined by a hinge with a pen on that hinge. This should be what it draws. Makes some nice doodles. Funny surfaces can be made by including friction. Shapes mainly vary by varying speeds of sin functions. Set tmax to something silly to make a mess!

(* Assumed the positions of the pendulums stay fixed, so the x of one and the \
y of the other could be set to 0 *)
*/

$uniqid = 'tmp/'.uniqid('plot_');

function gen_sins()
	{
	
	$max = getrandmax();
	$total = 0;
	
	$return = '(';
	
	for($i=1; $i<rand(2,10); $i++)
		{
		$rand = (rand(1,5)/2);
		#$rand=1;
		$total += $rand;
		#$total=1;
		$return .= $rand.'*sin((1.5+'.($i/2*rand(0,2)).')*t + '.(0*rand(0,3)*pi()/2 + 0*rand(0,2)*rand()/$max).') + ';
		
		$retarr[] = array($rand,(1.5 + $i/2*rand(0,2)));
		}
	

	#Return 1/(total premultipliers+0.5) and array of premultiplier, internal multiplier.
	return $return = array((1/(floor($total)+0.5)), $retarr);	
	}

#echo gen_sins();
#echo 'FACE';
#die;

$sins1 = gen_sins();
$sins2 = gen_sins();


function fx($x2, $y1)
	{
	return ($x2*$x2*$x2*$x2 + ($x2*$x2)*($y1*$y1) + $y1*sqrt(-($x2*$x2)*($x2*$x2*$x2*$x2 + ($y1*$y1)*(-16 + $y1*$y1) + 2*($x2*$x2)*(-8 + ($y1*$y1)))))/(2*$x2*($x2*$x2 + $y1*$y1));
	}
	
function fy($x2, $y1)
	{
	return (($x2*$x2)*$y1 + $y1*$y1*$y1 + sqrt(-($x2*$x2)*($x2*$x2*$x2*$x2 + ($y1*$y1)*(-16 + $y1*$y1) + 2*($x2*$x2)*(-8 + $y1*$y1))))/(2*($x2*$x2 + $y1*$y1));
	}

#radius
$r = 2;

#run time
$tmax = 200;

#friction function
function c($t, $tmax)
	{
	return (1 - $t/$tmax);
	}

#x (0) + swinging function
function x($t, $tmax, $sins)
	{
	foreach($sins[1] as $sin)
		{
		$sin_sum += $sin[0]*sin($sin[1]*$t);
		} 
	
	return 1 + 0.1*c($t, $tmax)*$sins[0]*$sin_sum;
	}

for ($t=0.1; $t<$tmax; $t+=0.02)
	{
	$x[] = fx(x($t, $tmax, $sins1),x($t, $tmax, $sins2));
	$y[] = fy(x($t, $tmax, $sins1),x($t, $tmax, $sins2));
	}
/*
$fp = fopen('plot', 'w');
for($i=0; $i<count($x); $i++)
	{
	fwrite($fp, $x[$i]."\t".$y[$i]."\n");
	}
fclose($fp);

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$start_time=microtime_float();

$points = file_get_contents('plot');
$points = explode("\n", $points);
for($i=0; $i<count($points); $i++)
	{
	$points[$i] = explode("\t", $points[$i]);
	}
echo 'TIME: '.(microtime_float() - $start_time);
die;
*/

$max = array('x'=>max($x), 'y'=>max($y));
$min = array('x'=>min($x), 'y'=>min($y));


#Begin creating and drawing onto image
$height = 400;
$width = 400;
$color = 0x00FF0000; #Alpha, then RGB
$image = imagecreatetruecolor($height, $width);
imagesavealpha($image, true);
$trans_colour = imagecolorallocatealpha($image, 0, 0, 0, 127);
imagefill($image, 0, 0, $trans_colour);

#have to do the first one to initialize it
$xpt = $height*($x[0] - $min['x'])/($max['x'] - $min['x']);
$ypt = $width*($y[0] - $min['y'])/($max['y'] - $min['y']);
$prev = array('x'=>$xpt,'y'=>$ypt);

for($i=0; $i<count($x); $i++)
	{
	#map everything between 0 and 1 *height or *width
	$xpt = $height*($x[$i] - $min['x'])/($max['x'] - $min['x']);
	$ypt = $width*($y[$i] - $min['y'])/($max['y'] - $min['y']);
	
	imageline($image, $prev['x'], $prev['y'], $xpt, $ypt, $color);
	
	$prev = array('x'=>$xpt,'y'=>$ypt);
	}

header('Content-Type: image/png');

imagepng($image);
imagedestroy($image);

?>
