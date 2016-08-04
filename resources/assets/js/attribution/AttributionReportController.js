mt2App.controller( 'AttributionReportController' , [ 'AttributionApiService' , '$filter' , '$mdToast' , '$log' , function ( AttributionApiService , $filter , $mdToast , $log ) {
    var self = this;

    self.startDate = new Date();
    self.endDate = new Date();

    self.records = [];
    self.meta = { "recordCount" : 0 , "recordTotals" : {} };
    self.queryPromise = null;
    self.query = {
        "type" : "Record" ,
        "filters" : { "date" : { "start" : self.startDate , "end" : self.endDate } } ,
        "order" : 'date' ,
        "limit" : 5 ,
        "page" : 1
    };

    self.loadRecords = function () {
        self.getRecords();
    }; 

    self.getRecords = function () { 
        self.queryPromise = AttributionApiService.getRecords(
            self.query ,
            function ( response ) {
                self.records = response.data.records;
                self.meta.recordCount = parseInt( response.data.totalRecords );
                self.meta.recordTotals = response.data.totals;
            } , function ( response ) {
                $mdToast.show(
                    $mdToast.simple()
                        .textContent( 'Failed to load Attribution Records. Please contact support.' )
                        .hideDelay( 1500 )
                );
            }
        );
    };

    self.switchReportType = function ( type ) {
        self.query.type = type;

        self.records = [];
        self.meta.recordCount = 0;
        self.meta.recordTotals = {};

        self.getRecords();
    };
} ] );
