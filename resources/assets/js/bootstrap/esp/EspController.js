mt2App.controller( 'espController' , [ '$rootScope' , '$log' , '$window' , '$location' , '$timeout' , 'EspService' , 'formValidationService' , 'modalService' , 'paginationService' , function ( $rootScope , $log , $window , $location , $timeout , EspService , formValidationService , modalService , paginationService ) {
    var self = this;
    self.$location = $location;

    self.accounts = [];

    self.currentAccount = { "_token" : "" , "id" : "" , "name" : "" , "email_id_field" : "","email_id_field_toggle" : false , "email_address_field" : "", "email_address_field_toggle" : false };

    self.editUrl = 'esp/edit/';
    self.formErrors = [];
    self.espId = "";
    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = paginationService.getDefaultPaginationCount();
    self.paginationOptions = paginationService.getDefaultPaginationOptions();
    self.currentPage = 1;
    self.campaignTriggered = false;
    self.accountTotal = 0;
    self.formSubmitted = false;
    self.fieldList = [
        { "label" : "Campaign Name" , "field" : "campaign_name" , "required" : true } ,
        { "label" : "Send Date" , "field" : "datetime" } ,
        { "label" : "Name" , "field" : "name"  } ,
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

        EspService.saveNewAccount( account, self.SuccessCallBackRedirect , self. saveNewAccountFailureCallback );
     };

    self.editAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);
        //If not used is selected fill in -1 as the value so we can skip validation
        var account = jQuery.extend({}, self.currentAccount)
        account.email_id_field = self.currentAccount.email_id_field_toggle ?  '-1' : self.currentAccount.email_id_field;
        account.email_address_field = self.currentAccount.email_address_field_toggle ? '-1' : self.currentAccount.email_address_field;

        EspService.editAccount( account , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    self.loadAccountSuccesCallback = function ( response ) {
        var currentToken = self.currentAccount._token;

        self.currentAccount = response.data;
        self.currentAccount._token = currentToken;

        if ( response.data.field_options != null ) {
            self.currentAccount.email_id_field = response.data.field_options.email_id_field;
            self.currentAccount.email_address_field = response.data.field_options.email_address_field;
        }

        self.currentAccount.email_id_field_toggle = self.currentAccount.email_id_field.length === 0;
        self.currentAccount.email_address_field_toggle = self.currentAccount.email_address_field.length === 0;
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

    self.setFields = function (mapping) {
        angular.forEach(mapping, function (currentField, feedIndex) {
            angular.forEach(self.fieldList, function (currentMapping, index) {
                if (currentMapping.field == currentField) {
                    var removedFields = self.fieldList.splice(index, 1);
                    itemRemoved = removedFields.pop();
                    self.selectedFields.push(itemRemoved);
                    self.currentFieldConfig.push(itemRemoved.field);
                    if(itemRemoved.field == "campaign_name"){
                        self.campaignTriggered = true;
                    }
                }
            });
        });
    };

    self.saveFieldOrder = function(){
        formValidationService.resetFieldErrors( self );
        EspService.updateMapping(
            self.espId,
            self.currentFieldConfig ,
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
            loadingfields = response.data[0].mappings.split(',');
            self.setFields(loadingfields);
        }
    };
    self.loadAccountsSuccessCallback = function ( response ) {
        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;

        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );
    };

    self.loadMappingFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load ESP Accounts.' );
        modalService.launchModal();
    };

    self.fileUploadSuccess = function (response){
        modalService.simpleToast("File was successfully uploaded for processing");
    };

    self.fileUploadFail = function (response){
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( "Something went wrong uploading file" );
        modalService.launchModal();
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/esp' );
        $window.location.href = '/esp';
    };

    self.saveNewAccountFailureCallback = function ( response ) {
        self.formSubmitted = false;
        self.currentAccount.email_id_field = "";
        self.currentAccount.email_address_field = "";
        formValidationService.loadFieldErrors( self , response );
    };

    self.editAccountFailureCallback = function ( response ) {
        self.formSubmitted = false;
        self.currentAccount.email_id_field = "";
        self.currentAccount.email_address_field = "";
        formValidationService.loadFieldErrors( self , response );
    };


} ] );
