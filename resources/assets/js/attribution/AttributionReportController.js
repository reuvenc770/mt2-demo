mt2App.controller( 'AttributionReportController' , [ 'AttributionApiService' , 'ClientApiService' , '$filter' , '$mdToast' , '$log' , '$window' , '$httpParamSerializer' , function ( AttributionApiService , ClientApiService , $filter , $mdToast , $log , $window , $httpParamSerializer ) {
    var self = this;

    self.startDate = new Date();
    self.endDate = new Date();

    self.records = [];
    self.meta = { "recordCount" : 0 , "recordTotals" : {} };
    self.queryPromise = null;
    self.query = {
        "type" : "ThreeMonth" ,
        "filters" : { "date" : { "start" : self.startDate , "end" : self.endDate } } ,
        "order" : 'date' ,
        "limit" : 50 ,
        "page" : 1
    };

    self.clientNameMap = {};
    self.listOwnerNameMap = {};

    self.threeMonthSections = {
        "currentMonth" : moment().format( 'MMMM' ) ,
        "lastMonth" : moment().subtract( 1 , 'month' ).format( 'MMMM' ) ,
        "twoMonthsAgo" : moment().subtract( 2 , 'month' ).format( 'MMMM' )
    };

    self.exportUrl = '/attr/report/export';

    self.loadRecords = function () {
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
            }
        );

        ClientApiService.getListOwners(
            function ( response ) {
                angular.forEach( response.data , function ( value , key ) {
                    self.listOwnerNameMap[ value.value ] = value.name;
                } );
            } ,
            function ( response ) {
                $mdToast.show(
                    $mdToast.simple()
                        .textContent( 'Failed to load List Owners. Please contact support.' )
                        .hideDelay( 1500 )
                );
            }
        );

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
