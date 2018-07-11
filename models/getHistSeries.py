from _models import MySQLDB

class getHistSeries(MySQLDB):
    def __init__(self, fk_id_1, fk_id_2):
        super().__init__()
        self.query = "SELECT * FROM hist_series WHERE (hist_series.fk_id = " + str(fk_id_1) +  " OR hist_series.fk_id = " + str(fk_id_2) + ") and pretty_date>='2018-01-01'"
        
    def getData(self):
        return super().getGeneric()
