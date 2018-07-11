from pathlib import Path
import sys

root = Path(__file__).parents[1]
root = str(root.resolve())
sys.path.insert(0, root + "/models")
sys.path.insert(0, root + "/classes")
print(sys.path)


import getTagsCorrel as m0
import getHistSeries as m1
import CorrelationData as c0 
import scipy
import numpy as np
import collections

conn = m0.getTagsCorrel()
data = conn.getData()
import pprint

for k,row in data.items():
    #if row['corr_type'] != 'mic' : continue
    if row['s_corr_id'] != 1 : continue
    conn = m1.getHistSeries(row['fk_id_1'],row['fk_id_2'])
    hist = conn.getData()
    histCombined = collections.OrderedDict()
    
    for i,hrow in hist.items():
        fk_id = hrow['fk_id']
        pretty_date = hrow[b'pretty_date'].strftime('%Y-%m-%d')
        histCombined.setdefault(fk_id,collections.OrderedDict())
        histCombined[fk_id].setdefault(pretty_date,{})
        histCombined[fk_id][pretty_date] = {'pretty_date': pretty_date, 'fk_id': fk_id, 'date': hrow[b'date'].strftime('%Y-%m-%d'), 'chg': float(hrow['chg'])}
    # pprint.pprint(histCombined)
    correl = c0.CorrelationData(histCombined,row)
    results = correl.calculateCorrelation()
    # print(results)
    # pprint.pprint(hist2)