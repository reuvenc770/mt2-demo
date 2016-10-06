mt2App.controller('domainController', ['$rootScope', '$log', '$window', '$location', '$timeout', 'DomainService', '$mdToast', function ($rootScope, $log, $window, $location, $timeout, DomainService, $mdToast) {
    var self = this;
    self.$location = $location;

    self.accounts = [];
    self.hideFormView = false;
    //Local Vars
    var currentEspAccount = "";
    var espName = "";
    var espNameQuery = $location.search().name;
    var espAccount = $location.search().espId;
     self.espNotChosen = true;
    var espAccountName = $location.search().espAccountName;
    //View Page
    if (typeof espAccount != 'undefined' && typeof espNameQuery != 'undefined') {
        currentEspAccount = espAccount;
        espName = espNameQuery;
        self.hideFormView = true;
        self.extraText = "For " + espNameQuery + " - " + espAccountName;
    }
    self.currentAccount = {
        "espName": espName,
        "domain_type": "1",
        "registrar": "",
        "proxy": "",
        "dba": "",
        "domains": "",
        "espAccountId": currentEspAccount
    };


    self.currentDomain = {
        "id" : "",
        "domain_name": "",
        "proxy_id": "",
        "registrar_id": "",
        "main_site" : "",
        "expires_at" : ""
        //"esp_account_id": ""

    };

    self.createUrl = 'domain/create/';
    self.espAccounts = [];
    self.selectedProxy = [];
    self.formErrors = [];
    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.rowBeingEdited = "0";
    self.paginationCount = '10';
    self.currentPage = 1;
    self.proxies = [];
    self.info = ["", "Enter Domain Info (Domain, Main Site, Expiration Date (2016-11-22)", "Enter Domain Info (Domain, Expiration Date (2016-11-22))"];
    self.currentInfo = self.info[1];
    self.GlythMap  = { 1:"glyphicon-ok-circle", 0:"glyphicon glyphicon-ban-circle"};
    self.updatingAccounts = true;
    self.type = 1;
    self.accountTotal = 0;
    self.queryPromise = null;



    self.loadAccounts = function () {
        self.queryPromise = DomainService.getAccounts(
            self.currentPage,
            self.paginationCount,
            self.loadAccountsSuccessCallback, self.loadAccountsFailureCallback);
    };

    self.loadAccount = function(id){
        DomainService.getAccount(id,self.loadAccountSuccessCallback, self.loadAccountsFailureCallback);
    };

    self.updateProxies = function () {
        DomainService.getProxies(self.currentAccount.domain_type, function (response) {
            self.proxies = response.data;
        });
        self.updatingAccounts = false;
    };

    self.updateType = function (type) {

        self.updatingAccounts = true;
        self.currentAccount.domain_type = type;
        self.currentInfo = self.info[type];
        self.type = type;
        if(self.currentAccount.espAccountId.length > 0) {
            self.updateDomains();
        }
        self.rowBeingEdited = 0;
        self.updateProxies();
    };
    self.init = function (type) {
        self.updatingAccounts = true;
        self.currentAccount.domain_type = type;
        self.updateProxies();
        if (typeof espNameQuery != 'undefined') { // we have to grab the esp's and then assign current
            self.updateEspAccounts();
            self.currentAccount.espAccountId = currentEspAccount;
        }
    };

    self.updateDomains = function () {
        self.domains = [];
        DomainService.getDomains(self.currentAccount.domain_type, self.currentAccount.espAccountId, self.updateDomainsSuccessCallback, "");
    };

    self.updateEspAccounts = function () {
        self.updatingAccounts = true;
        self.espNotChosen = true;
        DomainService.getEspAccounts(
            self.currentAccount.espName,
            self.updateEspAccountsSuccessCallback, self.loadAccountsFailureCallback);
    };

    self.beingEdited = function (domId){
        return self.rowBeingEdited == domId;
    };

    self.editRow = function (domId) {
        self.rowBeingEdited = domId;
        self.currendDomain = {};
        self.loadAccount(domId);
    };




    /**
     * Click Handlers
     */
    self.viewAdd = function () {
        $location.url(self.createUrl);
        $window.location.href = self.createUrl;
    };

    self.saveNewAccount = function ( event, form ) {
        self.resetFieldErrors();

        var errorFound = false;

        angular.forEach( form.$error.required , function( field ) {

            field.$setDirty();
            field.$setTouched();

            errorFound = true;
        } );

        if ( errorFound ) {
            $mdToast.showSimple( 'Please fix errors and try again.' );

            return false;
        };

        self.currentAccount.proxy = self.selectedProxy.id;
        DomainService.saveNewAccount(self.currentAccount, self.SuccessCallBackRedirect, function(response){
            angular.forEach( response.data , function( error , fieldName ) {

                form[ fieldName ].$setDirty();
                form[ fieldName ].$setTouched();
                form[ fieldName ].$setValidity('isValid' , false);

            });

            self.saveNewAccountFailureCallback(response);
        });

    };

    self.editAccount = function () {
        self.resetFieldErrors();
        DomainService.editAccount(self.currentAccount, self.SuccessCallBackRedirect, self.editAccountFailureCallback);
    };

    self.toggle = function(recordId,direction) {
        DomainService.toggleRow(recordId, direction, self.toggleRowSuccess, self.toggleRowFailure)
    };

    self.editDomain = function() {
        var domain = self.currentDomain;
        DomainService.editAccount(domain,self.editRowSuccess, self.editRowFailure)
    };



    /**
     * Callbacks
     */
    self.updateEspAccountsSuccessCallback = function (response) {
        self.espAccounts = response.data;
        self.updatingAccounts = false;
        self.espNotChosen = false;
    };

    self.updateDomainsSuccessCallback = function (response) {
        self.domains = response.data;
        self.updatingAccounts = false;
    };
    self.loadAccountsSuccessCallback = function (response) {
        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;
    };

    self.toggleRowSuccess = function ( response ) {
        $mdToast.showSimple("Domain Updated");
        self.updateDomains();
    };

    self.loadAccountSuccessCallback = function (response){
        self.currentDomain = response.data;
    };

    self.editRowSuccess = function (){
        $mdToast.showSimple("Domain Updated");
        self.rowBeingEdited = 0;
        self.currendDomain = {};
        self.updateDomains();
    };
    self.loadAccountFailureCallback = function (response){
        $mdToast.showSimple("Domain did not load");
        self.rowBeingEdited = 0;
    };


    self.loadAccountsFailureCallback = function (response) {
        self.setModalLabel('Error');
        self.setModalBody('Failed to load Domains.');

        self.launchModal();
    };

    self.SuccessCallBackRedirect = function (response) {
        $location.url('/domain');
        $window.location.href = '/domain';
    };

    self.saveNewAccountFailureCallback = function (response) {
        self.loadFieldErrors(response);
    };

    self.editAccountFailureCallback = function (response) {
        self.loadFieldErrors(response);
    };

    /**
     * Page Modal
     */

    self.setModalLabel = function (labelText) {
        var modalLabel = angular.element(document.querySelector('#pageModalLabel'));

        modalLabel.text(labelText);
    };

    self.setModalBody = function (bodyText) {
        var modalBody = angular.element(document.querySelector('#pageModalBody'));

        modalBody.text(bodyText);
    };

    self.launchModal = function () {
        $('#pageModal').modal('show');
    };

    self.resetModal = function () {
        self.setModalLabel('');
        self.setModalBody('');

        $('#pageModal').modal('hide');
    };

    /**
     * Errors
     */
    self.loadFieldErrors = function (response) {
        angular.forEach(response.data, function (value, key) {
            self.setFieldError(key, value);
        });
    };

    self.setFieldError = function (field, errorMessage) {
        self.formErrors[field] = errorMessage;
    };

    self.resetFieldErrors = function () {
        self.formErrors = {};
    };
}]);
