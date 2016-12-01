mt2App.controller( 'ReportController' , [ 'ReportApiService' , 'modalService' , '$log' , '$window' , '$httpParamSerializer' , function ( ReportApiService , modalService , $log , $window , $httpParamSerializer ) {
    var self = this;

    self.startDate = new Date();
    self.endDate = new Date();

    self.records = [];
    self.meta = { "recordCount" : 0 , "recordTotals" : {} };
    self.queryPromise = null;
    self.query = {
        "type" : "Deploy" ,
        "filters" : { "date" : { "start" : self.startDate , "end" : self.endDate } } ,
        "order" : 'date' ,
        "limit" : 50 ,
        "page" : 1
    };

    self.exportUrl = '/report/export';

    self.loadRecords = function () {
        self.getRecords();
    };

    self.getRecords = function () {
        self.queryPromise = ReportApiService.getRecords(
            self.query ,
            function ( response ) {
                self.records = response.data.records;
                self.meta.recordCount = parseInt( response.data.totalRecords );
                self.meta.recordTotals = response.data.totals;
            } , function ( response ) {
                modalService.simpleToast('Failed to load attribution records. Please contact support.');
            }
        );
    };

    self.exportReport = function () {
        var fullUrl = self.exportUrl + '?' + $httpParamSerializer( self.query );

        $window.open( fullUrl , '_blank' );
    };

    self.switchReportType = function ( type ) {
        self.query.type = type;

        self.updateQueryForReport( type );

        self.records = [];
        self.meta.recordCount = 0;
        self.meta.recordTotals = {};

        self.getRecords();
    };

    self.updateQueryForReport = function ( type ) {
        if  ( type === 'Deploy' ) {
            self.query.order = 'datetime';
        } else if ( type === 'EmailCampaignStatistics' ) {
            self.query.order = '-updated_at';
        } else {
            self.query.order = 'date';
        }
    }
} ] );
