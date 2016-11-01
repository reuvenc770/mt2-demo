mt2App.controller( 'ProxyController' , [ '$log' , '$window' , '$location' , '$timeout' , 'ProxyApiService', '$rootScope','$mdToast', '$mdConstant' , 'CustomValidationService' , function ( $log , $window , $location , $timeout , ProxyApiService, $rootScope, $mdToast , $mdConstant , CustomValidationService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ '' , 'ID', 'name', "IP Address", "Provider Name"];
    self.accounts = [];
    self.currentAccount = {  id: "", "name" : "" , "ip_addresses": [], "provider_name" : "", "esp_account_names" :[], "isp_names": [] };
    self.ip_address = "";
    self.isp_names = [];
    self.isps =  ["AOL","GMAIL","YAHOO","HOTMAIL"];
    self.isp_name= "";
    self.ip_addresses = [];
    self.esp_account_name = "";
    self.esp_account_names = [];
    self.createUrl = 'proxy/create/';
    self.editUrl = 'proxy/edit/';
    self.mdChipSeparatorKeys = [$mdConstant.KEY_CODE.ENTER , $mdConstant.KEY_CODE.COMMA , 9];
    self.formErrors = "";

    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.accountTotal = 0;
    self.sort = '-status';
    self.queryPromise = null;

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/proxy\/edit\/(\d{1,})/ );

        ProxyApiService.getAccount( pathMatches[ 1 ] , function ( response ) {
            self.currentAccount = response.data;
            self.ip_addresses = self.currentAccount.ip_addresses.split(',');
            self.esp_account_names = self.currentAccount.esp_account_names.split(',');
            self.isp_names = self.currentAccount.isp_names.split(',');
        } )
    };
    self.loadProfile = function ($id) {

        ProxyApiService.getAccount($id , function ( response ) {
            self.currentAccount = response.data;
        } )
    };

    self.loadAccounts = function () {
        self.queryPromise = ProxyApiService.getAccounts(self.currentPage , self.paginationCount , self.sort , self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.resetForm = function () {
        self.currentAccount = {};
    };

    self.toggle = function(recordId,direction) {
        ProxyApiService.toggleRow(recordId, direction, self.toggleRowSuccess, self.toggleRowFailure)
    };

    /**
     * Click Handlers
     */
    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };

    self.change = function ( form , fieldName ) {
        if ( fieldName == 'ip_addresses' ) {
            delete( form['ip_addresses'].$error.required );
        }

        CustomValidationService.onChangeResetValidity( self , form , fieldName );
    };

    self.saveNewAccount = function ( event , form) {
        self.resetFieldErrors();

        var errorFound = false;

        angular.forEach( form.$error.required , function( field ) {

            field.$setDirty();
            field.$setTouched();

            errorFound = true;
        } );

        if ( self.ip_addresses.length < 1  ) {

            form['ip_addresses'].$setDirty();
            form['ip_addresses'].$setTouched();
            form['ip_addresses'].$setValidity('isValid', false);
            form['ip_addresses'].$error.required = true;

            errorFound = true;
        }

        if ( errorFound ) {
            $mdToast.showSimple( 'Please fix errors and try again.' );

            return false;
        };

        self.currentAccount.ip_addresses = self.ip_addresses.join(', ');
        self.currentAccount.esp_account_names = self.esp_account_names.join(', ');
        self.currentAccount.isp_names = self.isp_names.join(', ');
        self.currentAccount.status =1;
        ProxyApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , function( response ) {
            angular.forEach( response.data , function( error , fieldName ) {

                form[ fieldName ].$setDirty();
                form[ fieldName ].$setTouched();
                form[ fieldName ].$setValidity('isValid' , false);
            });

            self.saveNewAccountFailureCallback( response);
        } );
    };

    self.editAccount = function () {
        self.resetFieldErrors();
        self.currentAccount.ip_addresses = self.ip_addresses.join(', ');
        self.currentAccount.esp_account_names = self.esp_account_names.join(', ');
        self.currentAccount.isp_names = self.isp_names.join(',');
        ProxyApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };


    self.addIpAddress = function () {
        if(self.ip_address.length > 0){
            self.ip_addresses.push(self.ip_address);
            self.ip_address = "";
        }
    };

    self.removePOBox = function (id) {
        self.ip_addresses.splice( id , 1 );

    };

    self.editIpAddress = function (id) {
        self.ip_address = self.ip_addresses[id];
        self.ip_addresses.splice( id , 1 );
    };

    self.addEspAccount = function () {
        if(self.esp_account_name.length > 0){
            self.esp_account_names.push(self.esp_account_name);
            self.esp_account_name = "";
        }
    };

    self.removeEspAccount = function (id) {
        self.esp_account_names.splice( id , 1 );

    };
    self.removeIpAddress = function (id) {
        self.ip_addresses.splice( id , 1 );

    };

    self.addIsp = function () {
        if(self.isp_name.length > 0){
            self.isp_names.push(self.isp_name);
            self.isp_name = "";
        }
    };

    self.removeIsp = function (id) {
        self.isp_names.splice( id , 1 );

    };



    /**
     * Watchers
     */
    $rootScope.$on( 'updatePage' , function () {
        self.loadAccounts();
    } );





    /**
     * Callbacks
     */
    self.loadAccountsSuccessCallback = function ( response ) {
        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;
    };

    self.loadAccountsFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load accounts.' );

        self.launchModal();
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/proxy' );
        $window.location.href = '/proxy';
    };

    self.SuccessProfileCallBackRedirect = function ( response ) {
        $location.url( '/home' );
        $window.location.href = '/home';
    };


    self.toggleRowSuccess = function ( response ) {
        $mdToast.showSimple("Proxy Updated");
        self.loadAccounts();
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
