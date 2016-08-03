mt2App.controller( 'DeployController' , [ '$log' , '$window' , '$location' , '$timeout' , 'DeployApiService' , function ( $log , $window , $location , $timeout , DeployApiService ) {
    var self = this;
    self.$location = $location;
    self.currentDeploy = { send_date : '',
                            deploy_id : '',
                            esp_account_id : '',
                            offer_id: "",
                            mailing_domain_id: "",
                            content_domain_id : "",
                            template_id: ""
                            };
    self.espAccounts = [];
    self.currentlyLoading = 0;
    self.templates = [];
    self.espLoaded = true;
    self.showRow = 1;
    self.mailingDomains = []; //id is 1
    self.contentDomains = []; //id is 2
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

    self.updateSelects = function () {
        DeployApiService.getMailingDomains(self.currentDeploy.esp_account_id, 1, self.updateMailingSuccess,self.updateDomainsFail);
        DeployApiService.getMailingDomains(self.currentDeploy.esp_account_id, 2, self.updateContentSuccess,self.updateDomainsFail);
        DeployApiService.getTemplates(self.currentDeploy.esp_account_id, self.updateTemplateSuccess, self.updateTemplateFail);
    };



    /**
     * Callbacks
     */

    self.loadEspSuccess = function (response) {
        self.espAccounts = response.data;
        self.currentlyLoading = 0;
    };

    self.updateContentSuccess = function (response){
            self.contentDomains = response.data;
    };

    self.updateMailingSuccess = function (response){
        self.mailingDomains = response.data;
    };

    self.updateTemplateSuccess = function (response) {
        self.templates = response.data;
        self.espLoaded = false;
    };

    self.loadEspFail = function () {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Something went wrong loading ESPs' );
        self.launchModal();
    };
    self.updateDomainsFail = function (){
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Something went wrong loading Offers' );
        self.launchModal();
    };

    self.updateTemplateFail = function (){
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Something went wrong loading Templates' );
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
