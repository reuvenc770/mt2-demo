mt2App.controller( 'MailingTemplateController' , [  '$rootScope' ,'$log' , '$window' , '$location' , '$timeout' , 'MailingTemplateApiService' , '$mdToast' , 'CustomValidationService' , function ( $rootScope, $log , $window , $location , $timeout , MailingTemplateApiService , $mdToast , CustomValidationService ) {
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

    self.templates = [];
    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.templateTotal = 0;
    self.sort = "-id";
    self.queryPromise = null;

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/mailingtemplate\/edit\/(\d{1,})/ );
        self.init();
        MailingTemplateApiService.getAccount( pathMatches[ 1 ] , function ( response ) {

            self.currentAccount = {
                id : response.data.id,
                name : response.data.template_name,
                templateType : response.data.template_type,
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

    self.change = function ( form , fieldName ) {
        CustomValidationService.onChangeResetValidity( self , form , fieldName );
    };

    self.saveNewAccount = function ( event , form ) {
        self.resetFieldErrors();

        var errorFound = false;

        angular.forEach( form.$error.required , function( field ) {
            field.$setDirty();
            field.$setTouched();

            errorFound = true;
        } );

        if (self.selectedEsps.length < 1) {
            self.setFieldError( 'selectedEsps' , 'At least 1 ESP is required.' );
            errorFound = true;
        }

        if ( errorFound ) {
            $mdToast.showSimple( 'Please fix errors and try again.' );

            return false;
        };

        MailingTemplateApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , function(response) {
            angular.forEach( response.data , function( error , fieldName ) {

                form[ fieldName ].$setDirty();
                form[ fieldName ].$setTouched();
                form[ fieldName ].$setValidity('isValid' , false);
            });

            self.saveNewAccountFailureCallback(response);
            } );
    };

    self.editAccount = function () {
        self.resetFieldErrors();
        self.espMembershipCallback();
        MailingTemplateApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    self.preview = function (){
        $window.open('mailingtemplate/preview/' + self.currentAccount.id);
    };

    self.previewIncomplete = function (){
        $window.open('mailingtemplate/preview/?html=' + self.currentAccount.html);
    };

    $rootScope.$on( 'updatePage' , function () {
        self.loadAccounts();
    } );

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
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load Templates.' );

        self.launchModal();
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
        self.loadFieldErrors(response);
    };

    self.editAccountFailureCallback = function ( response ) {
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
