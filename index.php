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
		}
	
	$return = rtrim($return, '+ ');
	
	$return .= ')';
	$return = '(1/'.(floor($total)+0.5).')*'.$return;
	return $return;
	
	}

#echo gen_sins();
#echo 'FACE';
#die;

$sins = gen_sins();

$plot='
set parametric
set term png transparent
set output "'.$uniqid.'.png"

#set title "Two oscillators pushing a pen"
set nokey
set noborder
set noxtics
set noytics

fx(x2, y1) = (x2**4 + (x2**2)*(y1**2) + y1*sqrt(-(x2**2)*(x2**4 + (y1**2)*(-16 + y1**2) + 2*(x2**2)*(-8 + (y1**2)))))/(2*x2*(x2**2 + y1**2))

fy(x2, y1) = ((x2**2)*y1 + y1**3 + sqrt(-(x2**2)*(x2**4 + (y1**2)*(-16 + y1**2) + 2*(x2**2)*(-8 + y1**2))))/(2*(x2**2 + y1**2))

#radius
r = 2

#run time
tmax = 200
set samples tmax*20

#friction function
c(t)= (1 - t/tmax)

#x (0) + swinging function
x(t)= 1 + 0.1*c(t)*'.$sins.'

#y (0) + swinging function
y(t)= 1 + 0.1*c(t)*'.gen_sins().'

#plot [1:tmax] fx(x(t),y(t)), fy(x(t),y(t)) with dots
plot [1:tmax] fx(x(t),y(t)),fy(x(t),y(t))

#pause -1';

$fp = fopen($uniqid.'.plt','w');
fwrite($fp,$plot);
fclose($fp);

system('gnuplot '.$uniqid.'.plt');

header('Content-Type: image/png');
passthru('cat '.$uniqid.'.png');

unlink($uniqid.'.plt');
unlink($uniqid.'.png');

?>
