mt2App.controller( 'ClientController' , [ '$rootScope' , '$window' , '$location' , 'ClientApiService' , function ( $rootScope , $window , $location , ClientApiService ) {
    var self = this;

    self.current = {
        address: "" ,
        address2: "" ,
        cake_sub_id: "" ,
        check_global_suppression: "Y" ,
        check_previous_oc: "0" ,
        city: "" ,
        client_has_client_group_restrictions: "0" ,
        client_id: "" ,
        client_main_name: "" ,
        client_record_ip: "" ,
        client_record_source_url: "" ,
        client_type: "" ,
        country_id: "" ,
        email_addr: "" ,
        ftp_pw: "" ,
        ftp_url: "" ,
        ftp_user: "" ,
        has_client_group_restriction: "0" ,
        list_owner: "" ,
        minimum_acceptable_record_date: "" ,
        network: "" ,
        orange_client: "Y" ,
        password: "" ,
        phone: "" ,
        rt_pw: "" ,
        state: "" ,
        status: "D" ,
        username: "" ,
        zip: ""
    };

    self.clients = [];

    self.createUrl = '/client/create';

    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;

    self.currentlyLoading = 0;
    self.generatingLinks = 0;
    self.updatingClient = 0;
    self.creatingClient = 0;

    self.clientTypes = [];
    self.typeSearchText = '';
    self.formErrors = [];
    self.listOwners = [];
    self.ownerSearchText = '';

    self.urlList = [];

    /**
     * Init Methods
     */
    self.loadAutoComplete = function () {
        self.loadClientTypes();
        self.loadListOwners();
    };

    self.loadClient = function () {
        var currentPath = $location.path();
        var matches = currentPath.match( /\/(\d{1,})/ );
        var id = matches[ 1 ]; 

        ClientApiService.getClient( id , self.loadClientSuccessCallback , self.loadClientSuccessCallback );
    };

    self.loadClients = function () {
        self.currentlyLoading = 1;

        ClientApiService.getClients( self.currentPage , self.paginationCount , self.loadClientsSuccessCallback , self.loadClientsFailureCallback );
    };


    /**
     * Button Click Handlers
     */
    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };

    self.generateLinks = function () {
        if ( self.urlList.length === 0 ) {
            self.generatingLinks = 1;

            ClientApiService.generateLinks(
                self.current.client_id ,
                self.generateLinksSuccessCallback ,
                self.generateLinksFailureCallback
            );
        } else {
            $( '#urlModal' ).modal('show');
        }
    };

    /**
     * Watchers
     */
    $rootScope.$on( 'updatePage' , function () {
        self.loadClients();
    } );


    /**
     * Form Methods
     */
    self.updateClient = function () {
        self.updatingClient = 1;

        ClientApiService.updateClient( self.getClientData() , self.SuccessCallBackRedirect , self.updateClientFailureCallback );
    };

    self.saveClient = function () {
        self.creatingClient = 1;

        ClientApiService.saveClient( self.getClientData() , self.SuccessCallBackRedirect , self.saveClientFailureCallback );
    };

    self.getClientData = function () {
        var clientData = {};

        angular.forEach( self.current , function ( field , fieldName ) {
            if ( typeof( field ) == 'object' ) {
                this[ fieldName ] = field.value;
            } else {
                this[ fieldName ] = field;
            }
        } , clientData );

        return clientData;
    };


    /**
     * Look-forwward Fields
     */
    self.getClientType = function ( searchText ) {
        return searchText ? self.clientTypes.filter( function ( obj ) { return obj.name.toLowerCase().indexOf( searchText.toLowerCase() ) === 0; } ) : self.clientTypes;
    };

    self.loadClientTypes = function () {
        ClientApiService.getTypes( self.loadClientTypesSuccessCallback , self.loadClientTypesFailureCallback );
    };

    self.getListOwners = function ( searchText ) {
        return searchText ? self.listOwners.filter( function ( obj ) { return obj.name.toLowerCase().indexOf( searchText.toLowerCase() ) === 0; } ) : self.listOwners;
    };

    self.loadListOwners = function () {
        ClientApiService.getListOwners( self.loadListOwnersSuccessCallback , self.loadListOwnersFailureCallback );
    };

    self.loadListOwnersSuccessCallback = function ( response ) {
        self.listOwners = response.data; 
    };

    self.loadListOwnersFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load client types.' );

        self.launchModal();
    };

    self.updateClient = function () {
        self.resetFieldErrors();
        ClientApiService.updateClient( self.current , self.SuccessCallBackRedirect , self.updateClientFailureCallback );
    };

    self.saveClient = function () {
        self.resetFieldErrors();

        var clientData = angular.copy( self.current );

        clientData.list_owner = self.current.list_owner.value;
        clientData.newClient = 1;

        ClientApiService.saveClient( clientData , self.SuccessCallBackRedirect , self.saveClientFailureCallback );
    };

    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
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
    }

    self.launchModal = function () {
        $( '#pageModal' ).modal('show');
    };

    self.resetModal = function () {
        self.setModalLabel( '' );
        self.setModalBody( '' );

        $( '#pageModal' ).modal('hide');
    };


    /**
     * Callbacks
     */
    self.loadClientSuccessCallback = function ( response ) {
        var currentRecord = response.data[ 0 ];

        currentRecord.country_id = parseInt( currentRecord[ 'country_id' ] );

        if ( typeof( currentRecord[ 'list_owner' ] ) !== 'undefined' ) {
            currentRecord[ 'list_owner' ] = currentRecord[ 'list_owner' ].toLowerCase();
        }

        self.current = currentRecord;
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/client' );
        $window.location.href = '/client';
    };

    self.loadClientFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load client.' );

        self.launchModal();
    };

    self.loadClientsSuccessCallback = function ( response ) {
        self.clients = response.data.data;

        self.pageCount = response.data.last_page;

        self.currentlyLoading = 0;
    };

    self.loadClientsFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load clients.' );

        self.launchModal();
    };

    self.updateClientSuccessCallback = function () {
        self.setModalLabel( 'Update Client' );
        self.setModalBody( 'Successfully updated client.' );

        self.launchModal();
    };
    
    self.updateClientFailureCallback = function (response) {
        self.loadFieldErrors(response);
    };
    
    self.saveClientFailureCallback = function (response) {
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

    self.loadClientTypesSuccessCallback = function ( response ) {
        self.clientTypes = response.data;
    };

    self.loadClientTypesFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load client types.' );

        self.launchModal();
    };

    self.loadListOwnersSuccessCallback = function ( response ) {
        self.listOwners = response.data;
    };

    self.loadListOwnersFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load client types.' );

        self.launchModal();
    };

    self.generateLinksSuccessCallback = function ( response ) {
        self.generatingLinks = 0;

        self.urlList = response.data;

        $( '#urlModal' ).modal('show');
    };

    self.generateLinksFailureCallback = function ( response ) {
        self.generatingLinks = 0;

        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to generate links.' );

        self.launchModal();
    }

    self.setFieldError = function ( field , errorMessage ) {
        self.formErrors[ field ] = errorMessage;
    };

    self.resetFieldErrors = function () {
        self.formErrors = {};
    };

} ] );
