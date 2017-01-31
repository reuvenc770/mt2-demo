mt2App.controller( 'espController' , [ '$rootScope' , '$log' , '$window' , '$location' , '$timeout' , 'EspService' , 'formValidationService' , 'modalService' , 'paginationService' , function ( $rootScope , $log , $window , $location , $timeout , EspService , formValidationService , modalService , paginationService ) {
    var self = this;
    self.$location = $location;

    self.accounts = [];

    self.currentAccount = {
        "_token" : "" ,
        "id" : "" ,
        "name" : "" ,
        "nickname" : "" ,
        "open_email_id_field" : "",
        "open_email_id_field_toggle" : false ,
        "open_email_address_field" : "",
        "open_email_address_field_toggle" : false,
        "email_id_field" : "",
        "email_id_field_toggle" : false ,
        "email_address_field" : "",
        "email_address_field_toggle" : false,
        "hasAccounts":true
    };

    self.editUrl = 'esp/edit/';
    self.formErrors = [];
    self.espId = "";
    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.colList = {};
    self.paginationCount = paginationService.getDefaultPaginationCount();
    self.paginationOptions = paginationService.getDefaultPaginationOptions();
    self.currentPage = 1;
    self.campaignTriggered = false;
    self.accountTotal = 0;
    self.formSubmitted = false;
    self.fieldList = [
        { "label" : "Campaign Name" , "field" : "campaign_name" , "required" : true, "position":0 } ,
        { "label" : "Send Date" , "field" : "datetime", "position":0 } ,
        { "label" : "Name" , "field" : "name", "position":0  } ,
        { "label" : "Subject" , "field" : "subject", "position":0  } ,
        { "label" : "From" , "field" : "from", "position":0  } ,
        { "label" : "From Email" , "field" : "from_email", "position":0  } ,
        { "label" : "Number Sent" , "field" : "e_sent", "position":0  } ,
        { "label" : "Number Delivered" , "field" : "delivered", "position":0  } ,
        { "label" : "Number Bounced" , "field" : "bounced", "position":0  } ,
        { "label" : "Number Optouts" , "field" : "optouts" , "position":0 } ,
        { "label" : "Number Opens" , "field" : "e_opens", "position":0  } ,
        { "label" : "Number of Unique Opens" , "field" : "e_opens_unique", "position":0  } ,
        { "label" : "Number of Clicks" , "field" : "e_clicks", "position":0  } ,
        { "label" : "Number of Unique Clicks" , "field" : "e_clicks_unique", "position":0  } ,
        { "label" : "Conversions" , "field" : "conversions",  "position":0  } ,
        { "label" : "Cost" , "field" : "cost", "position":0  } ,
        { "label" : "Revenue" , "field" : "revenue", "position":0  }
    ];

    self.selectedFields = [];

    modalService.setPopover();

    self.loadAccounts = function () {
        EspService.getAccounts(
            self.currentPage ,
            self.paginationCount ,
            self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/esp\/edit\/(\d{1,})/ );

        EspService.getAccount( pathMatches[ 1 ] , self.loadAccountSuccesCallback )
    };

    self.loadMapping = function () {
        var pathMatches = $location.path().match( /^\/esp\/mapping\/(\d{1,})/ );
        self.espId = pathMatches[1];
        EspService.getMapping( pathMatches[1] , self.loadMappingSuccessCallback , self.loadMappingFailureCallback );
    };

    /**
     * Click Handlers
     */
     self.saveNewAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);
        //If not used is selected fill in -1 as the value so we can skip validation
        var account = jQuery.extend({}, self.currentAccount)
        account.email_id_field = self.currentAccount.email_id_field_toggle ?  '-1' : self.currentAccount.email_id_field;
        account.email_address_field = self.currentAccount.email_address_field ? '-1' : self.currentAccount.email_address_field;
        account.open_email_id_field = self.currentAccount.open_email_id_field_toggle ?  '-1' : self.currentAccount.open_email_id_field;
        account.open_email_address_field = self.currentAccount.open_email_address_field_toggle ? '-1' : self.currentAccount.open_email_address_field;
        EspService.saveNewAccount( account, self.SuccessCallBackRedirect , self. saveNewAccountFailureCallback );
     };

    self.editAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);
        //If not used is selected fill in -1 as the value so we can skip validation
        var account = jQuery.extend({}, self.currentAccount);//CLONE
        account.email_id_field = self.currentAccount.email_id_field_toggle ?  '-1' : self.currentAccount.email_id_field;
        account.email_address_field = self.currentAccount.email_address_field_toggle ? '-1' : self.currentAccount.email_address_field;
        account.open_email_id_field = self.currentAccount.open_email_id_field_toggle ?  '-1' : self.currentAccount.open_email_id_field;
        account.open_email_address_field = self.currentAccount.open_email_address_field_toggle ? '-1' : self.currentAccount.open_email_address_field;
        EspService.editAccount( account , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    self.loadAccountSuccesCallback = function ( response ) {
        var currentToken = self.currentAccount._token;

        self.currentAccount = response.data;
        self.currentAccount._token = currentToken;

        if ( response.data.field_options != null ) {
            self.currentAccount.email_id_field = response.data.field_options.email_id_field;
            self.currentAccount.email_address_field = response.data.field_options.email_address_field;
            self.currentAccount.open_email_id_field = response.data.field_options.open_email_id_field;
            self.currentAccount.open_email_address_field = response.data.field_options.open_email_address_field;
        }

        self.currentAccount.email_id_field_toggle = self.currentAccount.email_id_field.length === 0;
        self.currentAccount.email_address_field_toggle = self.currentAccount.email_address_field.length === 0;
        self.currentAccount.open_email_id_field_toggle = self.currentAccount.open_email_id_field.length === 0;
        self.currentAccount.open_email_address_field_toggle = self.currentAccount.open_email_address_field.length === 0;
    };

    self.moveField = function ( droppedField , list , index ) {
        self.campaignTriggered = false;
        list.splice( index , 1 );
        self.currentFieldConfig = [];
        if ( list === self.fieldList && typeof( self.formErrors[ droppedField.field ] ) !== 'undefined' && self.formErrors[ droppedField.field ].length > 0 ) {
            delete( self.formErrors[ droppedField.field ] );
        }
        angular.forEach( self.selectedFields , function ( value , index ) {
            if(value.field == "campaign_name"){
                self.campaignTriggered = true;
            }
            self.currentFieldConfig.push(value.field);
        } );
    };



    self.saveFieldOrder = function(){
        formValidationService.resetFieldErrors( self );
        EspService.updateMapping(
            self.espId,
            self.colList ,
            self.SuccessCallBackRedirect ,
            self.saveNewAccountFailureCallback
        );
    };

    self.fileUploaded = function ($file, espAccount){
        self.file = $file.relativePath;
        self.espAccount = espAccount;
        EspService.processFile({"filename":self.file,"espName":self.espAccount}, self.fileUploadSuccess,self.fileUploadFail);
    };

    /**
     * Callbacks
     */
    self.loadMappingSuccessCallback = function ( response ) {
        if ( typeof( response.data[0] ) !== 'undefined' ) {
            self.colList = JSON.parse(response.data[0].mappings);
        }
    };
    self.loadAccountsSuccessCallback = function ( response ) {
        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;

        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );
    };

    self.loadMappingFailureCallback = function ( response ) {
        modalService.simpleToast( 'Failed to load ESP accounts.' );
    };

    self.fileUploadSuccess = function (response){
        modalService.setModalLabel('Success');
        modalService.setModalBody("File successfully uploaded for processing.");
        modalService.launchModal();
    };

    self.fileUploadFail = function (response){
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( "Something went wrong uploading file." );
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
