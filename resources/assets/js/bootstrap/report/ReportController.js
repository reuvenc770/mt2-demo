mt2App.controller( 'ReportController' , [ 'ReportApiService' , 'formValidationService' , 'modalService' , '$log' , '$window' , '$httpParamSerializer' , '$timeout' , function ( ReportApiService ,formValidationService , modalService , $log , $window , $httpParamSerializer , $timeout ) {
    var self = this;

    self.startDate = new Date();
    self.endDate = new Date();

    self.records = [];
    self.meta = { "recordCount" : 0 , "recordTotals" : {} };
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
                modalService.simpleToast( 'Failed to load Attribution Records. Please contact support.' );
            }
        );
    };

    self.exportReport = function () {
        var fullUrl = self.exportUrl + '?' + $httpParamSerializer( self.query );

        $window.open( fullUrl , '_blank' );
    };
} ] );
