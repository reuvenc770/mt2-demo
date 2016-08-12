mt2App.controller( 'AttributionReportController' , [ 'AttributionApiService' , 'ClientApiService' , '$filter' , '$mdToast' , '$log' , function ( AttributionApiService , ClientApiService , $filter , $mdToast , $log ) {
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
        "limit" : 50 ,
        "page" : 1
    };

    self.clientNameMap = {};

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
        if ( type == 'Client' && Object.keys( self.clientNameMap ).length == 0 ) {
            ClientApiService.getAllClients(
                function ( response ) {
                    angular.forEach( response.data , function ( value , key ) {
                        self.clientNameMap[ value.client_id ] = value.username;
                    } );
                } ,
                function ( response ) {
                    $mdToast.show(
                        $mdToast.simple()
                            .textContent( 'Failed to load Client names. Please contact support.' )
                            .hideDelay( 1500 )
                    );
                } );
        }

        self.query.type = type;

        self.records = [];
        self.meta.recordCount = 0;
        self.meta.recordTotals = {};

        self.getRecords();
    };
} ] );
