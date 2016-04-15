mt2App.controller( 'ClientAttributionController' , [ 'ClientApiService' , '$rootScope' , '$log' , function ( ClientApiService , $rootScope , $log ) {
    var self = this;

    self.clients = [];

    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.currentlyLoading = 0;

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
        $log.log( response );
    };

    self.setAttribution = function ( id , level ) {
        ClientApiService.setAttribution( id , level , self.setAttributionSuccessCallback , self.setAttributionFailureCallback );
    };

    self.setAttributionSuccessCallback = function ( response ) {
        $log.log( response );
    };
        
    self.setAttributionFailureCallback = function ( response ) {
        $log.log( response );
    }

    self.deleteAttribution = function ( id ) {
        ClientApiService.deleteAttribution( id , self.deleteAttributionSuccessCallback , self.deleteAttributionFailureCallback );
    };

    self.deleteAttributionSuccessCallback = function ( response ) {
        $log.log( response );
    };
        
    self.deleteAttributionFailureCallback = function ( response ) {
        $log.log( response );
    }

    $rootScope.$on( 'updatePage' , function () {
        self.loadClients();
    } );
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
