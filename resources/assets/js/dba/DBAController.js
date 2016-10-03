mt2App.controller( 'DBAController' , [ '$log' , '$window' , '$location' , '$timeout' , 'DBAApiService', '$rootScope', '$mdToast' , function ( $log , $window , $location , $timeout , DBAApiService, $rootScope, $mdToast ) {
    var self = this;
    self.$location = $location;

    self.accounts = [];
    self.po_box = {sub : "",address : "", address_2 : "", city : "", state : "", zip: "", phone : "", brands: [], notes: ""};
    self.brand = "";
    self.currentAccount = { id:"",  dba_name : "" , phone: "",
    dba_email : "", po_boxes : [], address: "", address_2 : "", city : "", state : "", zip : "",entity_name: ""};

    self.createUrl = 'dba/create/';
    self.editUrl = 'dba/edit/';

    self.formErrors = "";

    self.editingPOBox = false;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.accountTotal = 0;
    self.sort = "-status";
    self.queryPromise = null;

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/dba\/edit\/(\d{1,})/ );

        DBAApiService.getAccount( pathMatches[ 1 ] , function ( response ) {
            self.currentAccount = response.data;
            self.currentAccount.po_boxes = JSON.parse(response.data.po_boxes);
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
    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };

    self.onFormFieldChange = function ( event , form , fieldName ) {
        form[ fieldName ].$setValidity('isValid', true);

        self.formErrors[ fieldName ] = [];
    };

    self.saveNewAccount = function ( event , form ) {
        self.resetFieldErrors();

        var errorFound = false;

        angular.forEach( form.$error.required , function( field ) {
            if (field.$name.match(/^po_box+/i) == null ) {
                field.$setDirty();
                field.$setTouched();

                if ( field.$name == 'state' ) {
                    form.state.$error.required = true;
                }

                errorFound = true;
            }
        } );

        if ( errorFound ) {
            $mdToast.showSimple( 'Please fix errors and try again.' );

            return false;
        };

        self.currentAccount.po_boxes = JSON.stringify(self.currentAccount.po_boxes);
        self.currentAccount.status = 1;
        DBAApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , function( response ) {
            angular.forEach( response.data , function( error , fieldName ) {

                form[ fieldName ].$setDirty();
                form[ fieldName ].$setTouched();
                form[ fieldName ].$setValidity('isValid' , false);
            });

            self.saveNewAccountFailureCallback( response );
        } );
    };

    self.editAccount = function () {
        self.resetFieldErrors();
        self.currentAccount.po_boxes = JSON.stringify(self.currentAccount.po_boxes);
        DBAApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    self.addBrand = function () {
            self.po_box.brands.push(self.brand);
            self.brand = "";
    };


    self.removeBrand = function (id) {
        self.po_box.brands.splice( id , 1 );

    };

    self.editBrand = function (id) {
        self.brand = self.po_box.brands[id];
        self.po_box.brands.splice( id , 1 );
    };

    self.addPOBox = function ( event , form ) {
        if(self.po_box.address.length >= 1 || self.po_box.state.length >= 1) {
            self.currentAccount.po_boxes.push(self.po_box);
            self.clearPOBox();
        }
        self.editingPOBox = false;
    };

    self.removePOBox = function (id) {
        self.currentAccount.po_boxes.splice( id , 1 );

    };

    self.editPOBox = function (id) {
        self.po_box = self.currentAccount.po_boxes[id];
        self.currentAccount.po_boxes.splice( id , 1 );
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
        for (var i = 0, len = response.data.data.length; i < len; i++){
            self.accounts[i].po_boxes = JSON.parse(self.accounts[i].po_boxes);
        }
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;
    };

    self.loadAccountsFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load Users.' );

        self.launchModal();
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
        self.currentAccount.po_boxes = JSON.parse(self.currentAccount.po_boxes);
        self.loadFieldErrors(response);
    };

    self.editAccountFailureCallback = function ( response ) {
        self.loadFieldErrors(response);
    };

    self.toggleRowSuccess = function ( response ) {
        $mdToast.showSimple("DBA Updated");
        self.loadAccounts();
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
