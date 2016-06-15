mt2App.controller( 'ClientAttributionController' , [ 'ClientApiService' , '$rootScope' , '$location' , '$window' , '$log' , '$mdDialog' , '$mdToast' , function ( ClientApiService , $rootScope , $location , $window , $log , $mdDialog , $mdToast ) {
    var self = this;

    self.clients = [];

    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.currentlyLoading = 0;

    self.prevAttributionLevel = 0;

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

    self.setAttribution = function ( client , ev ) {
        if ( client.level == '' || client.level == null ) {
            self.setModalLabel( 'Setting Attribution Level...' );
            self.setModalBody( 'Attribution Level is required.' );
            self.launchModal();

            return false;
        } else if ( client.level == 255 ) {
            self.setModalLabel( 'Setting Attribution Level...' );
            self.setModalBody( 'Attribution Level can not be 255. Please choose another level.' );
            self.launchModal();

            return false;
        } else if ( !angular.isNumber( client.level ) ) {
            self.setModalLabel( 'Setting Attribution Level...' );
            self.setModalBody( 'Attribution Level can not contain letters. Please choose another level.' );
            self.launchModal();

            return false;
        }

        var confirm = $mdDialog.confirm()
            .title( 'Adjusting Attribution Level' )
            .textContent( 'Are you sure you want to adjust ' + client.name + '\'s attribution level from ' + self.prevAttributionLevel + ' to ' + client.level + '?' )
            .ariaLabel( 'Adjusting Attribution Level' )
            .targetEvent( ev )
            .ok( 'Yes' )
            .cancel( 'No' );

        $mdDialog.show( confirm ).then( function() {
            ClientApiService.setAttribution( client.id , client.level , self.setAttributionSuccessCallback , self.setAttributionFailureNotify );
        } , function () {
            client.level = self.prevAttributionLevel;

            $mdToast.show(
                $mdToast.simple()
                    .textContent( 'Canceled Adjustment' )
                    .hideDelay( 1500 )
            );
        } );

    };

    self.savePreviousLevel = function ( client ) {
        self.prevAttributionLevel = client.level;
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

    self.deleteAttribution = function ( id , ev ) {
        var confirm = $mdDialog.confirm()
            .title( 'Delete Attribution Level' )
            .textContent( 'Are you sure you want to delete this attribution level?' )
            .ariaLabel( 'Delete Attribution Level' )
            .targetEvent( ev )
            .ok( 'Yes' )
            .cancel( 'No' );

        $mdDialog.show( confirm ).then( function() {
            ClientApiService.deleteAttribution( id , self.deleteAttributionSuccessCallback , self.deleteAttributionFailureNotify );
        } , function () {
            $mdToast.show(
                $mdToast.simple()
                    .textContent( 'Cancel Deletion' )
                    .hideDelay( 3000 )
            );
        } );
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
            "deleteclient" : "&" ,
            "savepreviouslevel" : '&'
        } ,
        "templateUrl" : "js/templates/clientattribution-table.html"
    };
} );
