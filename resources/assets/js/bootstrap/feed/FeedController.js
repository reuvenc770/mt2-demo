mt2App.controller( 'FeedController' , [ '$rootScope' , '$window' , '$location' , '$timeout', 'FeedApiService', '$mdDialog', '$log' , 'formValidationService' , 'modalService' , 'paginationService' , function ( $rootScope , $window , $location , $timeout , FeedApiService , $mdDialog , $log , formValidationService , modalService , paginationService ) {
    var self = this;
    self.$location = $location;

    self.current = {
        id: "",
        client_id: "" ,
        party: 1 ,
        name : '' ,
        short_name: "" ,
        password : '' ,
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
    self.paginationCount = paginationService.getDefaultPaginationCount();
    self.paginationOptions = paginationService.getDefaultPaginationOptions();
    self.currentPage = 1;
    self.feedTotal = 0;
    self.queryPromise = null;
    self.sort= "-id";

    self.currentFieldConfig = {};
    self.fieldList = [
        { "label" : "Email" , "field" : "email_index" , "required" : true } ,
        { "label" : "Source URL" , "field" : "source_url_index" , "required" : true } ,
        { "label" : "Capture Date" , "field" : "capture_date_index" , "required" : true } ,
        { "label" : "IP" , "field" : "ip_index" , "required" : true } ,
        { "label" : "First Name" , "field" : "first_name_index" } ,
        { "label" : "Last Name" , "field" : "last_name_index" } ,
        { "label" : "Address" , "field" : "address_index" } ,
        { "label" : "Address 2" , "field" : "address2_index" } ,
        { "label" : "City" , "field" : "city_index" } ,
        { "label" : "State" , "field" : "state_index" } ,
        { "label" : "Zip" , "field" : "zip_index" } ,
        { "label" : "Country" , "field" : "country_index" } ,
        { "label" : "Gender" , "field" : "gender_index" } ,
        { "label" : "Phone" , "field" : "phone_index" } ,
        { "label" : "Date Of Birth" , "field" : "dob_index" } ,
    ];
    self.selectedFields = [];
    self.customField = '';

    self.formSubmitted = false;
    self.isReattributing = false;
    self.isSuppressing = false;

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

    self.setId = function ( id ) {
        self.current.id = id;
    };

    self.setFields = function ( fields ) {
        angular.forEach( fields , function ( currentField , feedIndex ) {
            if ( typeof( currentField.isCustom ) !== 'undefined' && currentField.isCustom ) {
                self.customField = currentField.label;

                self.addCustomField();
            } else {
                angular.forEach( self.fieldList , function ( availField , index ) {
                    if ( currentField === availField.field ) {
                        var removedFields = self.fieldList.splice( index , 1 );

                        self.selectedFields.push( removedFields.pop() );

                        self.currentFieldConfig[ currentField ] = self.selectedFields.length - 1;
                    }
                } );
            }
        } );
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
        FeedApiService.updatePassword( feedData , self.resetPasswordSuccessCallback, self.updateFeedFailureCallback );

    };

    self.runReattribution = function() {

        var confirm = $mdDialog.confirm()
            .title( 'Confirm Reattribute Records' )
            .ariaLabel( 'Confirm Reattribute Records' )
            .textContent( 'Are you sure you want to reattribute this feed\'s non-unique records?' )
            .ok( 'Yes, I am sure.' )
            .cancel( 'No' );

        $mdDialog.show( confirm ).then( function () {
            self.isReattributing = true;

            FeedApiService.runReattribution( self.current.id , self.runReattributionSuccessCallback , self.runReattributionFailureCallback );
        });

    };

    self.createSuppression = function() {
        var confirmSupp = $mdDialog.confirm()
            .title( 'Confirm Feed Suppression' )
            .ariaLabel( 'Confirm Feed Suppression' )
            .textContent( 'Are you sure you want to suppress this feed\'s unique records?' )
            .ok( 'Yes, I am sure.' )
            .cancel( 'No' );

        $mdDialog.show( confirmSupp ).then( function () {
            self.isSuppressing = true;

            FeedApiService.createSuppression( self.current.id , self.createSuppressionSuccessCallback , self.createSuppressionFailureCallback );
        });
    };
    /**
     * Feed File Field Ordering
     */
    self.moveField = function ( droppedField , list , index ) {
        list.splice( index , 1 );

        self.currentFieldConfig = {};

        if ( list === self.fieldList && typeof( self.formErrors[ droppedField.field ] ) !== 'undefined' && self.formErrors[ droppedField.field ].length > 0 ) {
            delete( self.formErrors[ droppedField.field ] );
        }

        angular.forEach( self.selectedFields , function ( value , index ) {
            if ( typeof( value.isCustom ) === 'undefined' ) {
                self.currentFieldConfig[ value.field ] = index;
            } else {
                self.addCustomFieldToConfig( value.label , index );
            }
        } );
    };

    self.addCustomFieldToConfig = function ( name , index ) {
        if ( typeof( self.currentFieldConfig[ 'other_field_index' ] ) === 'undefined' ) {
            self.currentFieldConfig[ 'other_field_index' ] = {};
        }

        self.currentFieldConfig[ 'other_field_index' ][ name ] = index;
    };

    self.addCustomField = function () {
        if ( self.customField ) {
            self.selectedFields.push( { "label" : self.customField , "isCustom" : true } );

            self.addCustomFieldToConfig( self.customField , self.selectedFields.length - 1 );

            self.customField = '';
        }
    };

    self.saveFieldOrder = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors( self );

        FeedApiService.updateFeedFields(
            self.current.id ,
            self.currentFieldConfig ,
            self.SuccessCallBackRedirectList ,
            self.saveFieldOrderFailureCallback
        );
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
        modalService.simpleToast( 'Failed to load feed.' );
    };

    self.loadFeedsSuccessCallback = function ( response ) {
        self.feeds = response.data.data;

        self.pageCount = response.data.last_page;

        self.feedTotal = response.data.total;

        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );
    };

    self.loadFeedsFailureCallback = function ( response ) {
        modalService.simpleToast( 'Failed to load feeds.' );
    };

    self.updateFeedFailureCallback = function (response) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors( self , response );
    };

    self.saveFeedFailureCallback = function (response) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors( self , response );
    };

    self.saveFieldOrderFailureCallback = function ( response ) {
        self.formSubmitted = false;

        formValidationService.loadFieldErrors( self , response );

        modalService.simpleToast( 'Please include the missing required fields.' );
    };

    self.resetPasswordSuccessCallback = function () {
        modalService.setModalLabel( 'Success' );
        modalService.setModalBody( 'Password reset has been submitted.' );
        modalService.launchModal();
    }
    self.runReattributionSuccessCallback = function ( response ) {
        modalService.setModalLabel( 'Success' );
        modalService.setModalBody( 'Reattributing non-unique records.' );
        modalService.launchModal();
    };

    self.runReattributionFailureCallback = function ( response ) {
        self.isReattributing = false;

        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to reattribute records. Please contact support.' );
        modalService.launchModal();
    };

    self.createSuppressionSuccessCallback = function ( response ) {
        modalService.setModalLabel( 'Success' );
        modalService.setModalBody( 'Creating feed suppression.' );
        modalService.launchModal();
    };

    self.createSuppressionFailureCallback = function ( response ) {
        self.isSuppressing = false;

        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to create feed suppression. Please contact support.' );
        modalService.launchModal();
    };

} ] );
