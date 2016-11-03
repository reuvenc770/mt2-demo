mt2App.controller('SourceUrlSearchController' , [ '$rootScope' , '$window' , '$location' , '$timeout', '$log' , 'SourceUrlSearchApiService' , 'FeedApiService' , 'formValidationService', 'modalService' , 'orderByFilter' , function ( $rootScope , $window , $location , $timeout , $log , SourceUrlSearchApiService , FeedApiService , formValidationService, modalService , orderBy ) {
    var self = this;

    self.formSubmitted = false;
    self.formErrors = [];
    self.search = {
        "source_url" : '' ,
        "clientIds" : [] ,
        "feedIds" : [] ,
        "verticalIds" : [] ,
        'startDate' : '' ,
        'endDate' : '' ,
        'exportFile' : false
    };
    self.rawStartDate = null;
    self.rawEndDate = null;
    self.dateRange = "custom";

    self.clientList = [];
    self.selectedClients= [];
    self.feedList = [];
    self.selectedFeeds = [];
    self.verticalList = [];
    self.selectedVerticals = [];

    self.loadFeedList = function () {
        FeedApiService.getAllFeeds( self.getAllFeedsSuccessCallback , self.getAllFeedsFailureCallback );
    };

    self.setClientList = function ( list ) {
        self.clientList = list;
    }

    self.setVerticalList = function ( list ) {
        self.verticalList = list;
    };

    self.updateSearchDate = function ( dateRange ) {

        var startDate = '';
        var endDate = '';

        if (dateRange != 'custom' ) {
            startDate = moment().subtract( dateRange , 'days' );
            endDate = moment();

            self.search.startDate = startDate.format('YYYY-MM-DD');
            self.search.endDate = endDate.format('YYYY-MM-DD');

            self.rawStartDate = startDate.toDate();
            self.rawEndDate = endDate.toDate();
        }

        if (dateRange == 'custom') {
            self.search.startDate = moment(self.rawStartDate).format('YYYY-MM-DD');
            self.search.endDate = moment(self.rawEndDate).format('YYYY-MM-DD');
        }
    };

    /**
     * Success Callbacks
     */
    self.getAllFeedsSuccessCallback = function ( response ) {
        self.feedList = response.data;
    };

    self.updateCurrentFeedList = function () {
        var feedIdList = [];

        angular.forEach( self.selectedFeeds , function ( feed ) {
            feedIdList.push( feed.id );
        });

        self.search.feedIds = feedIdList;
    };

    self.updateCurrentVerticalList = function () {
        var verticalIdList = [];

        angular.forEach( self.selectedVerticals , function ( vertical) {
            verticalIdList.push( vertical.id );
        });

        self.search.verticalIds = verticalIdList;
    };

    /**
     * Failure Callbacks
     */
    self.getAllFeedsFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load list of feeds.' );

        modalService.launchModal();
    };

}] );