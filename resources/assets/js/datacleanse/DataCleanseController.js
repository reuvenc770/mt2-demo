mt2App.controller( 'DataCleanseController' , [ '$rootScope' , '$window' , '$location' , '$log' , 'DataCleanseApiService' , 'formValidationService' , 'modalService' , function ( $rootScope , $window , $location , $log , DataCleanseApiService , formValidationService , modalService ) {
    var self = this;

    self.createUrl = '/datacleanse/create';

    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.cleanseTotal = 0;
    self.sort = 'name';
    self.queryPromise = null;
    self.creatingCleanse = false;

    self.current = { "exportType" : "Cleanse" , "ConfirmEmail" : "alphateam@zetainteractive.com" , "includeHeaders" : "Y" };
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
        self.queryPromise = DataCleanseApiService.getAll( self.currentPage , self.paginationCount , self.sort , self.loadCleanseSuccessCallback, self.loadCleanseFailureCallback );
    };

    self.loadAdvertisers = function () {
        DataCleanseApiService.getAdvertisers( self.loadAdvertisersSuccessCallback , self.loadAdvertisersFailureCallback );
    };

    self.loadCountries = function () {
        DataCleanseApiService.getCountries( self.loadCountriesSuccessCallback , self.loadCountriesFailureCallback );
    };

    self.loadOfferCategories = function () {
        DataCleanseApiService.getOfferCategories( self.loadOfferCategoriesSuccessCallback , self.loadOfferCategoriesFailureCallback );
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
        self.creatingCleanse = true;
        formValidationService.resetFieldErrors(self);

        DataCleanseApiService.save( self.current , self.saveCleanseSuccessCallback , self.saveCleanseFailureCallback );

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

    /**
     * Callbacks
     */
    self.loadCleanseSuccessCallback = function ( response ) {
        self.cleanses = response.data.data;
        self.pageCount = response.data.last_page;
        self.cleanseTotal = response.data.total;
    };

    self.loadCleanseFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load Data Cleanses. Please contact support.' );
        modalService.launchModal();
    };

    self.loadAdvertisersSuccessCallback = function ( response ) {
        self.advertisers = response.data;
    };

    self.loadAdvertisersFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load Advertiser Suppression. Please contact support.' );
        modalService.launchModal();
    };

    self.loadCountriesSuccessCallback = function ( response ) {
        self.countries = response.data;
    };

    self.loadCountriesFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load Suppression Countries. Please contact support.' );
        modalService.launchModal();
    };

    self.loadOfferCategoriesSuccessCallback = function ( response ) {
        self.offerCategories = response.data;
    };

    self.loadOfferCategoriesFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load Suppression Offer Countries. Please contact support.' );
        modalService.launchModal();
    };

    self.saveCleanseSuccessCallback = function ( response ) {
        self.creatingCleanse = false;
        modalService.setModalLabel( 'Success' );
        modalService.setModalBody( 'Successfully saved Data Cleanse.' );
        modalService.launchModal();
    };

    self.saveCleanseFailureCallback = function ( response ) {
        self.creatingCleanse = false;
        formValidationService.loadFieldErrors( self , response );
    };

} ] );
