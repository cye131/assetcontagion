import collections
import numpy as np
from scipy import stats
from minepy import MINE
import pprint

class CorrelationData:
    
    def __init__(self,combinedSeries,tagCorrel):
        self.combinedSeries = combinedSeries
        self.seriesNames = list( combinedSeries.keys() )
        self.freq = tagCorrel['freq']
        self.trail = tagCorrel['trail']
        self.val_type_1 = 'chg'
        self.val_type_2 = 'chg'
        self.corr_type = tagCorrel['corr_type']
        
        self.correlData = []
        self.correlIndex = []
    
    
    
    def calculateCorrelation(self):
        self.calculateCorrelationData()
        self.calculateCorrelationIndex()
        
        return {'data':self.correlData, 'index': self.correlIndex}
    
    def calculateCorrelationData(self):
        data = {'timeseries': collections.OrderedDict() }

        for code,seriesData in self.combinedSeries.items():
            if code == self.seriesNames[0]: level = '1'
            elif code == self.seriesNames[1]: level = '2'

            colUsed = getattr(self,'val_type_' + level)
            
            i = 0
            for tsDate,tsRow in seriesData.items():
                if ( tsDate not in list(data['timeseries'].keys())  ):
                    data['timeseries'][tsDate] = {}
                    data['timeseries'][tsDate]['date'] = tsDate
                if (tsRow[colUsed] != None ): data['timeseries'][tsDate][level] = tsRow[colUsed]
                # print(data['timeseries'][tsDate])
            
                i += 1
            
        data1_l = []
        data2_l = []
        dates_l = []
        
        for date,tsRow in data["timeseries"].items():
            # print(tsRow)
            rowKeys = list(tsRow.keys())
            if ('1' not in rowKeys) or ('2' not in rowKeys): continue
            if (tsRow['1'] == None or tsRow['2'] == None): continue
            # else: print ('not skipped')
            data1_l .append(tsRow['1'])
            data2_l.append(tsRow['2'])
            dates_l.append(date)
            
            if len(data1_l) > self.trail:
                data1_l.pop(0)
                data2_l.pop(0)
                dates_l.pop(0)
        
            if len(data1_l) == self.trail:
                corr = self.getCorr(data1_l,data2_l,self.corr_type)
                data["timeseries"][date]['correlation'] = round(corr,8)
                data["timeseries"][date]['inputs_used'] = len(dates_l)
                data["timeseries"][date]['earliest_input'] = dates_l[0]
            
        # print(data)
        self.correlData = data
        return
        
        
        
        
        
        
    def calculateCorrelationIndex(self):
        index = {}
        index['codes'] = self.seriesNames
        index['valuestocorrelate'] = [self.val_type_1,self.val_type_2]
        dates1 = []
        
        for date,row in self.combinedSeries[self.seriesNames[0]].items():
            dates1.append(date)
            
        for date,row in self.combinedSeries[self.seriesNames[1]].items():
            if date in dates1:
                index['firstshareddate'] = date
                break

        countcorrelation = 0
        # reverseddatearray = collections.OrderedDict( sorted(self.correlData['timeseries'].items(), key=lambda row: row[1]['date'], reverse=True))

        reverseddatearray = collections.OrderedDict( sorted(self.correlData['timeseries'].items(), key=lambda row: row[0], reverse=True))

        for date,row in reverseddatearray.items():
            if ( 'correlation' in list(row.keys()) ):
                countcorrelation += 1
                if (countcorrelation == 1):
                    index['correl_last_date'] = date
                    index['correl_last_val'] = row['correlation']
                    index['correl_last_earliestinput_date'] = row['earliest_input']
                elif (countcorrelation == self.trail):
                    index['correl_trail_date'] = date
                
                firstCorrelDate = date;
        
        index['correl_first_date'] = firstCorrelDate or None;

        index['correl_count'] = countcorrelation;
        
        if (countcorrelation >= 1):
            index['UPDATED'] = True;
        else:
            index['UPDATED'] = False;
        
        self.correlIndex = index
        return


    
    
    
    def getCorr (self,x,y,type) :
        if (type == 'rho'): return self.pearsonCorrelation(x,y);
        elif (type == 'ktau'): return self.kendallsTau(x,y);
        elif (type == 'srho'): return self.spearmansRho(x,y);
        elif (type == 'mic'): return self.MIC(x,y);



    def pearsonCorrelation(self,x, y) :
        res = stats.pearsonr(x,y)
        return res[0]
    
    def kendallsTau(self,x,y):
        res = stats.kendalltau(x,y)
        return res[0]
    
    def MIC(self,x,y):
        mine = MINE(alpha=0.6,c=15,est="mic_approx")
        mine.compute_score(x,y)
        return mine.mic()

    
    
        