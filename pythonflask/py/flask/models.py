# from flask_sqlalchemy import SQLAlchemy
# 
# db = SQLAlchemy()
# 
# class HistoricalData(db.Model):
#   __tablename__ = 'sector_indexhistoricaldata'
#   id = db.Column(db.String(100), primary_key = True)
#   cid = db.Column(db.String(50), index = True)
#   date = db.Column(db.DateTime)
#   close = db.Column(db.Float(10,2))
#   roi = db.Column(db.Float(10,4))

import json
import mysql.connector
from mysql.connector import errorcode
import jsonpickle


class MySQLDB:
    
    def __init__(self):
        self.cnx = None
        self.cursor = None
        self.error = None
        self.query = None
    
        
    def connectDB(self):
        try:
            self.cnx = mysql.connector.connect(user='charles', password='bark bark bark',host='127.0.0.1',database='tsd')
            self.cursor = self.cnx.cursor()
        except mysql.connector.Error as err:
            if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
                self.error = "Something is wrong with your user name or password"

            elif err.errno == errorcode.ER_BAD_DB_ERROR:
                self.error = "Database does not exist"

            else:
                self.error = "Uncoded error"

        #else:
            
        
        return(self.cnx,self.cursor,self.error)
              
                
class HistoricalData(MySQLDB):
        def __init__(self):
            super(HistoricalData, self).__init__()
            self.query = "SELECT * FROM `sector_indexhistoricaldata` WHERE `date` >= '2018-01-01' ORDER BY `classification_id`,`date`"
            
            
        def getData(self):            
            self.cursor.execute(self.query,)
            
            i = 0
            array = {}
            
            for (id,classification_id,date,close,roi) in self.cursor:
              #array[id] = date
              array[id] = {
                'date':date.strftime('%Y-%m-%d'),
                'close':float(close),
                'roi':float(roi)
              }
              i = i + 1
              
            self.cnx.close()
            
            jsonstr = jsonpickle.encode(array, unpicklable=False)
            
            return jsonstr
    
    
                
# else:
#     query = ("SELECT * FROM `sector_indexhistoricaldata` WHERE `date` >= '2018-01-01' ORDER BY `classification_id`,`date`")
#     
#     cursor.execute(query,)
#     
#     i = 0
#     array = {}
#     
#     for (id,classification_id,date,close,roi) in cursor:
#       #array[id] = date
#       array[id] = {
#         'date':date.strftime('%Y-%m-%d'),
#         'close':float(close),
#         'roi':float(roi)
#       }
#       i = i + 1
#       
#     cnx.close()
#     
#     jsonstr = jsonpickle.encode(array, unpicklable=False)
#     
#     return jsonstr