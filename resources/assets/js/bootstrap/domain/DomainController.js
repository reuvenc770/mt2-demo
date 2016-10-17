mt2App.controller('domainController', ['$rootScope', '$log', '$window', '$location', '$timeout', 'DomainService', '$mdToast', 'formValidationService', 'modalService', function ($rootScope, $log, $window, $location, $timeout, DomainService, $mdToast, formValidationService, modalService) {
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

    self.createUrl = 'domain/create/';
    self.espAccounts = [];
    self.selectedProxy = [];
    self.formErrors = [];
    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.proxies = [];
    self.formValidationService = [];
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


    /**
     * Click Handlers
     */
    self.viewAdd = function () {
        $location.url(self.createUrl);
        $window.location.href = self.createUrl;
    };

    self.saveNewAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);
        self.currentAccount.proxy = self.selectedProxy.id;
        DomainService.saveNewAccount(self.currentAccount, self.SuccessCallBackRedirect, self.saveNewAccountFailureCallback);
    };

    self.editAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);
        DomainService.editAccount(self.currentAccount, self.SuccessCallBackRedirect, self.editAccountFailureCallback);
    };

    self.toggle = function(recordId,direction) {
        DomainService.toggleRow(recordId, direction, self.toggleRowSuccess, self.toggleRowFailure)
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
        self.formSubmitted = false;
        $mdToast.showSimple("Domain Updated");
        self.updateDomains();
    };

    self.loadAccountsFailureCallback = function (response) {
        self.formSubmitted = false;
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
        formValidationService.loadFieldErrors(response);
    };

    self.editAccountFailureCallback = function (response) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors(response);
    };
}]);
