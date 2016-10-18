mt2App.controller( 'ProxyController' , [ '$log' , '$window' , '$location' , '$timeout' , 'ProxyApiService', '$rootScope','$mdToast', '$mdConstant' , 'formValidationService', 'modalService' , function ( $log , $window , $location , $timeout , ProxyApiService, $rootScope, $mdToast , $mdConstant , formValidationService, modalService ) {
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
    self.formSubmitted = false;
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


    self.saveNewAccount = function () {
        formValidationService.resetFieldErrors(self);

        if ( self.ip_addresses.length < 1  ) {
            formValidationService.setFieldError(self, 'ip_addresses' , 'At least 1 IP Address is required.' );
            $mdToast.showSimple( 'Please fix errors and try again.' );
        }

        self.currentAccount.ip_addresses = self.ip_addresses.join(', ');
        self.currentAccount.esp_account_names = self.esp_account_names.join(', ');
        self.currentAccount.isp_names = self.isp_names.join(', ');
        self.currentAccount.status =1;
        ProxyApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self.saveNewAccountFailureCallback);
    };

    self.editAccount = function () {
        formValidationService.resetFieldErrors(self);
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
     * Callbacks
     */
    self.loadAccountsSuccessCallback = function ( response ) {
        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;
    };

    self.loadAccountsFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load accounts.' );
        modalService.launchModal();
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
        formValidationService.loadFieldErrors(self,response);
    };

    self.editAccountFailureCallback = function ( response ) {
        formValidationService.loadFieldErrors(self,response);
    };

} ] );