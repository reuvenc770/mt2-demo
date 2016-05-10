mt2App.controller( 'ClientAttributionController' , [ 'ClientApiService' , '$rootScope' , '$location' , '$window' , '$log' , function ( ClientApiService , $rootScope , $location , $window , $log ) {
    var self = this;

    self.clients = [];

    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.currentlyLoading = 0;

    self.redirectUrl = '/client/attribution';

    self.loadClients = function () {
        self.currentlyLoading = 1;

        ClientApiService.getClientAttributionList( self.currentPage , self.paginationCount , self.loadClientsSuccessCallback , self.loadClientsFailureCallback );
    };

    self.loadClientsSuccessCallback = function ( response ) {
        self.clients = response.data.data;
        self.pageCount = response.data.last_page;
        self.currentlyLoading = 0;
    };

    self.loadClientsFailureCallback = function ( response ) {
        self.setModalLabel( 'Loading Clients...' );
        self.setModalBody( 'Failed to load clients. Please try again later.' );
        self.launchModal();
    };

    self.setAttribution = function ( id , level ) {
        if ( level == '' || level == null ) {
            self.setModalLabel( 'Setting Attribution Level...' );
            self.setModalBody( 'Attribution Level is required.' );
            self.launchModal();

            return false;
        } else if ( level == 255 ) {
            self.setModalLabel( 'Setting Attribution Level...' );
            self.setModalBody( 'Attribution Level can not be 255. Please choose another level.' );
            self.launchModal();

            return false;
        } else if ( !angular.isNumber( level ) ) {
            self.setModalLabel( 'Setting Attribution Level...' );
            self.setModalBody( 'Attribution Level can not contain letters. Please choose another level.' );
            self.launchModal();

            return false;
        }

        ClientApiService.setAttribution( id , level , self.setAttributionSuccessCallback , self.setAttributionFailureNotify );
    };

    self.setAttributionSuccessCallback = function ( response ) {
        if ( typeof( response.data.status ) !== 'undefined' && response.data.status === false ) {
            self.setAttributionFailureNotify();
        } else if ( typeof( response.data.status ) !== 'undefined' ) {
            self.setModalLabel( 'Setting Attribution Level' );
            self.setModalBody( 'Successfully Changed Attribution Level.' );
            self.launchModal();
        }
    };
        
    self.setAttributionFailureNotify = function () {
        self.setModalLabel( 'Setting Attribution Level' );
        self.setModalBody( 'Failed to change Attribution Level.' );
        self.launchModal();
    };

    self.deleteAttribution = function ( id ) {
        ClientApiService.deleteAttribution( id , self.deleteAttributionSuccessCallback , self.deleteAttributionFailureNotify );
    };

    self.deleteAttributionSuccessCallback = function ( response ) {
        if ( typeof( response.data.status ) !== 'undefined' && response.data.status === false ) {
            self.deleteAttributionFailureNotify();
        } else {
            self.successCallBackRedirect();
        }
    };
        
    self.deleteAttributionFailureNotify = function () {
        self.setModalLabel( 'Removing Attribution Level' );
        self.setModalBody( 'Failed to delete Attribution Level..' );
        self.launchModal();
    }

    self.successCallBackRedirect = function () {
        $location.url( self.redirectUrl );
        $window.location.href = self.redirectUrl;
    };

    $rootScope.$on( 'updatePage' , function () {
        self.loadClients();
    } );

    /*
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

mt2App.directive( 'clientattributionTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "=" ,
            "setclient" : "&" ,
            "deleteclient" : "&"
        } ,
        "templateUrl" : "js/templates/clientattribution-table.html"
    };
} );
