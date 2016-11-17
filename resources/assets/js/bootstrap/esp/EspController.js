mt2App.controller( 'espController' , [ '$rootScope' , '$log' , '$window' , '$location' , '$timeout' , 'EspService' , 'formValidationService' , 'modalService' , function ( $rootScope , $log , $window , $location , $timeout , EspService , formValidationService , modalService ) {
    var self = this;
    self.$location = $location;

    self.accounts = [];

    self.currentAccount = { "_token" : "" , "id" : "" , "name" : "" , "email_id_field" : "" , "email_address_field" : "" };

    self.editUrl = 'esp/edit/';

    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.accountTotal = 0;
    self.formSubmitted = false;
    self.fieldList = [
        { "label" : "Campaign Name" , "field" : "campaign_name" , "required" : true } ,
        { "label" : "Deploy ID" , "field" : "deploy_id" , "required" : true } ,
        { "label" : "Send Date" , "field" : "datetime" , "required" : true } ,
        { "label" : "Name" , "field" : "name" , "required" : true } ,
        { "label" : "Subject" , "field" : "subject" } ,
        { "label" : "From" , "field" : "from" } ,
        { "label" : "From Email" , "field" : "from_email" } ,
        { "label" : "Number Sent" , "field" : "e_sent" } ,
        { "label" : "Number Delivered" , "field" : "delivered" } ,
        { "label" : "Number Bounced" , "field" : "bounced" } ,
        { "label" : "Number Optouts" , "field" : "optouts" } ,
        { "label" : "Number Opens" , "field" : "e_opens" } ,
        { "label" : "Number of Unique Opens" , "field" : "e_opens_unique" } ,
        { "label" : "Number of Clicks" , "field" : "e_clicks" } ,
        { "label" : "Number of Unique Clicks" , "field" : "e_clicks_unique" } ,
        { "label" : "Conversions" , "field" : "conversions" } ,
        { "label" : "Cost" , "field" : "cost" } ,
        { "label" : "Revenue" , "field" : "revenue" }
    ];

    self.selectedFields = [];

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/esp\/edit\/(\d{1,})/ );

        EspService.getAccount( pathMatches[ 1 ] , self.loadAccountSuccesCallback )
    };

    self.loadAccounts = function () {
        EspService.getAccounts(
            self.currentPage ,
            self.paginationCount ,
            self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
    };

    /**
     * Click Handlers
     */
     self.saveNewAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);

        EspService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self. saveNewAccountFailureCallback );
     };

    self.editAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);
        EspService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    /**
     * Callbacks
     */
    self.loadAccountSuccesCallback = function ( response ) {
        var currentToken = self.currentAccount._token;

        self.currentAccount = response.data;
        self.currentAccount._token = currentToken;

        if ( response.data.field_options != null ) {
            self.currentAccount.email_id_field = response.data.field_options.email_id_field;
            self.currentAccount.email_address_field = response.data.field_options.email_address_field;
        }

    }
    self.loadAccountsSuccessCallback = function ( response ) {
        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;

        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );
    };

    self.loadAccountsFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load ESP Accounts.' );
        modalService.launchModal();
    };


    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/esp' );
        $window.location.href = '/esp';
    };

    self.saveNewAccountFailureCallback = function ( response ) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors( self , response );
    };

    self.editAccountFailureCallback = function ( response ) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors( self , response );
    };


} ] );
