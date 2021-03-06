mt2App.controller( 'ymlpCampaignController' , [ '$rootScope' , '$log' , '$window' , '$location' , '$timeout' , '$mdToast', 'YmlpCampaignApiService' , 'CustomValidationService' , function ( $rootScope , $log , $window , $location , $timeout , $mdToast , YmlpCampaignApiService,  CustomValidationService ) {
    var self = this;
    self.$location = $location;

    self.campaigns = [];
    self.currentCampaign = {"esp_account_id" : "" , "sub_id" : "" , "date" : ""};
    self.createUrl = 'ymlp/ymlp-campaign/create/';
    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.campaignTotal = 0;
    self.sort = '-id';
    self.queryPromise = null;
    self.formErrors = {};

    self.loadCampaign = function () {
        var pathMatches = $location.path().match( /^\/ymlp\/ymlp-campaign\/edit\/(\d{1,})/ );

        YmlpCampaignApiService.getCampaign( pathMatches[ 1 ] , function ( response ) {
            self.currentCampaign = response.data;

        } )
    }

    self.loadCampaigns = function () {
        self.queryPromise = YmlpCampaignApiService.getCampaigns(
            self.currentPage ,
            self.paginationCount ,
            self.sort ,
            self.loadCampaignSuccessCallback , self.loadCampaignsFailureCallback );
    };

    self.resetCurrentAccount = function () {

    };

    /**
     * Watchers
     */
    $rootScope.$on( 'updatePage' , function () {
        self.loadCampaigns();
    } );

    /**
     * Click Handlers
     */
    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };

    self.change = function ( form , fieldName ) {
        CustomValidationService.onChangeResetValidity( self , form , fieldName );
    };

    self.saveNewCampaign = function ( event , form ) {
        self.resetFieldErrors();

        var errorFound = false;

        angular.forEach( form.$error.required , function( field ) {
            field.$setDirty();
            field.$setTouched();

            errorFound = true;
        });

        if ( errorFound ) {
            $mdToast.showSimple( 'Please fix errors and try again.' );

            return false;
        }

        YmlpCampaignApiService.saveNewCampaign( self.currentCampaign , self.SuccessCallBackRedirect , function( response ) {
            angular.forEach( response.data , function( error , fieldName ) {

                form[fieldName].$setDirty();
                form[fieldName].$setTouched();
                form[fieldName].$setValidity('isValid' , false);
            });

            self.saveNewCampaignFailureCallback( response );
        });
    };

    self.editCampaign = function () {
        self.resetFieldErrors();

        YmlpCampaignApiService.editCampaign( self.currentCampaign , self.SuccessCallBackRedirect , self.editCampaignFailureCallback );
    }

    /**
     * Callbacks
     */
    self.loadCampaignSuccessCallback = function ( response ) {
        self.campaigns = response.data.data;
        self.pageCount = response.data.last_page;
        self.campaignTotal = response.data.total;
    };

    self.loadCampaignsFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load YMLP Campaigns' );

        self.launchModal();
    }

    self.saveNewCampaignFailureCallback = function ( response ) {
        self.loadFieldErrors( response );
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/ymlp/ymlp-campaign' );
        $window.location.href = '/ymlp/ymlp-campaign';
    };

    self.editCampaignFailureCallback = function ( response ) {
        self.loadFieldErrors( response );
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
     * Errors
     */
    self.loadFieldErrors = function (response ) {
        angular.forEach(response.data, function(value, key) {
            self.setFieldError( key , value );
        });
    };

    self.setFieldError = function ( field , errorMessage ) {
        self.formErrors[ field ] = errorMessage;
    };

    self.resetFieldErrors = function () {
        self.formErrors = {};
    };
} ] );
