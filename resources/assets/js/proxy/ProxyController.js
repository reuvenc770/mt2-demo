mt2App.controller( 'ProxyController' , [ '$log' , '$window' , '$location' , '$timeout' , 'ProxyApiService', '$rootScope','$mdToast', function ( $log , $window , $location , $timeout , ProxyApiService, $rootScope, $mdToast ) {
    var self = this;
    self.$location = $location;

    self.headers = [ '' , 'ID', 'name', "IP Address", "Provider Name"];
    self.accounts = [];
    self.currentAccount = {  id: "", "name" : "" , "ip_addresses": [], "provider_name" : "", "esp_names" :[], "isp_names": [] };
    self.ip_address = "";
    self.isp_names = [];
    self.isps =  ["AOL","GMAIL","YAHOO","HOTMAIL"];
    self.isp_name= "";
    self.ip_addresses = [];
    self.esp_name = "";
    self.esp_names = [];
    self.createUrl = 'proxy/create/';
    self.editUrl = 'proxy/edit/';

    self.formErrors = "";

    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.currentlyLoading = 0;

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/proxy\/edit\/(\d{1,})/ );

        ProxyApiService.getAccount( pathMatches[ 1 ] , function ( response ) {
            self.currentAccount = response.data;
            self.ip_addresses = self.currentAccount.ip_addresses.split(',');
            self.esp_names = self.currentAccount.esp_names.split(',');
            self.isp_names = self.currentAccount.isp_names.split(',');
        } )
    };
    self.loadProfile = function ($id) {

        ProxyApiService.getAccount($id , function ( response ) {
            self.currentAccount = response.data;
        } )
    };

    self.loadAccounts = function () {
        self.currentlyLoading = 1;
        ProxyApiService.getAccounts(self.currentPage , self.paginationCount , self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
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
        self.resetFieldErrors();
        self.currentAccount.ip_addresses = self.ip_addresses.join(',');
        self.currentAccount.esp_names = self.esp_names.join(',');
        self.currentAccount.isp_names = self.isp_names.join(',');
        self.currentAccount.status =1;
        ProxyApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self.saveNewAccountFailureCallback );
    };

    self.editAccount = function () {
        self.resetFieldErrors();

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

    self.addEsp = function () {
        if(self.esp_name.length > 0){
            self.esp_names.push(self.esp_name);
            self.esp_name = "";
        }
    };

    self.removeEsp = function (id) {
        self.esp_names.splice( id , 1 );

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
        self.currentlyLoading = 0;
    };

    self.loadAccountsFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load Users.' );

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
