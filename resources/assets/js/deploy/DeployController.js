mt2App.controller( 'DeployController' , [ '$log' , '$window' , '$location' , '$timeout' , 'DeployApiService', '$mdToast'  , function ( $log , $window , $location , $timeout , DeployApiService, $mdToast ) {
    var self = this;
    self.$location = $location;
    self.currentDeploy = { send_date : '',
                            deploy_id : '',
                            esp_account_id : '',
                            offer_id: "",
                            mailing_domain_id: "",
                            content_domain_id : "",
                            template_id: "",
                            cake_affiliate_id: "",
                            notes : ""
                            };
    self.espAccounts = [];
    self.currentlyLoading = 0;
    self.templates = [];
    self.cakeAffiliates = [];
    self.espLoaded = true;
    self.showRow = false;
    self.offerLoading =true;
    self.mailingDomains = []; //id is 1
    self.contentDomains = []; //id is 2
    self.offers = [];
    self.formErrors = [];
    self.deploys = [];
    self.searchText = "";
    self.listProfiles = [];

    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;


    self.loadAccounts = function () {
        self.loadEspAccounts();
        self.loadAffiliates();
        self.loadListProfiles();
        self.loadDeploys();
        self.currentlyLoading = 0;
    };


    self.loadEspAccounts = function (){
      self.currentlyLoading = 1;
      DeployApiService.getEspAccounts(self.loadEspSuccess, self.loadEspFail);
    };

    self.loadDeploys = function () {
        self.currentlyLoading = 1;
        DeployApiService.getDeploys(self.currentPage , self.paginationCount, self.loadDeploySuccess, self.loadDeployFail);
    };

    self.loadListProfiles = function () {
        self.currentlyLoading = 1;
        DeployApiService.getListProfiles(self.loadProfileSuccess, self.loadProfileFail)
    };

    self.updateSelects = function () {
        DeployApiService.getMailingDomains(self.currentDeploy.esp_account_id, 1, self.updateMailingSuccess,self.updateDomainsFail);
        DeployApiService.getMailingDomains(self.currentDeploy.esp_account_id, 2, self.updateContentSuccess,self.updateDomainsFail);
        DeployApiService.getTemplates(self.currentDeploy.esp_account_id, self.updateTemplateSuccess, self.updateTemplateFail);
    };

    self.loadAffiliates = function () {
        DeployApiService.getCakeAffiliates(self.loadCakeSuccess, self.loadCakeFail);
    };

    self.displayForm = function () {
        self.showRow = true;
    };

    self.saveNewDeploy = function () {
        self.currentDeploy.deploy_id = undefined; //faster then delete
        DeployApiService.insertDeploy(self.currentDeploy, self.loadNewDeploySuccess, self.loadNewDeployFail);
    };

    self.offerWasSelected = function (item) {

        if(item){
            self.reloadCFS(item.originalObject.id);
            self.currentDeploy.offer_id = item.originalObject.id;
        }
        self.offerLoading = false;
    };
    self.reloadCFS = function(offerId){
        DeployApiService.getCreatives(offerId, self.updateCreativesSuccess, self.updateCreativesFail);
        DeployApiService.getSubjects(offerId, self.updateSubjectsSuccess, self.updateSubjectsFail);
        DeployApiService.getFroms(offerId, self.updateFromsSuccess, self.updateFromsFail);
    };






    /**
     * Callbacks
     */
    self.loadDeploySuccess = function (response) {
        self.deploys = response.data.data;
        self.pageCount = response.data.last_page;
    };

    self.loadEspSuccess = function (response) {
        self.espAccounts = response.data;
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

    self.loadCakeSuccess = function (response) {
        self.cakeAffiliates = response.data;
    };

    self.loadProfileSuccess = function (response) {
      self.listProfiles = response.data;
    };

    self.loadNewDeploySuccess = function (response) {
        self.currentDeploy =  { send_date : '',
            deploy_id : '',
            esp_account_id : '',
            offer_id: "",
            mailing_domain_id: "",
            content_domain_id : "",
            template_id: "",
            cake_affiliate_id: "",
            notes : ""
        };
        $mdToast.showSimple( 'New Deploy Created!' );
        DeployApiService.getDeploys(self.currentPage , self.paginationCount, self.loadDeploySuccess, self.loadDeployFail);

    };

    self.updateCreativesSuccess = function (response){
        self.creatives = response.data;
    };

    self.updateFromsSuccess = function (response){
      self.froms = response.data;
    };

    self.updateSubjectsSuccess = function (response){
        self.subjects = response.data;
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

    self.loadDeployFail = function (){
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Something went wrong loading Deploys' );
        self.launchModal();
    };

    self.loadCakeFail = function () {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Something went wrong loading Cake Affiliates' );
        self.launchModal();
    };

    self.loadProfileFail = function () {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Something went wrong loading List Profiles' );
        self.launchModal();
    };

    self.updateCreativesFail = function () {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Something went wrong loading Creatives' );
        self.launchModal();
    };

    self.updateFromsFail = function () {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Something went wrong loading Froms' );
        self.launchModal();
    };

    self.updateSubjectsFail = function () {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Something went wrong loading Subjects' );
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
