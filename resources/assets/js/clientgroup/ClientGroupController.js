mt2App.controller( 'ClientGroupController' , [ '$rootScope' , '$log' , '$window' , '$location' , 'ClientGroupApiService' , 'ClientApiService' , function ( $rootScope , $log , $window , $location , ClientGroupApiService , ClientApiService ) {
    /**
     * Contants
     */
    var self = this;
    self.createUrl = '/clientgroup/create';
    self.testUser = 217;


    /**
     * Pagination Properties
     */
    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;


    /**
     * Data Fields and Containers
     */
    self.clientGroups = [];
    self.clientMap = {};
    self.current = {
        "gid" : 0 ,
        "user_id" : 0 ,
        "groupName" : '' ,
        "clients" : '' ,
        "excludeFromSuper" : false
    };
    self.formErrors = [];
    

    /**
     * Loading Flags
     */
    self.creatingClientGroup = false;
    self.copyingClientGroup = false;
    self.deletingClientGroup = false;


    /**
     * Chip Field Properties
     */
    self.selectedClients = [];
    self.clientList = [];
    self.availableWidgetTitle = "Available Clients";
    self.chosenWidgetTitle = "Chosen Clients";
    self.clientNameField = "username";
    self.clientIdField = "client_id";

    /**
     * Init Methods
     */
    self.prepopPage = function () {
        var currentPath = $location.path();
        var pathParts = currentPath.match( new RegExp( /(\d+)/ ) );
        var prepopPage = (
            pathParts !== null
            && angular.isNumber( parseInt( pathParts[ 0 ] ) )
        );

        if ( prepopPage ) {
            self.current.gid = pathParts[ 0 ];

            ClientGroupApiService.getClientGroup(
                self.current.gid ,
                self.prepopPageDataSuccessCallback ,
                self.prepopPageDataFailureCallback
            );

            ClientGroupApiService.getClients(
                self.current.gid ,
                self.prepopPageClientsSuccessCallback ,
                self.prepopPageClientsFailureCallback
            );
        }
    };

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

    self.loadClientList = function () {
        ClientApiService.getAllClients( function ( response ) {
            self.clientList = response.data;
        } , function ( response ) {
            self.setModalLabel( 'Error' );
            self.setModalBody( 'Failed to load Client Group\'s list of clients.' );

            self.launchModal();
        } );
    }


    /**
     * Button Click Handlers
     */
    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };

    self.saveClientGroup = function ( event ) {
        self.resetFieldErrors();
        self.creatingClientGroup = true;
        self.updateClientFormField();

        var requestData = self.current;
        requestData[ 'action' ] = 'create';
        if ( requestData[ 'user_id' ] == 0 ) requestData[ 'user_id' ] = self.testUser;

        ClientGroupApiService.createClientGroup(
            requestData , 
            self.SuccessCallBackRedirect ,
            self.saveClientGroupFailureCallback
        );
    };

    self.updateClientGroup = function ( event ) {
        self.resetFieldErrors();
        self.updatingClientGroup = true;
        self.updateClientFormField();

        var requestData = self.current;
        requestData[ 'action' ] = 'update';

        ClientGroupApiService.updateClientGroup(
            requestData , 
            self.SuccessCallBackRedirect ,
            self.updateClientGroupFailureCallback
        );
    };

    self.copyClientGroup = function ( groupID ) {
        self.copyingClientGroup = true;

        ClientGroupApiService.copyClientGroup(
            groupID ,
            self.copyClientGroupSuccessCallback ,
            self.copyClientGroupFailureCallback
        );
    };

    self.deleteClientGroup = function ( groupID ) {
        self.deletingClientGroup = true;

        ClientGroupApiService.deleteClientGroup(
            groupID ,
            self.SuccessCallBackRedirect ,
            self.deleteClientGroupFailureCallback
        );
    };
    
    /**
     * Watchers
     */
    $rootScope.$on( 'updatePage' , function () {
        $( '.collapse' ).collapse( 'hide' );
        self.loadClientGroups();
    } );

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
     * Success Callbacks
     */
    self.prepopPageDataSuccessCallback = function ( response ) {
        self.current.groupName = response.data.name;
        console.log(self.current.groupName);
        self.current.excludeFromSuper = response.data.excludeFromSuper;
    };

    self.prepopPageClientsSuccessCallback = function ( response ) {
        angular.forEach( response.data.records , function ( value , key ) {
            self.selectedClients.push( { "id" : value.client_id , "name" : value.name } );
        } );
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/clientgroup' );
        $window.location.href = '/clientgroup';
    };

    self.copyClientGroupSuccessCallback = function ( response ) {
        self.copyingClientGroup = false;

        var redirectUrl = 'clientgroup/edit/' + response.data.id;

        $location.url( redirectUrl );
        $window.location.href = redirectUrl;
    };

    self.loadClientGroupsSuccessCallback = function ( response ) {
        self.currentlyLoading = 0;

        self.clientGroups = response.data.data;
        self.pageCount = response.data.last_page;
    };

    self.loadClientsSuccessCallback = function ( response ) {
        self.clientMap[ response.data.groupid ] = response.data.records;
    };


    /**
     * Failure Callbacks
     */
    self.prepopPageDataFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load Client Group details.' );

        self.launchModal();
    };

    self.prepopPageClientsFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load Client Group\'s clients.' );

        self.launchModal();
    };

    self.saveClientGroupFailureCallback = function ( response ) {
        self.creatingClientGroup = false;

        self.loadFieldErrors( response );
    };

    self.updateClientGroupFailureCallback = function ( response ) {
        self.updatingClientGroup = false;

        self.loadFieldErrors( response );
    };

    self.copyClientGroupFailureCallback = function ( response ) {
        self.copyingClientGroup = false;

        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to copy client group.' );

        self.launchModal();
    };

    self.deleteClientGroupFailureCallback = function ( response ) {
        self.deletingClientGroup = false;

        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to delete client group.' );

        self.launchModal();
    };

    self.loadClientGroupsFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load client groups.' );

        self.launchModal();
    };

    self.loadClientsFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load clients.' );

        self.launchModal();
    };
} ] );
