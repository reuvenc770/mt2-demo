mt2App.controller('domainController', ['$rootScope', '$log', '$window', '$location', '$timeout', 'DomainService', '$mdToast', '$httpParamSerializer','formValidationService', 'modalService', 'paginationService' , function ($rootScope, $log, $window, $location, $timeout, DomainService, $mdToast, $httpParamSerializer, formValidationService, modalService , paginationService ) {
    var self = this;
    self.$location = $location;

    self.accounts = [];
    self.hideFormView = false;
    //Local Vars
    var currentEspAccount = "";
    var espName = "";
    var espNameQuery = $location.search().name;
    var espAccount = $location.search().espId;
    self.formSubmitted = false;
     self.espNotChosen = true;
    var espAccountName = $location.search().espAccountName;
    //View Page
    if (typeof espAccount != 'undefined' && typeof espNameQuery != 'undefined') {
        currentEspAccount = espAccount;
        espName = espNameQuery;
        self.hideFormView = true;
        self.extraText = "For " + espNameQuery + " - " + espAccountName;
    }
    if(typeof searchDomains != 'undefined'){
        self.domains = searchDomains;
    }
    self.currentAccount = {
        "espName": espName,
        "domain_type": "1",
        "registrar": "",
        "proxy": "",
        "dba": "",
        "domains": "",
        "live_a_record": "",
        "espAccountId": currentEspAccount
    };


    self.currentDomain = {
        "id" : "",
        "domain_name": "",
        "proxy_id": "",
        "registrar_id": "",
        "main_site" : "",
        "expires_at" : "",
        "live_a_record": "",
        "esp_account_id": ""
    };

    self.createUrl = 'domain/create/';
    self.espAccounts = [];
    self.selectedProxy = [];
    self.formErrors = [];
    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.rowBeingEdited = "0";
    self.paginationCount = paginationService.getDefaultPaginationCount();
    self.paginationOptions = paginationService.getDefaultPaginationOptions();
    self.currentPage = 1;
    self.search = {"esp": espName,
        "eps_account_id" : undefined,
        "doing_business_as_id": undefined ,
        "registrar_id": undefined,
        "proxy_id": undefined};
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

        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );
    };
    self.init = function (type) {

        self.updatingAccounts = true;
        self.currentAccount.domain_type = type;
        self.updateProxies();
        if (typeof espNameQuery != 'undefined') { // we have to grab the esp's and then assign current
            self.updateEspAccounts();
            self.currentAccount.espAccountId = currentEspAccount;
        }
        if(self.currentAccount.espAccountId.length > 0) {
            self.updateDomains();
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

    self.updateSearchEspAccounts = function () {
        self.updatingAccounts = true;
        DomainService.getEspAccounts(
            self.search.esp,
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
    self.saveNewAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);
        self.currentAccount.proxy = self.selectedProxy.id;
        DomainService.saveNewAccount(self.currentAccount, self.SuccessCallBackRedirect, self.saveNewAccountFailureCallback);

    };

    self.editAccount = function () {
        self.formSubmitted = true;
        form.resetFieldErrors(self);
        DomainService.editAccount(self.currentAccount, self.SuccessCallBackRedirect, self.editAccountFailureCallback);
    };

    self.toggle = function(recordId,direction) {
        DomainService.toggleRow(recordId, direction, self.toggleRowSuccess, self.toggleRowFailure)
    };

    self.editDomain = function() {
        self.formSubmitted = true;
        var domain = self.currentDomain;
        DomainService.editAccount(domain,self.editRowSuccess, self.editRowFailure)
    };

    self.searchDomains = function (){
       var params = $httpParamSerializer(self.search);
        $location.url('/domain/search?'+ params);
        $window.location.href = '/domain/search?'+ params;
    };



    /**
     * Callbacks
     */
    self.updateEspAccountsSuccessCallback = function (response) {
        self.espAccounts = response.data;
        self.updatingAccounts = false;
        self.formSubmitted = false;
        self.espNotChosen = false;
    };

    self.updateDomainsSuccessCallback = function (response) {
        self.domains = response.data;
        self.updatingAccounts = false;
        self.formSubmitted = false;
    };
    self.loadAccountsSuccessCallback = function (response) {
        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );

        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;
        self.updatingAccounts = false;
    };

    self.toggleRowSuccess = function ( response ) {
        $mdToast.showSimple("Domain Updated");
        self.updateDomains();
    };

    self.loadAccountSuccessCallback = function (response){
        self.currentDomain = response.data;
        self.currentDomain.registrar_id = String(response.data.registrar_id);
        self.currentDomain.live_a_record = String(response.data.live_a_record);
    };

    self.editRowSuccess = function (){
        $mdToast.showSimple("Domain Updated");
        self.rowBeingEdited = 0;
        self.currendDomain = {};
        self.formSubmitted = false;
        self.updateDomains();
    };
    self.loadAccountFailureCallback = function (response){
        $mdToast.showSimple("Domain did not load");
        self.rowBeingEdited = 0;
    };


    self.loadAccountsFailureCallback = function (response) {
        modalService.setModalLabel('Error');
        modalService.setModalBody('Failed to load Domains.');

        modalService.launchModal();
    };

    self.SuccessCallBackRedirect = function (response) {
        $location.url('/domain');
        $window.location.href = '/domain';
    };

    self.saveNewAccountFailureCallback = function (response) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors(self,response);
    };

    self.editAccountFailureCallback = function (response) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors(self,response);
    };

}]);
