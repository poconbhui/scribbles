#!/usr/bin/env gnuplot

set parametric

unset border
unset tics

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
x(t)= 1 + c(t)*(0.1*sin(t) + 0.1*sin(1.5*t))

#y (0) + swinging function
y(t)= 1 + c(t)*0.2*sin(t)

#plot [1:tmax] fx(x(t),y(t)), fy(x(t),y(t)) with dots
plot [1:tmax] fx(x(t),y(t)),fy(x(t),y(t)) notitle

pause -1
