#include <stdio.h>
#include <stdlib.h>
#include <math.h>

int range_rand(int i, int j);//rand(min,max) as in php
int first_zero(float* arr);//find the first zero in an array. Maybe not necessary now?

void gen_sins(float* sins, float* sins_vals);//generates some random numbers for the sinusoids.

float f_x(float x2, float y1);//x position of the pen
float f_y(float x2, float y1);//y position of the pen
float c(float t, float tmax); //friction function
float x(float t, float tmax, float* sinsx, float* sinsx_vals);//position of the oscillator

float friction=1;

int main(int argc, char* argv[])
{
//argv[1] = x multi, argv[2] = y multi
//argv[3] = tmax, argv[4] = t_increment
long i,j;
float t,tmax = 200,t_increment=0.02;
float *x_pos;
float *y_pos;
//float x_pos[10000],y_pos[10000];
float xmax = -100,ymax = -100;
float xmin = 100,ymin = 100;
float xnormal, ynormal;
float x_multi = 1,y_multi = 1;
float sinsx[10*2],sinsy[10*2]; //10 sin cofactor, 10 t cofactor
float sinsx_vals[2],sinsy_vals[2]; //normalization, length

if(argc==6)
	{
	friction = atof(argv[5]);
	}

if(argc>=5)
	{
	x_multi = atof(argv[1]);
	y_multi = atof(argv[2]);
	
	tmax = atof(argv[3]);
	t_increment = atof(argv[4]);
	
	x_pos = (float*)malloc((int)tmax/t_increment*sizeof(float));
	y_pos = (float*)malloc((int)tmax/t_increment*sizeof(float));
	}
else
	{
	printf("This program outputs the points a pen connected by arms of some length to two perpendicular oscillators would trace out.\n\nUsage:\n%s x_max y_max max_runtime time_increment\n\nThe left/right columns vary between 0 and x_max/y_max.\n",argv[0]);
	return 0;
	}

srand((unsigned) time(NULL));

gen_sins(sinsx, sinsx_vals);
gen_sins(sinsy, sinsy_vals);


i=0;
for(t=0.1;t<tmax; t+=t_increment)
	{
	x_pos[i] = f_x( x(t, tmax, sinsx, sinsx_vals), x(t, tmax, sinsy, sinsy_vals) );
	y_pos[i] = f_y( x(t, tmax, sinsx, sinsx_vals), x(t, tmax, sinsy, sinsy_vals) );
	
	if(x_pos[i] > xmax)
		{
		xmax = x_pos[i];
		}
	if(y_pos[i] > ymax)
		{
		ymax = y_pos[i];
		}
	
	if(x_pos[i] < xmin)
		{
		xmin = x_pos[i];
		}
	if(y_pos[i] < ymin)
		{
		ymin = y_pos[i];
		}
	
	i++;
	}

xnormal = xmax - xmin;
ynormal = ymax - ymin;
for(j=0;j<i;j++)
	{
	x_pos[j] = x_multi*(x_pos[j] - xmin)/xnormal;
	y_pos[j] = y_multi*(y_pos[j] - ymin)/ynormal;
	
	printf("%f\t%f\n",x_pos[j],y_pos[j]);
	
	/*
	if(x_pos[j]>1 || y_pos[j]>1 || x_pos[j]<0 || y_pos[j]<0)
	printf("FIX: %0.5f\t%0.5f\nORG: %0.5f\t%0.5f\nMIN: %0.5f\t%0.5f\nMAX: %0.5f\t%0.5f\nNRM: %0.5f\n\n", x_pos[j], y_pos[j], t, tmax, xmin, ymin, xmax, ymax, xnormal);
	*/
	}

free(x_pos);
free(y_pos);

return 1;
}

int range_rand(int i, int j)
{
int k;

k = i + rand() / ( RAND_MAX / ( j+1 - i ) + 1 );

return k;
}



int first_zero(float *arr)
{
int i=0;
while(arr[i]>0)
	{
	i++;
	}
return i;
}


void gen_sins(float *sins, float *sins_vals)
{
int i;


int rand_i = 7;//range_rand(2,10);

for(i=0; i<10; i++)/*Return array of premultiplier, internal multiplier.*/
	{
	if(i<rand_i)
		{
		sins[i] = ((float)range_rand(1,5))/2;
		sins_vals[0] += sins[i];
		sins[10 + i] = 1.5 + (i+1)/2*range_rand(0,2);
		//printf("PRE: %f\tIN:%f\n",sins[i],sins[10+i]);
		}
	else
		{
		sins[i] = 0;
		sins[10 + i] = 0;
		}
	}

sins_vals[0] = 1/sins_vals[0];
sins_vals[1] = rand_i;


}


float f_x(float x2, float y1)
{
return (x2*x2*x2*x2 + (x2*x2)*(y1*y1) + y1*sqrt(-(x2*x2)*(x2*x2*x2*x2 + (y1*y1)*(-16 + y1*y1) + 2*(x2*x2)*(-8 + (y1*y1)))))/(2*x2*(x2*x2 + y1*y1));
}
	
float f_y(float x2, float y1)
{
return ((x2*x2)*y1 + y1*y1*y1 + sqrt(-(x2*x2)*(x2*x2*x2*x2 + (y1*y1)*(-16 + y1*y1) + 2*(x2*x2)*(-8 + y1*y1))))/(2*(x2*x2 + y1*y1));
}

//friction function
float c(float t, float tmax)
{
return (1 - friction*t/tmax);
}

//x (0) + swinging function
float x(float t, float tmax, float *sins, float *sins_vals)
{
int i;
float sin_sum=0;

for(i=0; i<sins_vals[1];i++)
	{
	sin_sum += sins[i]*sin(sins[10+i]*t);
	}
	
return 1 + 0.1*c(t, tmax)*sins_vals[0]*sin_sum;
}
