mt2App.controller( 'DeployController' , [ '$log' , '$window' , '$location' , '$timeout' , 'DeployApiService' , function ( $log , $window , $location , $timeout , DeployApiService ) {
    var self = this;
    self.$location = $location;
    self.currentDeploy = { send_date : '', deploy_id : '', esp_account_id : ''};
    self.headers =  ['Send Date'];
    self.espAccounts = [];
    self.currentlyLoading = 0;
    self.showRow = 1;
    self.offers = [];
    self.formErrors = [];
    self.deploys = [];
    self.searchText = "";


    self.loadAccounts = function () {
        self.loadEspAccounts();
    };


    self.loadEspAccounts = function (){
      self.currentlyLoading = 1;
      DeployApiService.getEspAccounts(self.loadEspSuccess, self.loadEspFail);
    };


    self.typeAheadSearch = function (term){
        if(term == undefined) {
            return self.offers;
        }
        console.log(term);
        self.currentlyLoading = 1;
        DeployApiService.getOffersSearch(term, self.loadOfferSuccess, self.loadOfferFail)
    };

    /**
     * Callbacks
     */

    self.loadEspSuccess = function (response) {
        self.espAccounts = response.data;
        self.currentlyLoading = 0;
    };

    self.loadOfferSuccess = function (response){
        self.offers = response.data;
        self.currentlyLoading = 0;
        return self.offers;
    };

    self.loadEspFail = function () {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Something went wrong loading ESPs' );
        self.launchModal();
    };
    self.loadOfferFail = function (){
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Something went wrong loading Offers' );
        self.launchModal();
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
    };

    self.launchModal = function () {
        $( '#pageModal' ).modal('show');
    };

    self.resetModal = function () {
        self.setModalLabel( '' );
        self.setModalBody( '' );

        $( '#pageModal' ).modal('hide');
    };
} ] );
