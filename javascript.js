/*
call scribbles_init(canvas_id) to run
This really should be translated into a proper object orientated program.
Some of it already has, mainly to ensure variables are passed by reference
by using objects, as in time.

I suppose I could wrap all the positioning functions in an object wrapper,
and then wrap the whole thing in one big object with a method init() to
run thw whole thing. That might not be very fun though.
*/

function timeObject()
	{
	this.t = 0.001;
	this.tmax = 100;
	this.increment = 0.05;
	this.init = new Array();
	this.speed = 10;
	};
time = new timeObject;

var friction = 1;
var x_pos, y_pos;

var height=400,width=400;

var ctx;

var timer_var;

var sins_x = new Object;
sins_x.array = new Array();
sins_x.vals = new Array();

var sins_y = new Object;
sins_y.array = new Array();
sins_y.vals = new Array();

var x_min=10000,x_max=0,y_min=10000,y_max=0;


function get_point(time, get_iterate, delay, this_init)
	{
	//get position
	x_pos = f_x( x(time.t, time.tmax, sins_x), x(time.t, time.tmax, sins_y) );
	y_pos = f_y( x(time.t, time.tmax, sins_x), x(time.t, time.tmax, sins_y) );
	
	//normalize
	x_pos = width*(x_pos - x_min)/(x_max - x_min);
	y_pos = height*(y_pos - y_min)/(y_max - y_min);
	
	//$('#write').append('<p>'+t+': '+x_pos+' '+y_pos+'</p>');
	ctx.lineTo(x_pos,y_pos);
	ctx.stroke();
	time.t+=time.increment;
	//alert(t);
	//$('#smallwrap').append('<p>'+time.t+': '+x_pos+' '+y_pos+'<p>');
	if(time.t<time.tmax && get_iterate==true)
		{
		time.init[this_init] = setTimeout(function()
			{
			get_point(time, true, delay, this_init);
			}, delay);
		}
	};

function scribbles_init(id)
{
//stopping previous run if found
if(time.init[0])
	{
	for(var i=0; i<time.speed; i++)
		{
		clearTimeout(time.init[i]);
		}
	}

//initialize canvas
var canvas = document.getElementById(id);
if (canvas.getContext)
	{
	height=canvas.height;
	width=canvas.width;
	canvas.height=height;
	canvas.width=width;
	ctx = canvas.getContext('2d');
	
	ctx.strokeStyle = "rgba(255,0,0,0.6)";
	ctx.lineWidth = 0.07;
	
	//initializing time
	time = new timeObject;
	
	//initializing sins
	sins_x = new Object;
	sins_x.array = new Array();
	sins_x.vals = new Array();

	sins_y = new Object;
	sins_y.array = new Array();
	sins_y.vals = new Array();
	
	//Generate sins
	gen_sins(sins_x);
	gen_sins(sins_y);
	
	//find maxes and mins
	x_min=10000,x_max=0,y_min=10000,y_max=0;
	for(var t=0; t<40; t+=time.increment)
		{
		x_pos = f_x( x(t, time.tmax, sins_x), x(t, time.tmax, sins_y) );
		y_pos = f_y( x(t, time.tmax, sins_x), x(t, time.tmax, sins_y) );
		
		(x_pos>x_max) && (x_max=x_pos);
		(y_pos>y_max) && (y_max=y_pos);
		(x_pos<x_min) && (x_min=x_pos);
		(y_pos<y_min) && (y_min=y_pos);
		}
	
	//initializing x,y positions
	x_pos = f_x( x(time.t, time.tmax, sins_x), x(time.t, time.tmax, sins_y) );
	y_pos = f_y( x(time.t, time.tmax, sins_x), x(time.t, time.tmax, sins_y) );
	x_pos = width*(x_pos - x_min)/(x_max - x_min);
	y_pos = height*(y_pos - y_min)/(y_max - y_min);
	
	ctx.moveTo(x_pos, y_pos);


	//beginning running main loop
	var delay=1;
	for(var i=0;i<time.speed;i++)
		{
		get_point(time, true, delay, i);
		}
	}
}


/*
This is (or was) the C code. It needs to be done up a bit.
Can't pass normal variables by reference, so it may be possible
to pass them as objects instead. So, instead of passing sins as
the array, I'll pass an object called sins with an attached array
called something. Have function(sins){sins.array[]=...}; instead.
Should only be a small change.
Must sort out random number generator again.
*/

function range_rand(i, j)
{
var k;

//k = i + rand() / ( RAND_MAX / ( j+1 - i ) + 1 );

k = i + Math.random()*(j+1-i);
k = Math.floor(k);

return k;
};

function gen_sins(sins)
{
var i;
var rand_i = 7;//range_rand(2,10);

sins.vals[0] = 0;
for(i=0; i<10; i++)/*Return array of premultiplier, internal multiplier.*/
	{
	if(i<rand_i)
		{
		sins.array[i] = (range_rand(1,5))/2;
		sins.vals[0] += sins.array[i];
		sins.array[10 + i] = 1.5 + (i+1)/2*range_rand(0,2);
		//printf("PRE: %f\tIN:%f\n",sins[i],sins[10+i]);
		}
	else
		{
		sins.array[i] = 0;
		sins.array[10 + i] = 0;
		}
	}

sins.vals[0] = 1/sins.vals[0];
sins.vals[1] = rand_i;
};


function f_x(x2, y1)
{
return (x2*x2*x2*x2 + (x2*x2)*(y1*y1) + y1*Math.sqrt(-(x2*x2)*(x2*x2*x2*x2 + (y1*y1)*(-16 + y1*y1) + 2*(x2*x2)*(-8 + (y1*y1)))))/(2*x2*(x2*x2 + y1*y1));
};
	
function f_y(x2, y1)
{
return ((x2*x2)*y1 + y1*y1*y1 + Math.sqrt(-(x2*x2)*(x2*x2*x2*x2 + (y1*y1)*(-16 + y1*y1) + 2*(x2*x2)*(-8 + y1*y1))))/(2*(x2*x2 + y1*y1));
};

//friction function
function c(t, tmax)
{
return (1 - friction*t/tmax);
};

//x (0) + swinging function
function x(t, tmax, sins)
{
var i;
var sin_sum=0;

for(i=0; i<sins.vals[1];i++)
	{
	sin_sum += sins.array[i]*Math.sin(sins.array[10+i]*t);
	}
	
return 1 + 0.1*c(t, tmax)*sins.vals[0]*sin_sum;
};
