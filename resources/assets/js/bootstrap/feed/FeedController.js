mt2App.controller( 'FeedController' , [ '$rootScope' , '$window' , '$location' , '$timeout', 'FeedApiService', '$mdToast', '$mdDialog', '$log' , 'formValidationService' , 'modalService' , function ( $rootScope , $window , $location , $timeout , FeedApiService, $mdToast , $mdDialog , $log , formValidationService , modalService) {
    var self = this;
    self.$location = $location;

    self.current = {
        id: "",
        client_id: "" ,
        party: 1 ,
        name : '' ,
        short_name: "" ,
        status: "Active" ,
        vertical_id: "" ,
        frequency: "" ,
        type_id: "" ,
        country_id: 1 ,
        source_url: ""
    };

    self.feeds = [];
    self.frequency = [ "TBD", "RT" , "Daily" , "Weekly" , "Monthly"];

    self.createUrl = '/feed/create';

    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.feedTotal = 0;
    self.queryPromise = null;
    self.sort= "-id";

    self.formSubmitted = false;

    self.formErrors = [];

    /**
     * Init Methods
     */

    self.loadFeed = function () {
        var pathMatches = $location.path().match( /^\/feed\/edit\/(\d{1,})/ );

        FeedApiService.getFeed( pathMatches[1] , self.loadFeedSuccessCallback , self.loadFeedFailureCallback );
    };

    self.loadFeeds = function () {
        self.queryPromise = FeedApiService.getFeeds(
            self.currentPage ,
            self.paginationCount ,
            self.sort,
            self.loadFeedsSuccessCallback , self.loadFeedsFailureCallback );
    };

    self.saveFeed = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);

        FeedApiService.saveFeed( self.current , self.SuccessCallBackRedirectList , self.saveFeedFailureCallback );
    };

    self.updateFeed = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);

        FeedApiService.updateFeed( self.current , self.SuccessCallBackRedirectList , self.updateFeedFailureCallback );
    };

    self.resetPassword = function() {
        var feedData  = angular.copy( self.current );
        FeedApiService.updatePassword( feedData , function(){ $mdToast.showSimple( 'Password Reset has been submitted' );} , self.updateFeedFailureCallback );

    };

    /**
     * Callbacks
     */
    self.loadFeedSuccessCallback = function ( response ) {
        self.current = response.data;
        self.current.vertical_id = String(response.data.vertical_id);
        self.current.client_id = String(response.data.client_id);
        self.current.type_id = String(response.data.type_id);
    };

    self.SuccessCallBackRedirectList = function ( response ) {
        $location.url( '/feed/');
        $window.location.href = '/feed/';
    };


    self.loadFeedFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load feed.' );

        modalService.launchModal();
    };

    self.loadFeedsSuccessCallback = function ( response ) {
        self.feeds = response.data.data;

        self.pageCount = response.data.last_page;

        self.feedTotal = response.data.total;

        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );
    };

    self.loadFeedsFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load feeds.' );

        modalService.launchModal();
    };

    self.updateFeedSuccessCallback = function () {
        modalService.setModalLabel( 'Update Feed' );
        modalService.setModalBody( 'Successfully updated feed.' );

        modalService.launchModal();
    };

    self.updateFeedFailureCallback = function (response) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors( self , response );
    };

    self.saveFeedFailureCallback = function (response) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors( self , response );
    };

} ] );
