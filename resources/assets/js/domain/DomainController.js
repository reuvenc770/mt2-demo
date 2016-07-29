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
    self.formErrors = [];
    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.proxies = [];
    self.info = ["", "Enter Domain Info (Domain, Main Site, Expiration Date (2016-11-22)", "Enter Domain Info (Domain, Expiration Date (2016-11-22))"];
    self.currentInfo = self.info[1];
    self.GlythMap  = { 1:"glyphicon-ok-circle", 0:"glyphicon glyphicon-ban-circle"};
    self.updatingAccounts = false;



    self.loadAccounts = function () {
        DomainService.getAccounts(
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
        self.updateDomains();
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
        DomainService.getDomains(self.currentAccount.domain_type, self.currentAccount.espAccountId, self.updateDomainsSuccessCallback, "");
    };

    self.updateEspAccounts = function () {
        self.updatingAccounts = true;
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
        self.resetFieldErrors();
        self.currentAccount.proxy = self.currentAccount.proxy.id;
        DomainService.saveNewAccount(self.currentAccount, self.SuccessCallBackRedirect, self.saveNewAccountFailureCallback);
    };

    self.editAccount = function () {
        self.resetFieldErrors();
        DomainService.editAccount(self.currentAccount, self.SuccessCallBackRedirect, self.editAccountFailureCallback);
    };

    self.inactiveAccount = function (id) {
        DomainService.inactiveAccount(id, function () {
            $mdToast.showSimple( 'Domain has Been deactivated' );
            self.updateDomains();
        }, self.saveNewAccountFailureCallback);
    };




    /**
     * Callbacks
     */
    self.updateEspAccountsSuccessCallback = function (response) {
        self.espAccounts = response.data;
        self.updatingAccounts = false;
    };

    self.updateDomainsSuccessCallback = function (response) {
        self.domains = response.data;
        self.updatingAccounts = false;
    };
    self.loadAccountsSuccessCallback = function (response) {
        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
    };

    self.loadAccountsFailureCallback = function (response) {
        self.setModalLabel('Erro r');
        self.setModalBody('Failed to load ESP Accounts.');

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
    }

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
