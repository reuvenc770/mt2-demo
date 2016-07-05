mt2App.controller( 'AttributionController' , [ 'AttributionApiService' , 'ClientApiService' , '$mdToast' , '$log' , function ( AttributionApiService , ClientApiService , $mdToast , $log ) {
    var self = this;

    self.current = { "name" : '' };

    self.models = [];
    self.clients = [];

    self.currentlyLoading = false;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.pageCount = 0;
    self.reachedFirstPage = true;
    self.reachedMaxPage = false;

    self.viewAdd = function () {};

    self.loadModels = function () {
        self.currentlyLoading = 1;

        AttributionApiService.getModels(
            self.currentPage ,
            self.paginationCount ,
            function ( response ) {
                self.models = response.data.data;

                self.pageCount = response.data.last_page;

                self.currentlyLoading = 0;
            } ,
            function ( response ) { 
                $mdToast.show(
                    $mdToast.simple()
                        .textContent( 'Failed to load Models. Please contact support.' )
                        .hideDelay( 1500 )
                );
            }
        );
    };

    self.saveModel = function () {
        var levels = [];

        angular.forEach( self.clients , function ( value , key ) {
            levels.push( { "id" : value.client_id , "level" : value.attribution_level } );
        } );

        AttributionApiService.saveNewModel(
            self.current.name ,
            levels ,
            function ( response ) {
                $mdToast.show(
                    $mdToast.simple()
                        .textContent( 'Successfully saved Model.' )
                        .hideDelay( 1500 )
                );
            } ,
            function ( response ) {
                $mdToast.show(
                    $mdToast.simple()
                        .textContent( 'Failed to save Model. Please contact support.' )
                        .hideDelay( 1500 )
                );
        } );
    };

    self.loadClients = function () {
        ClientApiService.getAllClients(
            function ( response ) { 
                self.clients = response.data;

                var attr_level = 1;
                angular.forEach( self.clients , function ( value , key ) {
                    value.attribution_level = attr_level;

                    attr_level++;
                } );
            } ,
            function ( response ) {
                $mdToast.show(
                    $mdToast.simple()
                        .textContent( 'Failed to load Clients. Please contact support.' )
                        .hideDelay( 1500 )
                );
            }
        );
    };
} ] );
