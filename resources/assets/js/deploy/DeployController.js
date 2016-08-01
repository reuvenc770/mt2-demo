mt2App.controller( 'DeployController' , [ '$log' , '$window' , '$location' , '$timeout' , 'DeployApiService' , function ( $log , $window , $location , $timeout , DeployApiService ) {
    var self = this;
    self.$location = $location;
    self.deploy = {};
    self.currentDeploy = { send_date : '', deploy_id : '', esp_account_id : ''};
    self.headers =  ['Send Date'];
    self.espAccounts = [];
    self.currentlyLoading = 0;
    self.showRow = 1;
    self.formErrors = [];
    self.deploys = [];


    self.loadAccounts = function () {
        self.loadEspAccounts();
    };


    self.loadEspAccounts = function (){
      self.currentlyLoading = 1;
      DeployApiService.getEspAccounts(self.loadEspSuccess, self.loadEspFail);
    };



    /**
     * Callbacks
     */

    self.loadEspSuccess = function (response) {
        self.espAccounts = response.data;
        self.currentlyLoading = 0;
    };

    self.loadEspFail = function () {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Something went wrong loading ESPs' );
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
