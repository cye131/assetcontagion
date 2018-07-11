import jsonpickle


class Controller1:
    
    #def __init__(self):

    
    def getdata(self,cnx,cursor):
        query = ("SELECT * FROM `sector_indexhistoricaldata` WHERE `date` >= '2018-01-01' ORDER BY `classification_id`,`date`")
        
        cursor.execute(query,)
        
        i = 0
        array = {}
        
        for (id,classification_id,date,close,roi) in cursor:
          #array[id] = date
          array[id] = {
            'date':date.strftime('%Y-%m-%d'),
            'close':float(close),
            'roi':float(roi)
          }
          i = i + 1
          
        cnx.close()
        
        jsonstr = jsonpickle.encode(array, unpicklable=False)
        
        return jsonstr