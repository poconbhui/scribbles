<?php
/*
Interesting Scribbles 

Modelled on dual pendulums with a rigid stick coming from each, joined by a hinge with a pen on that hinge. This should be what it draws. Makes some nice doodles. Funny surfaces can be made by including friction. Shapes mainly vary by varying speeds of sin functions. Set tmax to something silly to make a mess!

(* Assumed the positions of the pendulums stay fixed, so the x of one and the \
y of the other could be set to 0 *)
*/

$uniqid = 'tmp/'.uniqid('plot_');


#Begin creating and drawing onto image
$width = ($_GET['width']!='')?$_GET['width']:600;
$height = ($_GET['height']!='')?$_GET['height']:400;
$color = 0x00FF0000; #Alpha, then RGB
$image = imagecreatetruecolor($width, $height);
imagesavealpha($image, true);
$trans_colour = imagecolorallocatealpha($image, 0, 0, 0, 127);
imagefill($image, 0, 0, $trans_colour);


#build points
$tmax = ($_GET['tmax']!='')?$_GET['tmax']:200;
$t_increment = ($_GET['t_increment']!='')?$_GET['t_increment']:0.01;
$friction = ($_GET['friction']!='')?$_GET['friction']:1;
$points=`./scribbuild.out $width $height $tmax $t_increment $friction`;
$points = explode("\n", $points);
$num_points = count($points);
for($i=0; $i<$num_points; $i++)
	{
	$points[$i] = explode("\t",$points[$i]);
	}

//echo $points[0][0]."\t".$points[0][1]."\n";
//echo $points[$num_points-2][0]."\t".$points[$num_points-2][1];
//die;
for($i=1; $i<$num_points-2; $i++)
	{
	imageline($image, $points[$i-1][0], $points[$i-1][1], $points[$i][0], $points[$i][1], $color);
	}

header('Content-Type: image/png');

imagepng($image);
imagedestroy($image);

?>
