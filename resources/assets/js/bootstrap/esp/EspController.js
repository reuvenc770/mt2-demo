mt2App.controller( 'espController' , [ '$rootScope' , '$log' , '$window' , '$location' , '$timeout' , 'EspService' , 'formValidationService' , 'modalService' , function ( $rootScope , $log , $window , $location , $timeout , EspService , formValidationService , modalService ) {
    var self = this;
    self.$location = $location;

    self.accounts = [];

    self.currentAccount = { "_token" : "" , "id" : "" , "name" : "" , "email_id_field" : "" , "email_address_field" : "" };

    self.editUrl = 'esp/edit/';
    self.formErrors = [];
    self.espId = "";
    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentFieldConfig = [];
    self.currentPage = 1;
    self.accountTotal = 0;
    self.formSubmitted = false;
    self.fieldList = [
        { "label" : "Campaign Name" , "field" : "campaign_name" , "required" : true } ,
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

    self.loadAccounts = function () {
        EspService.getAccounts(
            self.currentPage ,
            self.paginationCount ,
            self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
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
        EspService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self. saveNewAccountFailureCallback );
     };

    self.editAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);
        EspService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    self.moveField = function ( droppedField , list , index ) {
        list.splice( index , 1 );

        self.currentFieldConfig = [];

        if ( list === self.fieldList && typeof( self.formErrors[ droppedField.field ] ) !== 'undefined' && self.formErrors[ droppedField.field ].length > 0 ) {
            delete( self.formErrors[ droppedField.field ] );
        }
        angular.forEach( self.selectedFields , function ( value , index ) {
            self.currentFieldConfig.push(value.field);
        } );
    };

    self.setFields = function (mapping) {
        angular.forEach(self.fieldList, function (currentField, feedIndex) {
            angular.forEach(mapping, function (currentMapping, index) {
                if (currentMapping == currentField.field) {
                    var removedFields = self.fieldList.splice(index, 1);
                    itemRemoved = removedFields.pop();
                    self.selectedFields.push(itemRemoved);
                    self.currentFieldConfig.push(itemRemoved.field);
                }
            });
        });
    };

    self.saveFieldOrder = function(){
        self.formSubmitted = true;
        formValidationService.resetFieldErrors( self );
        EspService.updateMapping(
            self.espId,
            self.currentFieldConfig ,
            self.SuccessCallBackRedirect ,
            self.saveNewAccountFailureCallback
        );
    };

    self.processCsv = function ($file){
        self.file = $file.relativePath;
        $('#validateModal').modal('show');
    }

    /**
     * Callbacks
     */
    self.loadMappingSuccessCallback = function ( response ) {
        loadingfields = response.data[0].mappings.split(',');
        self.setFields(loadingfields);

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
