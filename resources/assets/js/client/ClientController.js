mt2App.controller( 'ClientController' , [ '$log' , '$window' , '$location' , 'ClientApiService' , function ( $log , $window , $location , ClientApiService ) {
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
        status: "" ,
        unique_profile_id: "" ,
        username: "" ,
        zip: ""
    };

    self.clients = [];

    self.createUrl = '/client/create';

    self.loadClient = function () {
        var currentPath = $location.path();
        var matches = currentPath.match( /\/(\d{1,})/ );
        var id = matches[ 1 ]; 

        ClientApiService.getClient( id , self.loadClientSuccessCallback , self.loadClientSuccessCallback );
    };

    self.loadClients = function () {
        ClientApiService.getClients( self.loadClientsSuccessCallback , self.loadClientsFailureCallback );
    };

    self.updateClient = function () {
        ClientApiService.updateClient( self.current , self.updateClientSuccessCallback , self.updateClientFailureCallback );
    };

    self.saveClient = function () {
        ClientApiService.saveClient( self.current , self.saveClientSuccessCallback , self.saveClientFailureCallback );
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
        currentRecord[ 'list_owner' ] = currentRecord[ 'list_owner' ].toLowerCase();

        self.current = currentRecord;
    };

    self.loadClientFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load client.' );

        self.launchModal();
    };

    self.loadClientsSuccessCallback = function ( response ) {
        self.clients = response.data;
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
    
    self.updateClientFailureCallback = function () {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to update client.' );

        self.launchModal();
    };

    self.saveClientSuccessCallback = function () {
        self.setModalLabel( 'Add Client' );
        self.setModalBody( 'Successfully added new client.' );

        self.launchModal();
    };
    
    self.saveClientFailureCallback = function () {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to save new client.' );

        self.launchModal();
    };
} ] );
