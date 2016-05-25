mt2App.controller( 'DataCleanseController' , [ '$rootScope' , '$window' , '$location', '$mdToast' , '$anchorScroll' , '$log' , 'DataCleanseApiService' , function ( $rootScope , $window , $location , $mdToast , $anchorScroll , $log , DataCleanseApiService ) {
    var self = this;

    self.createUrl = '/datacleanse/create';

    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;

    self.current = { "exportType" : "Cleanse" , "ConfirmEmail" : "alphateam@zetainteractive.com" };
    self.cleanses = [];

    self.advertisers = [];
    self.selectedAdvertisers = [];
    self.availableAdvertiserWidgetTitle = "Available Advertisers";
    self.chosenAdvertiserWidgetTitle = "Chosen Advertisers";

    self.countries = [];
    self.selectedCountries = [];
    self.availableCountryWidgetTitle = "Available Countries";
    self.chosenCountryWidgetTitle = "Chosen Countries";

    self.offerCategories = [];
    self.selectedOfferCategories = [];
    self.availableCategoryWidgetTitle = "Available Offer Categories";
    self.chosenCategoryWidgetTitle = "Chosen Offer Categories";

    /**
     * Loading Methods
     */
    self.load = function () {
        DataCleanseApiService.getAll( self.currentPage , self.paginationCount , function ( response ) {
            self.cleanses = response.data.data; 
            self.pageCount = response.data.last_page;
        } , function ( response ) {
            $mdToast.showSimple( 'Failed to load Data Cleanses. Please contact support.' );
        } );
    };

    self.loadAdvertisers = function () {
        DataCleanseApiService.getAdvertisers( function ( response ) { 
            self.advertisers = response.data;
        } , function ( response ) {
            $mdToast.showSimple( 'Failed to load Advertiser Suppression. Please contact support.' );
        } );
    };

    self.loadCountries = function () {
        DataCleanseApiService.getCountries( function ( response ) {
            self.countries = response.data;
        } , function ( response ) {
            $mdToast.showSimple( 'Failed to load Suppression Countries. Please contact support.' );
        } );
    };

    self.loadOfferCategories = function () {
        DataCleanseApiService.getOfferCategories( function ( response ) {
            self.offerCategories = response.data;
        } , function ( response ) {
            $mdToast.showSimple( 'Failed to load Suppression Offer Categories. Please contact support.' );
        } );
    };

    /**
     * Watchers
     */
    $rootScope.$on( 'updatePage' , function () { self.load(); } );

    /**
     * Click Handlers
     */
    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    }

    self.saveCleanse = function ( $event , form ) {
        if ( self.selectedAdvertisers.length <= 0 ) {
            $anchorScroll( 'suppressionAdvertisers' );

            $mdToast.showSimple( 'At least 1 Advertiser is required.' );

            return false;
        }

        var errorFound = false;

        angular.forEach( form.$error.required , function ( field ) {
            field.$setDirty();
            field.$setTouched();

            errorFound = true;
        } );

        if ( errorFound ) {
            $mdToast.showSimple( 'Please fix errors and try again.' );

            return false;
        }

        DataCleanseApiService.save( self.current , function ( response ) {
            $mdToast.showSimple( 'Successfully saved Data Cleanse.' );
        } , function ( response ) {
            if ( typeof( response.data.pname ) !== 'undefined' ) {
                $mdToast.showSimple( 'Data Export Filename is required. Please choose one and try again.' );
            } else if ( typeof( response.data.aid ) !== 'undefined' ) {
                $anchorScroll( 'suppressionAdvertisers' );

                $mdToast.showSimple( 'At least 1 Advertiser is required. Please choose one and try again.' );
            } else if ( typeof( response.data.ConfirmEmail ) !== 'undefined' ) {
                $mdToast.showSimple( 'A confirmation email is required.' );
            } else {
                $mdToast.showSimple( 'Failed to save Data Cleanse. Please try again.' );
            }
        } );
    };

    /**
     * Membership Widget Callbacks
     */
    self.advertiserMembershipCallback = function () {
        var advertiserIdList = [];

        angular.forEach( self.selectedAdvertisers , function ( advertiser , advertiserIndex ) {
            advertiserIdList.push( advertiser.id );
        } );

        self.current.aid = advertiserIdList;
    };

    self.countryMembershipCallback = function () {
        var countryIdList = [];

        angular.forEach( self.selectedCountries , function ( country , countryIndex ) {
            countryIdList.push( country.id );
        } );

        self.current.scountryID = countryIdList;
    };

    self.offerCategoryMembershipCallback = function () {
        var offerCategoryIdList = [];

        angular.forEach( self.selectedOfferCategories , function ( category , countryIndex ) {
            offerCategoryIdList.push( category.id );
        } );

        self.current.scatid = offerCategoryIdList;
    };

} ] );
