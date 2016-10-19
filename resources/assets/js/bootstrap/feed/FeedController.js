mt2App.controller( 'FeedController' , [ '$rootScope' , '$window' , '$location' , 'FeedApiService', '$mdToast', '$mdDialog', function ( $rootScope , $window , $location , FeedApiService, $mdToast , $mdDialog ) {
    var self = this;

    self.current = {
        address: "" ,
        address2: "" ,
        cake_sub_id: "" ,
        check_global_suppression: "Y" ,
        check_previous_oc: "0" ,
        city: "" ,
        feed_has_client_restrictions: "0" ,
        feed_id: "" ,
        feed_main_name: "" ,
        feed_record_ip: "" ,
        feed_record_source_url: "" ,
        feed_type: "" ,
        country_id: "" ,
        email_addr: "" ,
        ftp_pw: "" ,
        ftp_url: "" ,
        ftp_user: "" ,
        has_client_restriction: "0" ,
        list_owner: "" ,
        feedTypeId: "",
        minimum_acceptable_record_date: "" ,
        network: "" ,
        orange_client: "Y" ,
        password: "" ,
        phone: "" ,
        rt_pw: "" ,
        state: "" ,
        status: "D" ,
        username: "" ,
        zip: "",
        payout_type: "",
        payout_amount: "0"
    };

    self.feeds = [];

    self.createUrl = '/feed/create';

    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.feedTotal = 0;
    self.queryPromise = null;

    self.generatingLinks = 0;
    self.updatingFeed = 0;
    self.creatingFeed = 0;

    self.feedTypes = [];
    self.typeSearchText = '';
    self.formErrors = [];
    self.listOwners = [];
    self.ownerSearchText = '';

    self.urlList = [];

    /**
     * Init Methods
     */
    self.loadAutoComplete = function () {
        self.loadFeedTypes();
        self.loadListOwners();
    };

    self.loadFeed = function () {
        var currentPath = $location.path();
        var matches = currentPath.match( /\/(\d{1,})/ );
        var id = matches[ 1 ];

        FeedApiService.getFeed( id , self.loadFeedSuccessCallback , self.loadFeedFailureCallback );
    };

    self.loadFeeds = function () {

        self.queryPromise = FeedApiService.getFeeds( self.currentPage , self.paginationCount , self.loadFeedsSuccessCallback , self.loadFeedsFailureCallback );
    };


    /**
     * Button Click Handlers
     */
    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };

    self.generateLinks = function () {
        if ( self.urlList.length === 0 ) {
            self.generatingLinks = 1;

            FeedApiService.generateLinks(
                self.current.feed_id ,
                self.generateLinksSuccessCallback ,
                self.generateLinksFailureCallback
            );
        } else {
            $mdDialog.show({
                contentElement: '#urlModal',
                clickOutsideToClose: true,
                disableParentScroll: false
            });
        }
    };

    self.closeUrlModal = function () {
        $mdDialog.cancel();
    };
    /**
     * Watchers
     */
    $rootScope.$on( 'updatePage' , function () {
        self.loadFeeds();
    } );


    /**
     * Form Methods
     */

    self.getFeedData = function () {
        var feedData = {};

        angular.forEach( self.current , function ( field , fieldName ) {
            if ( typeof( field ) == 'object' ) {
                this[ fieldName ] = field.value;
            } else {
                this[ fieldName ] = field;
            }
        } , feedData );

        return feedData;
    };


    /**
     * Look-forwward Fields
     */
    self.getFeedType = function ( searchText ) {
        return searchText ? self.clientTypes.filter( function ( obj ) { return obj.name.toLowerCase().indexOf( searchText.toLowerCase() ) === 0; } ) : self.feedTypes;
    };

    self.loadFeedTypes = function () {
        FeedApiService.getTypes( self.loadFeedTypesSuccessCallback , self.loadFeedTypesFailureCallback );
    };

    self.getListOwners = function ( searchText ) {
        return searchText ? self.listOwners.filter( function ( obj ) { return obj.name.toLowerCase().indexOf( searchText.toLowerCase() ) === 0; } ) : self.listOwners;
    };

    self.loadListOwners = function () {
        FeedApiService.getListOwners( self.loadListOwnersSuccessCallback , self.loadListOwnersFailureCallback );
    };

    self.loadListOwnersSuccessCallback = function ( response ) {
        self.listOwners = response.data;
    };

    self.loadListOwnersFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load feed types.' );

        self.launchModal();
    };

    self.updateFeed = function () {
        self.resetFieldErrors();
        var feedData = angular.copy( self.current );
        feedData.list_owner = self.current.list_owner.name;
        feedData.feed_type = self.current.feed_type.value;
        FeedApiService.updateFeed( feedData , self.SuccessCallBackRedirectList , self.updateFeedFailureCallback );
    };

    self.resetPassword = function() {
        var feedData  = angular.copy( self.current );
        FeedApiService.updatePassword( feedData , function(){ $mdToast.showSimple( 'Password Reset has been submitted' );} , self.updateFeedFailureCallback );

    };

    self.saveFeed = function ( event , form ) {
        self.resetFieldErrors();

        var errorFound = false;

        angular.forEach( form.$error.required , function( field ) {
            field.$setDirty();
            field.$setTouched();

            if ( field.$name == 'state' ) {
                form.state.$error.required = true;
            }

            errorFound = true;
        } );

        if ( errorFound ) {
            $mdToast.showSimple( 'Please fix errors and try again.' );

            return false;
        };

        var feedData = angular.copy( self.current );

        feedData.list_owner = self.current.list_owner.name;
        feedData.newFeed = 1;
        feedData.feed_type = self.current.feed_type.value;

        FeedApiService.saveFeed( feedData , self.SuccessCallBackRedirect , function( response ) {
            angular.forEach( response.data , function( error , fieldName ) {

                form[ fieldName ].$setDirty();
                form[ fieldName ].$setTouched();
                form[ fieldName ].$setValidity('isValid' , false);
            });

            self.saveFeedFailureCallback( response );
        });
    };

    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };

    /**
     * Page Modal
     */
    self.setModalLabel = function ( labelText ) {
        var modalLabel = angular.element( document.querySelector( '#pageModalLabel' ) );

        modalLabel.text( labelText );
    };

    self.setModalBody = function ( bodyText ) {
        var modalBody = angular.element( document.querySelector( '#pageModalBody' ) );

        modalBody.text( bodyText );
    }

    self.launchModal = function () {
        $( '#pageModal' ).modal('show');
    };

    self.resetModal = function () {
        self.setModalLabel( '' );
        self.setModalBody( '' );

        $( '#pageModal' ).modal('hide');
    };


    /**
     * Callbacks
     */
    self.loadFeedSuccessCallback = function ( response ) {
        var currentRecord = response.data[ 0 ];

        currentRecord.country_id = parseInt( currentRecord[ 'country_id' ] );
        currentRecord.client_type = {name:currentRecord[ 'feed_type'],value:currentRecord[ 'feed_type']};
        currentRecord.list_owner = {name:currentRecord[ 'list_owner'],value:currentRecord[ 'list_owner']};

        self.current = currentRecord;
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/feed/edit/'+ response.data.clientId );
        $window.location.href = '/feed/edit/' + response.data.clientId;
    };
    self.SuccessCallBackRedirectList = function ( response ) {
        $location.url( '/feed/');
        $window.location.href = '/feed/';
    };


    self.loadFeedFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load feed.' );

        self.launchModal();
    };

    self.loadFeedsSuccessCallback = function ( response ) {
        self.feeds = response.data.data;

        self.pageCount = response.data.last_page;

        self.feedTotal = response.data.total;
    };

    self.loadFeedsFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load feeds.' );

        self.launchModal();
    };

    self.updateFeedSuccessCallback = function () {
        self.setModalLabel( 'Update Feed' );
        self.setModalBody( 'Successfully updated feed.' );

        self.launchModal();
    };

    self.updateFeedFailureCallback = function (response) {
        self.loadFieldErrors(response);
    };

    self.saveFeedFailureCallback = function (response) {
        self.loadFieldErrors(response);
    };

    /**
     * Errors
     */
    self.loadFieldErrors = function (response ) {
        angular.forEach(response.data, function(value, key) {
            self.setFieldError( key , value );
        });
    };

    self.loadFeedTypesSuccessCallback = function ( response ) {
        self.clientTypes = response.data;
    };

    self.loadFeedTypesFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load feed types.' );

        self.launchModal();
    };

    self.loadListOwnersSuccessCallback = function ( response ) {
        self.listOwners = response.data;
    };

    self.loadListOwnersFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load feed types.' );

        self.launchModal();
    };

    self.generateLinksSuccessCallback = function ( response ) {
        self.generatingLinks = 0;

        self.urlList = response.data;

        $mdDialog.show({
            contentElement: '#urlModal',
            clickOutsideToClose: true,
            disableParentScroll: false
        });
    };

    self.generateLinksFailureCallback = function ( response ) {
        self.generatingLinks = 0;

        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to generate links.' );

        self.launchModal();
    }

    self.setFieldError = function ( field , errorMessage ) {
        self.formErrors[ field ] = errorMessage;
    };

    self.resetFieldErrors = function () {
        self.formErrors = {};
    };

} ] );
