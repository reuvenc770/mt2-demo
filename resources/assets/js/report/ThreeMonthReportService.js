mt2App.service( 'ThreeMonthReportService' , [ 'AttributionApiService' , 'FeedApiService' , '$mdToast' , '$window' , '$httpParamSerializer' , function ( AttributionApiService , FeedApiService , $mdToast , $window , $httpParamSerializer ) {
    var self = this;

    self.exportUrl = '/attr/report/export';

    self.startDate = new Date();
    self.endDate = new Date();

    self.query = {
        "type" : "ThreeMonth" ,
        "filters" : { "date" : { "start" : self.startDate , "end" : self.endDate } }
    };

    self.currentMonth = moment().format( 'MMMM' );
    self.lastMonth = moment().subtract( 1 , 'month' ).format( 'MMMM' );
    self.twoMonthsAgo = moment().subtract( 2 , 'month' ).format( 'MMMM' );

    self.feedNameMap = {};
    self.clientNameMap = {};

    self.setOrder = function ( order ) {
        self.query.order = order;
    };

    self.setLimit = function ( limit ) {
        self.query.limit = limit;
    };

    self.setPage = function ( page ) {
        self.query.page = page;
    };

    self.getRecords = function ( successCallback , failureCallback ) {
        return AttributionApiService.getRecords(
            self.query ,
            successCallback ,
            failureCallback 
        );
    };

    self.exportReport = function () {
        var fullUrl = self.exportUrl + '?' + $httpParamSerializer( self.query );

        $window.open( fullUrl , '_blank' );        
    };

    self.loadClientAndFeedNames = function () {
        FeedApiService.getAllFeeds(
            function ( response ) {
                angular.forEach( response.data , function ( value , key ) {
                    self.feedNameMap[ value.client_id ] = value.username;
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

        FeedApiService.getListOwners(
            function ( response ) {
                angular.forEach( response.data , function ( value , key ) {
                    self.clientNameMap[ value.value ] = value.name;
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
    };
} ] );
