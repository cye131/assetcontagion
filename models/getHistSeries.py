from _models import MyPDO

class getHistSeries(MyPDO):

    def __init__(self, fk_id_1, fk_id_2,pretty_date=None):
        super().__init__()

        self.bindList = [str(fk_id_1), str(fk_id_2)]
        if (pretty_date != None):
            dateStr = "AND hist_series.pretty_date >= %s"
            self.bindList.append(pretty_date.strftime('%Y-%m-%d'))
        else:
            dateStr = ''
        
        self.query = "SELECT * FROM hist_series WHERE (hist_series.fk_id = %s OR hist_series.fk_id = %s) " + dateStr
        
    def getData(self):
        return super().getGeneric()

