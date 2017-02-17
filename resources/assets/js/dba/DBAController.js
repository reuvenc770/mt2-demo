mt2App.controller( 'DBAController' , [ '$log' , '$window' , '$location' , '$timeout' , 'DBAApiService', '$rootScope', '$mdToast' , 'CustomValidationService', 'formValidationService', 'modalService' , 'paginationService' , function ( $log , $window , $location , $timeout , DBAApiService, $rootScope, $mdToast , CustomValidationService, formValidationService, modalService , paginationService ) {
    var self = this;
    self.$location = $location;

    self.accounts = [];
    self.po_box = {address : "", address_2 : "", city : "", state : "", zip: "", phone : "", brands: "", esp_account_names : [] , isp_names : [] , notes: ""};
    self.brand = "";
    self.currentAccount = { id:"",  dba_name : "" , phone: "", password: "",
    dba_email : "", po_boxes : [], address: "", address_2 : "", city : "", state : "", zip : "",entity_name: ""};

    self.isp_name = "";
    self.esp_account_name = "";

    self.createUrl = 'dba/create/';
    self.editUrl = 'dba/edit/';

    self.formErrors = {"po_box": {}};
    self.search = {
    };
    self.editingPOBox = false;
    self.pageCount = 0;
    self.paginationCount = paginationService.getDefaultPaginationCount();
    self.paginationOptions = paginationService.getDefaultPaginationOptions();
    self.currentPage = 1;
    self.poBoxHolder = [];
    self.accountTotal = 0;
    self.sort = "-status";
    self.editForm = false;
    self.queryPromise = null;
    self.recordListStatus = 'index';

    modalService.setPopover();

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/dba\/edit\/(\d{1,})/ );

        DBAApiService.getAccount( pathMatches[ 1 ] , function ( response ) {
            self.currentAccount = response.data;
            self.poBoxHolder = JSON.parse(response.data.po_boxes);
        } )
    };

    self.loadAccounts = function () {
        self.queryPromise = DBAApiService.getAccounts(self.currentPage, self.paginationCount, self.sort , self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.resetForm = function () {
        self.currentAccount = {};
    };

    self.sortCurrentRecords = function() {
        if (self.recordListStatus === 'index' ) {
            self.loadAccounts();
        }

        if ( self.recordListStatus === 'search' ) {
            self.searchDBA();
        }
    };

    /**
     * Click Handlers
     */

    self.saveNewAccount = function () {
        self.editForm = true;
        formValidationService.resetFieldErrors(self);
        self.currentAccount.po_boxes = JSON.stringify(self.poBoxHolder);
        self.currentAccount.status = 1;
        DBAApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect, self.saveNewAccountFailureCallback );
    };

    self.editAccount = function () {
        self.editForm = true;
        formValidationService.resetFieldErrors(self);
        self.currentAccount.po_boxes = JSON.stringify(self.poBoxHolder);
        DBAApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    self.addEspAccount = function () {
        if(self.esp_account_name.length > 0){
            if (typeof(self.po_box.esp_account_names) === 'undefined' ) {
                self.po_box.esp_account_names = [];
            }
            self.po_box.esp_account_names.push(self.esp_account_name);
            self.esp_account_name = "";
        }
    };

    self.removeEspAccount = function (id) {
        self.po_box.esp_account_names.splice( id , 1 );

    };

    self.addIsp = function () {
        if(self.isp_name.length > 0){
            if (typeof(self.po_box.isp_names) === 'undefined' ) {
                self.po_box.isp_names = [];
            }
            self.po_box.isp_names.push(self.isp_name);
            self.isp_name = "";
        }
    };

    self.removeIsp = function (id) {
        self.po_box.isp_names.splice( id , 1 );

    };

    self.addPOBox = function () {
        var poBoxError = false;
        if(self.po_box.address == 0){
            self.formErrors.po_box.address = ["P.O. Box Address is Required"];
            poBoxError = true;
        }
        if(self.po_box.city == 0){
            self.formErrors.po_box.city = ["P.O. Box City is Required"];
            poBoxError = true;
        }
        if(self.po_box.state == 0){
            self.formErrors.po_box.state = ["P.O. Box State is Required"];
            poBoxError = true;
        }
        if(self.po_box.zip == 0){
            self.formErrors.po_box.zip = ["P.O Box Zipcode is Required"];
            poBoxError = true;
        }
        if(poBoxError) {
          return;
        }

        self.poBoxHolder.push(self.po_box);
        self.clearPOBox();
    };

    self.removePOBox = function (id) {
        self.poBoxHolder.splice( id , 1 );

    };

    self.editPOBox = function (id) {
        self.po_box = self.poBoxHolder[id];
        self.poBoxHolder.splice( id , 1 );

        self.editingPOBox = true;
    };

    self.clearPOBox = function () {
        self.po_box = {address : "", address_2 : "", city : "", state : "", zip: "" , phone:"", brands:[], brand: "", esp_account_names : [] , isp_names : [] };
    };

    self.toggle = function(recordId,direction) {
        DBAApiService.toggleRow(recordId, direction, self.toggleRowSuccess, self.toggleRowFailure);
    };

    self.formatBox = function(box){
      var boxes = JSON.parse(box);
        return boxes;
    };

    self.searchDBA = function() {
        self.recordListStatus = 'search';

        var searchObj = {
            "dba_name": self.search.dba_name || undefined,
            "registrant_name" : self.search.registrant_name || undefined,
            "dba_email" : self.search.dba_email || undefined,
            "address":    self.search.address || undefined,
            "entity_name": self.search.entity_name || undefined
        };

        self.queryPromise = DBAApiService.searchDBA(self.paginationCount, searchObj, self.sort, self.loadAccountsSuccessCallback, self.loadAccountsFailureCallback);
        self.currentlyLoading = 0;
    };

    self.resetSearch = function() {
        self.loadAccounts();
        self.search = {};
        self.recordListStatus = 'index';
    }

    self.delete = function(recordId) {
        var r = confirm("Are you sure you want to delete this");
        if (r == true) {
            DBAApiService.deleteRow(recordId, self.deleteRowSuccess, self.deleteRowFailure)
        }

    };
    /**
     * Callbacks
     */
    self.loadAccountsSuccessCallback = function ( response ) {
        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );

        self.accounts = response.data.data;
        for (var i = 0, len = response.data.data.length; i < len; i++){
            self.accounts[i].po_boxes = JSON.parse(self.accounts[i].po_boxes);
        }
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;
    };

    self.loadAccountsFailureCallback = function ( response ) {
        modalService.simpleToast( 'Failed to load accounts.' );
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/dba' );
        $window.location.href = '/dba';
    };

    self.SuccessProfileCallBackRedirect = function ( response ) {
        $location.url( '/home' );
        $window.location.href = '/home';
    };

    self.saveNewAccountFailureCallback = function ( response ) {
        self.editForm = false;
        formValidationService.loadFieldErrors(self,response);
    };

    self.editAccountFailureCallback = function ( response ) {
        self.editForm = false;
        formValidationService.loadFieldErrors(self,response);
    };

    self.toggleRowSuccess = function ( response ) {
        modalService.setModalLabel('Success');
        modalService.setModalBody("DBA status updated.");
        modalService.launchModal();
        self.loadAccounts();
    };

    self.toggleRowFailure = function ( response ) {
        modalService.setModalLabel('Error');
        modalService.setModalBody('Failed to update DBA status. Please try again.');
        modalService.launchModal();
        self.loadAccounts();
    };

    self.deleteRowFailure = function ( response ) {
        modalService.setModalLabel('Failed To Delete Row');
        modalService.setModalBodyRawHtml(response.data.delete);
        modalService.launchModal();
        self.loadAccounts();
    };

    self.deleteRowSuccess = function ( response ) {
        modalService.simpleToast("Successfully Deleted Row");
        self.loadAccounts();
    };



} ] );
