from _models import MyPDO

class getTagsCorrel(MyPDO):
    def __init__(self):
        super().__init__()
        self.query = "SELECT * FROM `tags_correl` ORDER BY `s_corr_id` ASC"
        
        
    def getData(self):
        return super().getGeneric()
