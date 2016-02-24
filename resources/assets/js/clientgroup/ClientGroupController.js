mt2App.controller( 'ClientGroupController' , [ '$rootScope' , '$log' , '$window' , '$location' , 'ClientGroupApiService' , function ( $rootScope , $log , $window , $location , ClientGroupApiService ) {
    var self = this;

    self.createUrl = '/clientgroup/create';

    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;

    self.clientGroups = [];
    self.clientMap = {};

    self.loadClientGroups = function () {
        self.currentlyLoading = 1;

        ClientGroupApiService.getClientGroups(
            self.currentPage ,
            self.paginationCount ,
            self.loadClientGroupsSuccessCallback ,
            self.loadClientGroupsFailureCallback
        );
    };

    self.loadClients = function ( groupID ) {
        ClientGroupApiService.getClients(
            groupID ,
            self.loadClientsSuccessCallback ,
            self.loadClientsFailureCallback
        );
    }

    /**
     * Button Click Handlers
     */
    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };


    /**
     * Watchers
     */
    $rootScope.$on( 'updatePage' , function () {
        $( '.collapse' ).collapse( 'hide' );
        self.loadClientGroups();
    } );

    /**
     * Callbacks
     */
    self.loadClientGroupsSuccessCallback = function ( response ) {
        self.currentlyLoading = 0;

        self.clientGroups = response.data.data;
        self.pageCount = response.data.last_page;
    };

    self.loadClientGroupsFailureCallback = function ( response ) {
    };

    self.loadClientsSuccessCallback = function ( response ) {
        self.clientMap[ response.data.groupid ] = response.data.records;
    };

    self.loadClientsFailureCallback = function ( response ) {
    };
} ] );
