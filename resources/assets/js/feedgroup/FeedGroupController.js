mt2App.controller( 'FeedGroupController' , [ '$rootScope' , '$log' , '$window' , '$location' , 'FeedGroupApiService' , 'FeedApiService' , 'formValidationService' , 'modalService' , '$timeout' , 'paginationService', function ( $rootScope , $log , $window , $location , FeedGroupApiService , FeedApiService , formValidationService , modalService , $timeout , paginationService ) {
    var self = this;

    /**
     * Pagination Properties
     */
    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = paginationService.getDefaultPaginationCount();
    self.paginationOptions = paginationService.getDefaultPaginationOptions();
    self.currentPage = 1;
    self.feedGroupTotal = 0;
    self.sort = '-id';
    self.queryPromise = null;

    /**
     * Data Fields and Containers
     */
    self.feedGroups = [];
    self.current = {
        "name" : "" ,
        "feeds" : []
    };
    self.formErrors = [];

    self.creatingFeedGroup = false;
    self.updatingFeedGroup = false;
    self.feedList = [];
    self.feedListNameField = "feedListDisplayName";
    self.prepopFeeds = [];

    /**
     * Init Methods
     */
    self.loadFeedGroups = function () {
        self.queryPromise = FeedGroupApiService.getFeedGroups(
            self.currentPage ,
            self.paginationCount ,
            self.sort ,
            self.loadFeedGroupsSuccessCallback ,
            self.loadFeedGroupsFailureCallback
        );
    };

    self.loadFeedList = function () {
        FeedApiService.getAllFeeds( self.getAllFeedsSuccessCallback , self.getAllFeedsFailureCallback );
    };

    self.setId = function ( id ) {
        self.current.id = id;
    };

    self.setName = function ( name ) {
        self.current.name = name;
    };

    self.setFeeds = function ( feeds ) {
        self.prepopFeeds = feeds;

    };

    /**
     * Button Click Handlers
     */
    self.saveFeedGroup = function () {
        if ( !self.creatingFeedGroup ) {
            formValidationService.resetFieldErrors(self);
            self.creatingFeedGroup = true;

            var requestFeedList = [];
            angular.forEach( self.current.feeds , function ( value , key ) {
                requestFeedList.push( value.id );
            } );

            FeedGroupApiService.createFeedGroup(
                { "name" : self.current.name , "feeds" : requestFeedList } ,
                self.SuccessCallBackRedirect ,
                self.saveFeedGroupFailureCallback
            );
        }
    };

    self.updateFeedGroup = function ( event ) {
        if ( !self.updatingFeedGroup ) {
            formValidationService.resetFieldErrors(self);
            self.updatingFeedGroup = true;

            var requestFeedList = [];
            angular.forEach( self.current.feeds , function ( value , key ) {
                requestFeedList.push( value.id );
            } );

            FeedGroupApiService.updateFeedGroup(
                { "id" : self.current.id , "name" : self.current.name , "feeds" : requestFeedList } ,
                self.SuccessCallBackRedirect ,
                self.updateFeedGroupFailureCallback
            );
        }
    };

    /**
     * Success Callbacks
     */
    self.getAllFeedsSuccessCallback = function ( response ) {

        var sortFeedList = [];

        angular.forEach( response.data , function ( value ) {
            value.feedListDisplayName = value.short_name + " ( " + value.id + " ) " ;
            sortFeedList.push(value);
        } );

        self.feedList = sortFeedList;

        if ( self.prepopFeeds.length > 0 ) {
            var feedsToRemove = [];

            angular.forEach( self.feedList , function ( value , index ) {
                if ( self.prepopFeeds.indexOf( value.id ) >= 0 ) {
                    feedsToRemove.push( value );
                    self.current.feeds.push( value );
                }
            } );

            angular.forEach( feedsToRemove , function ( value , index ) {
                self.feedList.splice( self.feedList.indexOf( value ) , 1 );
            } );
        }
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/feedgroup' );
        $window.location.href = '/feedgroup';
    };

    self.loadFeedGroupsSuccessCallback = function ( response ) {
        self.currentlyLoading = 0;
        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );

        self.feedGroups = response.data.data
        self.pageCount = response.data.last_page;
        self.feedGroupTotal = response.data.total;
    };

    /**
     * Failure Callbacks
     */
    self.getAllFeedsFailureCallback = function ( response ) {
        modalService.simpleToast( 'Failed to load feed group\'s list of feeds.' );
    };

    self.saveFeedGroupFailureCallback = function ( response ) {
        self.creatingFeedGroup = false;

        formValidationService.loadFieldErrors( self , response );
    };

    self.updateFeedGroupFailureCallback = function ( response ) {
        self.updatingFeedGroup = false;

        formValidationService.loadFieldErrors( self , response );
    };

    self.loadFeedGroupsFailureCallback = function ( response ) {
        modalService.simpleToast( 'Failed to load feed groups.' );
    };
} ] );
