mt2App.controller( 'MailingTemplateController' , [  '$rootScope' ,'$log' , '$window' , '$location' , '$timeout' , 'MailingTemplateApiService' , '$mdToast' , 'formValidationService', 'modalService' , function ( $rootScope, $log , $window , $location , $timeout , MailingTemplateApiService , $mdToast , formValidationService, modalService ) {
    var self = this;
    self.$location = $location;

    self.createUrl = 'mailingtemplate/create/';
    self.editUrl = 'mailingtemplate/edit/';
    self.formErrors = "";

    /**
     * membership widget Field Properties
     */
    self.selectedEsps = [];
    self.espList = [];
    self.currentAccount = { name : "", templateType : "", selectedEsps :[], html : "", text : "" };
    self.availableWidgetTitle = "Available ESP Accounts";
    self.chosenWidgetTitle = "Chosen ESP Accounts";
    self.espNameField = "account_name";
    self.espIdField = "id";
    self.widgetName = 'esps';
    self.templateTypeMap = [ 'N/A', 'Normal HTML' , 'HTML Lite (no images)' , 'Image Only' , 'Image Map' , 'Newsletter' , 'Clickable Button' ];
    self.formErrors = [];
    self.templates = [];
    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.templateTotal = 0;
    self.sort = "-id";
    self.formSubmitted = false;
    self.queryPromise = null;

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/mailingtemplate\/edit\/(\d{1,})/ );
        self.init();
        MailingTemplateApiService.getAccount( pathMatches[ 1 ] , function ( response ) {

            self.currentAccount = {
                id : response.data.id,
                name : response.data.template_name,
                templateType : String(response.data.template_type),
                selectedEsps : response.data.esp_accounts,
                html :response.data.template_html,
                text : response.data.template_text };
            self.selectedEsps = self.currentAccount.selectedEsps;
        } );
        self.espMembershipCallback();
    };
    self.init = function () {
      MailingTemplateApiService.getEspAccounts(self.loadEspsSuccessCallback,self.loadAccountsFailureCallback);
    };

    self.espMembershipCallback = function (){
        var espIdList = [];
        angular.forEach( self.selectedEsps , function ( client , clientIndex ) {
            espIdList.push( client[ self.espIdField ] ); //lol
        } );

        if (espIdList.length > 0) {
            self.formErrors.selectedEsps = "";
        }

        self.currentAccount.selectedEsps = espIdList.join(",");
    };


    self.loadAccounts = function () {
        self.queryPromise = MailingTemplateApiService.getAccounts(self.currentPage , self.paginationCount, self.sort , self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.resetForm = function () {
        self.currentAccount = {};
    };

    /**
     * Click Handlers
     */
    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };


    self.saveNewAccount = function () {
        formValidationService.resetFieldErrors(self);
        self.formSubmitted = true;
        if (self.selectedEsps.length < 1) {
            formValidationService.setFieldError(self, 'selectedEsps' , 'At least 1 ESP is required.' );
            $mdToast.showSimple( 'Please fix errors and try again.' );
            return false;
        }
        MailingTemplateApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self.saveNewAccountFailureCallback);
    };

    self.editAccount = function () {
        formValidationService.resetFieldErrors(self);
        self.formSubmitted = true;
        self.espMembershipCallback();
        MailingTemplateApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    self.preview = function (){
        $window.open('mailingtemplate/preview/' + self.currentAccount.id);
    };

    self.previewIncomplete = function (){
        $window.open('mailingtemplate/preview/?html=' + self.currentAccount.html);
    };

    /**
     * Callbacks
     */
    self.loadAccountsSuccessCallback = function ( response ) {
        self.templates = response.data.data;
        self.pageCount = response.data.last_page;
        self.templateTotal = response.data.total;
    };

    self.loadEspsSuccessCallback = function ( response ) {
        self.espList = response.data;
    };

    self.loadAccountsFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load Templates.' );
        modalService.launchModal();
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/mailingtemplate' );
        $window.location.href = '/mailingtemplate';
    };

    self.SuccessProfileCallBackRedirect = function ( response ) {
        $location.url( '/home' );
        $window.location.href = '/home';
    };

    self.saveNewAccountFailureCallback = function ( response ) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors(self,response);
    };

    self.editAccountFailureCallback = function ( response ) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors(self,response);
    };

} ] );
