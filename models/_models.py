import json
import mysql.connector
import jsonpickle


class MySQLDB:
    
    def __init__(self):
        self.cnx = None
        self.cursor = None
        self.error = None
        self.query = None
        self.cnx, self.cursor, self.error = self.connectDB();
        
    
        
    def connectDB(self):
        try:
            self.cnx = mysql.connector.connect(user='charles', password='bark bark bark',host='127.0.0.1',database='tsd')
            self.cursor = self.cnx.cursor(dictionary = True)
        except mysql.connector.Error as err:
            if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
                self.error = "Something is wrong with your user name or password"

            elif err.errno == errorcode.ER_BAD_DB_ERROR:
                self.error = "Database does not exist"

            else:
                self.error = "Uncoded error"
        #else:
        return(self.cnx,self.cursor,self.error)
    
    
    
    
    def getGeneric(self):            
        self.cursor.execute(self.query,)

        i = 0
        res = {}
        for row in self.cursor:
            res[i] = {}
            for k,v in row.items():
                res[i][k] = v;
            i = i + 1
          
        self.cnx.close()
       
        return res
        #jsonstr = jsonpickle.encode(res, unpicklable=False)
        #return jsonstr