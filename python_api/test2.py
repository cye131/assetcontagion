
import numpy as np
import json

x = np.array([[1, 2, 3], [4, 5, 6]], np.int32)
b = x.tolist()
json = json.dumps(b)