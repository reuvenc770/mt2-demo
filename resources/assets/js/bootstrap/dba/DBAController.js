mt2App.controller( 'DBAController' , [ '$log' , '$window' , '$location' , '$timeout' , 'DBAApiService', '$rootScope', '$mdToast' , 'CustomValidationService', 'formValidationService', 'modalService' , function ( $log , $window , $location , $timeout , DBAApiService, $rootScope, $mdToast , CustomValidationService, formValidationService, modalService ) {
    var self = this;
    self.$location = $location;

    self.accounts = [];
    self.po_box = {sub : "",address : "", address_2 : "", city : "", state : "", zip: "", phone : "", brands: "", notes: ""};
    self.brand = "";
    self.currentAccount = { id:"",  dba_name : "" , phone: "", password: "",
    dba_email : "", po_boxes : [], address: "", address_2 : "", city : "", state : "", zip : "",entity_name: ""};

    self.createUrl = 'dba/create/';
    self.editUrl = 'dba/edit/';

    self.formErrors = {"po_box": {}};

    self.editingPOBox = false;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.poBoxHolder = [];
    self.accountTotal = 0;
    self.sort = "-status";
    self.editForm = false;
    self.queryPromise = null;

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


    self.addPOBox = function () {
        var poBoxError = false;
        if(self.po_box.sub == 0){
            self.formErrors.po_box.sub = ["Sub # is required"];
            poBoxError = true;
        }
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
        self.po_box = {address : "", address_2 : "", city : "", state : "", zip: "" , phone:"", brands:[], brand: ""};
    };

    self.toggle = function(recordId,direction) {
        DBAApiService.toggleRow(recordId, direction, self.toggleRowSuccess, self.toggleRowFailure);
    };

    self.formatBox = function(box){
      var boxes = JSON.parse(box);
        return boxes;
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
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load Users.' );
        modalService.launchModal();
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
        $mdToast.showSimple("DBA Updated");
        self.loadAccounts();
    };


} ] );
