import json
import mysql.connector
import jsonpickle


class MyPDO:

    def __init__(self):
        self.cnx = None
        self.cursor = None
        self.error = None
        self.query = None
        self.bindList = None
        self.cnx, self.cursor, self.error = self.connectDB();
    
    def closeDB(self):
        self.cursor.close()
        self.cnx.close()
        
    def connectDB(self):
        try:
            self.cnx = mysql.connector.connect(user='charles', password='bark bark bark',host='127.0.0.1',database='tsd',autocommit=True)
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

        if (self.bindList == None):
            self.cursor.execute(self.query,)
        else:
            self.cursor.execute(self.query,self.bindList)

        i = 0
        res = {}
        for row in self.cursor:
            res[i] = {}
            for k,v in row.items():
                res[i][k] = v;
            i = i + 1


        return res
        #jsonstr = jsonpickle.encode(res, unpicklable=False)
        #return jsonstr

		
		
    def insertMultiple(self,tblName,colNames,dataVals):
        dataToInsert = []

        for data in dataVals:
            for k,v in data.items():
                dataToInsert.append(v)

        updateCols = []
        rowQs = []
        allQs = []
        for curCol in colNames:
            updateCols.append(curCol + ' = VALUES(' + curCol + ')')
            rowQs.append('%s')

        onDup = ', '.join(updateCols)

        rowPlaces = '(' + ', '.join(rowQs) + ')'

        for i in dataVals:
            allQs.append(rowPlaces)

        allPlaces = ', '.join(allQs)

        self.query = "INSERT INTO " + tblName + " (" + ', '.join(colNames) + ") VALUES " + allPlaces + " ON DUPLICATE KEY UPDATE " + onDup;
        self.cursor.execute(self.query,dataToInsert)

        self.cnx.commit()
        self.successRowsChanged = self.cursor.rowcount