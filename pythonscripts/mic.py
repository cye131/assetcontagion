import time
startTime = time.time()

import sys
import jsonpickle
from minepy import MINE

#print(sys.argv)
#print(sys.argv[0])
#print('t')
fromPHP = jsonpickle.decode(sys.argv[2])
#x=[0.0017,0.0009,-0.0068,0.0041,-0.0009,-0.0026,0.0074,-0.0031,0.0032,-0.002,-0.0024,-0.0116,0.0127,-0.0069,0.0108,0.0045,0.0007,0.0086,-0.0007,0.0031,0.0011,0.0017,-0.004,0.0025,-0.001,-0.0021,-0.004,0,-0.0063,0.0019]
#y=[-0.0007,0.0055,-0.0034,0.0052,0.0031,-0.0061,0.0093,-0.0051,-0.0031,-0.0069,-0.0072,-0.015,0.0191,-0.0045,-0.0014,-0.0181,0.022,0.0049,-0.0007,0.0045,-0.0003,-0.001,0.0014,-0.0028,-0.0093,0.0042,-0.0194,0,-0.0063,.0096]

mine = MINE(alpha=0.6,c=15,est="mic_approx")
mine.compute_score(fromPHP["y1"],fromPHP["y2"])

toJSON = {};
toJSON['mic'] = mine.mic()
toJSON['py_exec_time'] = format(time.time() - startTime) + 'seconds'

json = jsonpickle.encode(toJSON,unpicklable=False)
print(json)
