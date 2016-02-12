mt2App.controller( 'ClientController' , [ '$log' , '$window' , '$location' , 'ClientApiService' , function ( $log , $window , $location , ClientApiService ) {
    var self = this;

    self.clients = [];

    self.createUrl = 'client/create/';
    self.editUrl = 'client/edit/';

    self.headers = [ '' , 'ID' , 'Name' , 'Status' , 'Type' , 'Sub-Affiliate ID' , 'Source URL' , 'Owner' , 'Owner Type' , 'Username' , 'Email' , 'Phone' , 'Address' , 'Country' ];

    self.loadClients = function () {
        ClientApiService.getClients( self.loadClientsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.loadClientsSuccessCallback = function ( response ) {
        self.clients = response.data;
    };

    self.loadAccountsFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load ESP Accounts.' );

        self.launchModal();
    };

    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };

    self.suppressionToggle = function () {

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
} ] );
