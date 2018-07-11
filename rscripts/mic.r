<<<<<<< HEAD
start_time = Sys.time()

args = commandArgs(TRUE)
#print(args)
setwd(args[1])

#install.packages("jsonlite", lib= "lib" )
library(minerva,lib.loc = "lib",quietly=TRUE)
library(jsonlite,lib.loc = "lib",quietly=TRUE)
lib_time = Sys.time()

from = fromJSON(args[2])

y = data.frame(from[1],from[2])

MINE = mine(y[,1],y[,2])
MIC = MINE$MIC


end_time = Sys.time()
exec_time = toString(lib_time-start_time)
MINE$r_exec_time = exec_time

to = toJSON(as.vector(MINE),digits=8)
=======
start_time = Sys.time()

args = commandArgs(TRUE)
#print(args)
setwd(args[1])

#install.packages("jsonlite", lib= "lib" )
library(minerva,lib.loc = "lib",quietly=TRUE)
library(jsonlite,lib.loc = "lib",quietly=TRUE)
lib_time = Sys.time()

from = fromJSON(args[2])

y = data.frame(from[1],from[2])

MINE = mine(y[,1],y[,2])
MIC = MINE$MIC


end_time = Sys.time()
exec_time = toString(lib_time-start_time)
MINE$r_exec_time = exec_time

to = toJSON(as.vector(MINE),digits=8)
>>>>>>> 975553768a294c5739879bca2697957b736e6203
print(to)