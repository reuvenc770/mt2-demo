mt2App.controller( 'AttributionController' , [ 'AttributionApiService' , 'ClientApiService' , '$mdToast'  , '$mdSidenav' , '$log' , '$location' , function ( AttributionApiService , ClientApiService , $mdToast , $mdSidenav , $log , $location ) {
    var self = this;

    self.current = { "id" : 0 , "name" : '' };

    self.models = [];
    self.clients = [];
    self.clientLevels = {};

    self.levelCopySideNavId = 'levelCopy';
    self.levelCopyModelId = 0;
    self.levelCopyClients = [];
    self.levelCopyClientIndex = {};
    self.disableCopyButton = true;

    self.currentlyLoading = false;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.pageCount = 0;
    self.reachedFirstPage = true;
    self.reachedMaxPage = false;

    self.initLevelCopyPanel = function () {
        self.loadModels();
    };
 
    self.loadLevelPreview = function () {
        self.loadClients( self.levelCopyModelId , function ( response ) {
            self.levelCopyClients = response.data;
            self.disableCopyButton = false;
        } );
    };

    self.getModelId = function () {
        if ( self.current.id > 0 ) { return self.current.id; }

        var currentPath = $location.path();
        var pathParts = currentPath.match( new RegExp( /(\d+)/ ) );

        if ( pathParts === null ) { return null; }

        var modelId = parseInt( pathParts[ 0 ] );
        var idExists = (
            pathParts !== null
            && angular.isNumber( modelId )
        ); 

        if ( idExists ) { return modelId; }
        else { return null; }
    };

    self.prepopModel = function () {
        var modelId = self.getModelId();

        if ( modelId !== null ) {
            self.current.id = modelId;

            self.loadClients();
            self.getModelName( modelId );
        }
    };

    self.getModelName = function ( modelId ) {
        AttributionApiService.getModel(
            modelId ,
            function ( response ) {
                self.current.name = response.data[ 0 ].name;
            } ,
            function ( response ) {
                self.displayToast( 'Failed to load Model Information. Please contact support.' );
            }
        );
    };

    self.loadLevels = function ( modelId , customSuccessCallback ) {
        var defaultSuccessCallback = function ( response ) {
            angular.forEach( response.data , function ( levelClient , key ) {
                var clientIndex = self.clientIndex[ levelClient.client_id ];
                self.clients[ clientIndex ].attribution_level = levelClient.level;
            } );
        };

        AttributionApiService.getLevels(
            modelId ,
            ( typeof( customSuccessCallback ) != 'undefined' ? customSuccessCallback : defaultSuccessCallback ) ,
            function ( response ) {
                $mdToast.show(
                    $mdToast.simple()
                        .textContent( 'Failed to load Attribution Levels. Please contact support.' )
                        .hideDelay( 1500 )
                );
            }
        );
    };

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
                self.displayToast( 'Failed to load Models. Please contact support.' );
            }
        );
    };

    self.saveModel = function ( $event , form ) {
        var errorFound = false;

        angular.forEach( form.$error.required , function ( field ) {
            field.$setDirty();
            field.$setTouched();

            errorFound = true;
        } );

        if ( errorFound ) {
            self.displayToast( 'Please fix errors and try again.' );

            return false;
        }

        var levels = [];

        angular.forEach( self.clients , function ( value , key ) {
            levels.push( { "id" : value.id , "level" : key + 1 } );
        } );

        AttributionApiService.saveNewModel(
            self.current.name ,
            levels ,
            function ( response ) {
                self.displayToast( 'Successfully saved Model.' );
            } ,
            function ( response ) {
                self.displayToast( 'Failed to save Model. Please contact support.' );
        } );
    };

    self.updateModel = function ( $event , form ) {
        var errorFound = false;

        angular.forEach( form.$error.required , function ( field ) {
            field.$setDirty();
            field.$setTouched();

            errorFound = true;
        } );

        if ( errorFound ) {
            self.displayToast( 'Please fix errors and try again.' );

            return false;
        }

        var levels = [];

        angular.forEach( self.clients , function ( value , key ) {
            levels.push( { "id" : value.id , "level" : key + 1 } );
        } );

        AttributionApiService.updateModel(
            self.getModelId() ,
            self.current.name ,
            levels ,
            function ( response ) {
                self.displayToast( 'Successfully updated Model.' );
            } ,
            function ( response ) {
                self.displayToast( 'Failed to update Model. Please contact support.' );
        } );
    };

    self.copyModelPreview = function ( $event , currentModelId ) {
        $log.log( currentModelId );

        if ( typeof( currentModelId ) !== 'undefined' ) {
            self.current.id = currentModelId;

            self.loadClients( currentModelId );
        }

        $mdSidenav( self.levelCopySideNavId ).open();
    };

    self.copyLevels = function () {
        AttributionApiService.copyLevels(
            self.getModelId() ,
            self.levelCopyModelId ,
            function ( response ) {
                self.loadClients();

                $mdSidenav( self.levelCopySideNavId ).close();

                self.displayToast( 'Successfully copied client levels.' );
            } , 
            function ( response ) {
                self.displayToast( 'Failed to copy client levels. Please contact support.' );
            } 
        );
    };

    self.loadClients = function ( altModelId , altSuccessCallback ) {
        var successCallback = function ( response ) { 
            self.clients = response.data;

            angular.forEach( self.clients , function ( value , key ) {
                self.clientLevels[ value.id ] = key + 1;
            } );
        };

        var modelId = self.getModelId();

        if ( typeof( altModelId ) !== 'undefined'  ) {
            modelId = altModelId;
        }

        if( typeof( altSuccessCallback ) !== 'undefined' ) {
            successCallback = altSuccessCallback;
        }

        if ( modelId === null ) {
            ClientApiService.getAllClients(
                function ( response ) {
                    var clientList = [];

                    angular.forEach( response.data , function ( client , key ) {
                        clientList.push( { "id" : client.client_id , "name" : client.username } );
                    } );

                    self.clients = clientList;
                } ,
                function ( response ) {
                    self.displayToast( 'Failed to load Clients. Please contact support.' );
                }
            );
        } else {
            AttributionApiService.getModelClients(
                modelId ,
                successCallback ,
                function ( response ) {
                    self.displayToast( 'Failed to load Clients. Please contact support.' );
                }
            );
        }
    };

    self.displayToast = function ( message ) {
        $mdToast.show(
            $mdToast.simple()
                .textContent( message )
                .hideDelay( 1500 )
        );
    };
} ] );
