from pathlib import Path
import sys

root = Path(__file__).parents[1]
root = str(root.resolve())
sys.path.insert(0, root + "/models")
sys.path.insert(0, root + "/classes")

import _models as m
import getTagsCorrel as m0
import getHistSeries as m1
import CorrelationData as c0 
import scipy
import numpy as np
import collections
from datetime import date
import pprint

conn = m0.getTagsCorrel()
tagsCorrel = conn.getData()
conn.closeDB()

for k,row in tagsCorrel.items():
    conn = m1.getHistSeries(row['fk_id_1'],row['fk_id_2'], (row[b'obs_end_input_min'] or None))
    histSeries = conn.getData()
    conn.closeDB()
    histCombined = collections.OrderedDict()
    
    for i,hrow in histSeries.items():
        fk_id = hrow['fk_id']
        pretty_date = hrow[b'pretty_date'].strftime('%Y-%m-%d')
        histCombined.setdefault(fk_id,collections.OrderedDict())
        histCombined[fk_id].setdefault(pretty_date,{})
        histCombined[fk_id][pretty_date] = {'pretty_date': pretty_date, 'fk_id': fk_id, 'date': hrow[b'date'].strftime('%Y-%m-%d'), 'chg': float(hrow['chg'])}
    # pprint.pprint(histCombined)
    correl = c0.CorrelationData(histCombined,row)
    results = correl.calculateCorrelation()
    data = results['data']
    index = results['index']
    
    # pprint.pprint(data)
    # pprint.pprint(index)
    # sys.exit()
    
    
    # SQL query for historical data
    #
    #
    #
    #
 
    dataVals = []
    for tsDate,tsRow in data['timeseries'].items():
        if ( 'correlation' not in list(tsRow.keys()) ): continue
        dataVals.append(collections.OrderedDict([
             ('pretty_date', tsDate),
             ('value', tsRow['correlation']),
             ('fk_id', row['s_corr_id'])
        ]))
        
        
    uHistCorrel = {'data': [], 'info': {}}
    if ( not data or len(data) == 0 ):
        uHistCorrel['info']['errorMsg'] = 'No data was able to be calculated'
        uHistCorrel['info']['insertedHistData'] = False
    elif (len(dataVals) == 0):
        uHistCorrel['info']['errorMsg'] = 'Not enough data for correlation calculations'
        uHistCorrel['info']['insertedHistData'] = False
    else:
        colNames = ['pretty_date','value','fk_id']
        sql = m.MyPDO()
        sql.insertMultiple('hist_correl',colNames,dataVals)
        sql.closeDB()
        
    # Check if historical data insert was successful
    #
    #
    #
    #
    if ( 'insertedHistData' in list(uHistCorrel['info'].keys()) and uHistCorrel['info']['insertedHistData'] == False ):
        print('ERR')
    elif (sql.successRowsChanged == 0 ):
        uHistCorrel['info'] = {
            'rowsChg': 0,
            'errorMsg': 'No new data to add',
            'insertedHistData': False
            }
    else:
        uHistCorrel['info'] = {
            'rowsChg': sql.successRowsChanged,
            'errorMsg': '',
            'insertedHistData': True,
            'firstDate': index['correl_first_date'],
            'lastDate': index['correl_last_date'],
            'lastVal': index['correl_last_val'],
            'lastFirstInput': index['correl_last_earliestinput_date'],
            'obsCount': index['correl_count']
        }
    
    



    #  If so then update the data tags
    # 
    # 
    # 
    # 
    if (uHistCorrel['info']['insertedHistData'] == True):
        
        queryVals = [
            uHistCorrel['info']['lastDate'],
            uHistCorrel['info']['lastVal'],
            uHistCorrel['info']['lastFirstInput'],
            uHistCorrel['info']['obsCount'],
            row['s_corr_id']
            ]
        
        if ( row[b'obs_start'] == None ):
            includeObsStart = 'obs_start=%s,'
            queryVals.insert(0,uHistCorrel['info']['firstDate'])
        else:
            includeObsStart = ''
        
        
        query = "UPDATE tags_correl SET " + includeObsStart + " obs_end=%s, obs_end_val=%s, obs_end_input_min=%s, obs_count=obs_count + %s, last_updated=now() WHERE s_corr_id=%s"


        sql2 = m.MyPDO()
        sql2.cursor.execute(query,queryVals)
        sql2.cnx.commit()
        sql2.successRowsChanged = sql2.cursor.rowcount
        sql2.closeDB

        if sql2.successRowsChanged != 1:
            print('SQL2 Failure')
            continue
        # else:
            # print(sql2.successRowsChanged)
            # print(row['s_corr_id'])
            
        uHistCorrel['info']['updatedTags'] = True;
        
        
        uHistCorrel['data'] = data;



    if (uHistCorrel['info']['insertedHistData'] == True):
        print( "Successful ("+str(row['s_corr_id'])+"): updated "+str(uHistCorrel['info']['rowsChg'])+" rows from "+uHistCorrel['info']['firstDate']+" to "+uHistCorrel['info']['lastDate'] )
    else:
        print( "Failed ("+str(row['s_corr_id'])+"): "+uHistCorrel['info']['errorMsg'] )