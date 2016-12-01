mt2App.controller('SourceUrlSearchController' , [ '$rootScope' , '$window' , '$location' , '$timeout', '$log' , 'FeedApiService' , 'formValidationService', 'modalService' , 'orderByFilter' , '$anchorScroll' , function ( $rootScope , $window , $location , $timeout , $log , FeedApiService , formValidationService, modalService , orderBy , $anchorScroll ) {
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

    self.queryPromise = null;
    self.recordCounts = [];

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

    self.searchSourceUrl = function () {
        formValidationService.resetFieldErrors( self );

        self.queryPromise = FeedApiService.searchSourceUrl(
            self.search ,
            self.searchSourceUrlSuccessCallback ,
            self.searchSourceUrlFailureCallback
        );
    };

    self.downloadCsv = function ( csv ) {
        var blob = new Blob( [ csv ] , { "type" : "text/csv;charset=utf-8" } );
        var filename = "source_url_record_count_" + self.search.source_url + "_" + self.search.startDate + "_" + self.search.endDate +  ".csv";

        if ( navigator.msSaveBlob ) { // IE 10+
            navigator.msSaveBlob( blob , filename );
        } else {
            var link = document.createElement( 'a' );

            if ( typeof( link.download ) !== 'undefined' ) {
                var windowUrl = (window.URL || window.webkitURL);
                var url = windowUrl.createObjectURL( blob );

                link.setAttribute( "href" , url );
                link.setAttribute( "download" , filename );
                document.body.appendChild( link );
                link.click();

                document.body.removeChild( link );
                windowUrl.revokeObjectURL( blob );
            } else {
                modalService.setModalLabel('Error');
                modalService.setModalBody( 'Cannot download csv file. Please use a modern browser.' );
                modalService.launchModal();
            }
        }
    };

    /**
     * Success Callbacks
     */
    self.getAllFeedsSuccessCallback = function ( response ) {
        self.feedList = response.data;
    };

    self.updateCurrentClientList = function () {
        var clientIdList = [];

        angular.forEach( self.selectedClients , function ( client ) {
            clientIdList.push( client.id );
        } );

        self.search.clientIds = clientIdList;
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

    self.searchSourceUrlSuccessCallback = function ( response ) {
        self.recordCounts = response.data.records;

        if ( response.data.records.length <= 0 ) {
            modalService.setModalLabel('Error');
            modalService.setModalBody( 'No Records' );
            modalService.launchModal();
        }

        if ( typeof( response.data.csv ) !== 'undefined' ) {
            self.downloadCsv( response.data.csv );
        }

        $timeout( function () {
            $location.hash( 'tableLoaded' );

            $anchorScroll();
        } , 200 );
    }

    /**
     * Failure Callbacks
     */
    self.getAllFeedsFailureCallback = function ( response ) {
        modalService.simpleToast( 'Failed to load list of feeds.' );
    };

    self.searchSourceUrlFailureCallback = function ( response ) {
        formValidationService.loadFieldErrors( self , response );
    };
}] );
