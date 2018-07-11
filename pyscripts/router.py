from pathlib import Path
import sys

root = Path(__file__).parents[1]
root = str(root.resolve())
sys.path.insert(0, root + "/models")
print(sys.path)


import getTagsCorrel as m

conn = m.getTagsCorrel()
data = conn.getData()


