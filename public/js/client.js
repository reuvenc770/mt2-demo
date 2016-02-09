/**
 * MT2 App Module
 */
var mt2App = angular.module( 'mt2App' , [] );

mt2App.config( function ( $locationProvider ) {
    $locationProvider.html5Mode( true );
} );

mt2App.directive( 'genericTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" , 
        "bindToController" : { 
            "headers" : "=" ,
            "records" : "=" ,
            "editurl" : "="
        } ,
        "templateUrl" : "js/templates/generic-table.html"
    };
} );

mt2App.directive( 'editButton' , [ '$window' , '$location' , function ( $window , $location ) {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "editurl" : "=" ,
            "recordid" : "="
        } ,
        "templateUrl" : "js/templates/edit-button.html" ,
        "link" : function ( scope , element , attrs )  {
            if ( typeof( scope.ctrl ) != 'undefined' ) {
                element.on( 'click' , function () {
                    var fullEditUrl = scope.ctrl.editurl + scope.ctrl.recordid;
                    $location.url( fullEditUrl );
                    $window.location.href = fullEditUrl;
                } );
            }
        }
    };
} ] );

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

mt2App.service( 'ClientApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/client';

    self.getClients = function ( successCallback , failureCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl } )
            .then( successCallback , failureCallback );
    }
} );

//# sourceMappingURL=client.js.map
