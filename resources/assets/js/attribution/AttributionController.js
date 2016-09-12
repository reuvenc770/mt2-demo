mt2App.controller( 'AttributionController' , [ 'AttributionApiService' , 'FeedApiService' , 'AttributionProjectionService' , 'ThreeMonthReportService' , '$mdToast'  , '$mdSidenav' , '$log' , '$location' , function ( AttributionApiService , FeedApiService , AttributionProjectionService , ThreeMonthReportService , $mdToast , $mdSidenav , $log , $location ) {
    var self = this;

    self.current = { "id" : 0 , "name" : '' };

    self.models = [];
    self.feeds = [];
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

    self.showModelActions = false;
    self.selectedModelId = 0;
    self.selectedModel = [];
    self.modelQueryPromise = null;
    self.disableProjection = false;

    self.reportRecords = [];
    self.reportRecordTotals = {};
    self.reportQueryPromise = null;

    self.currentMonth = ThreeMonthReportService.currentMonth;
    self.lastMonth = ThreeMonthReportService.lastMonth;
    self.twoMonthsAgo = ThreeMonthReportService.twoMonthsAgo;

    self.feedNameMap = ThreeMonthReportService.feedNameMap;
    self.clientNameMap = ThreeMonthReportService.clientNameMap;
    self.exportReport = ThreeMonthReportService.exportReport;

    self.projectionRecords = [];
    self.initProjectionChart = AttributionProjectionService.initChart;
    self.refreshProjectionPage = AttributionProjectionService.refreshPage;

    self.initIndexPage = function () {
        self.loadModels();
        self.loadReportRecords();
        ThreeMonthReportService.loadClientAndFeedNames();
    };

    self.initProjectionPage = function () {
        ThreeMonthReportService.loadClientAndFeedNames();
        AttributionProjectionService.initPage();
    }

    self.toggleModelActionButtons = function () {
        if ( self.selectedModel.length > 0 ) {
            self.selectedModelId = self.selectedModel[0].id;
            self.showModelActions = true;

            if ( self.selectedModel[0].live == 1 ) {
                self.disableProjection = true;
            } else {
                self.disableProjection = false;
            }
        } else {
            self.selectedModelId = 0;
            self.showModelActions = false;
        }
    };

    self.loadReportRecords = function () {
        self.reportQueryPromise = ThreeMonthReportService.getRecords(
            function ( response ) {
                self.reportRecords = response.data.records;
                self.reportRecordTotals = response.data.totals;
            } ,
            function ( response ) {
                $mdToast.show(
                    $mdToast.simple()
                        .textContent( 'Failed to load Attribution Report Records. Please contact support.' )
                        .hideDelay( 1500 )
                );
            }
        );
    }

    self.loadProjectionRecords = function () { 
        self.projectionReportQueryPromise = AttributionProjectionService.loadRecords(
            function ( response ) {
                self.projectionRecords = response.data;
            } ,
            function ( response ) {
                $mdToast.show(
                    $mdToast.simple()
                        .textContent( 'Failed to load projection table data. Please contact support.' )
                        .hideDelay( 1500 )
                );
            }
        );
    };

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
                self.feeds[ clientIndex ].attribution_level = levelClient.level;
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

        self.modelQueryPromise = AttributionApiService.getModels(
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

        angular.forEach( self.feeds , function ( value , key ) {
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

        angular.forEach( self.feeds , function ( value , key ) {
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

    self.setModelLive = function () {
        AttributionApiService.setModelLive(
            self.selectedModelId ,
            function ( response ) {
                self.displayToast( 'Feed Levels are updated. Please run attribution.' );

                self.loadModels();
            },
            function ( response ) {
                self.displayToast( 'Failed to update Feed Levels. Please contact support.' );
            }
        );
    };

    self.runAttribution = function ( modelRun ) {
        var modelId = '';
        if ( modelRun ) {
            modelId = self.selectedModelId;
        }

        AttributionApiService.runAttribution(
            modelId ,
            function ( response ) {
                self.displayToast( 'Attribution is running.' );
            } , function ( response ) {
                self.displayToast( 'Failed to start Attribution Run. Please contact support.' );
            }
        );
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
            self.feeds = response.data;

            angular.forEach( self.feeds , function ( value , key ) {
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
            FeedApiService.getAllFeeds(
                function ( response ) {
                    var feedList = [];

                    angular.forEach( response.data , function ( client , key ) {
                        feedList.push( { "id" : client.client_id , "name" : client.username } );
                    } );

                    self.feeds = feedList;
                } ,
                function ( response ) {
                    self.displayToast( 'Failed to load Feeds. Please contact support.' );
                }
            );
        } else {
            AttributionApiService.getModelFeeds(
                modelId ,
                successCallback ,
                function ( response ) {
                    self.displayToast( 'Failed to load Feeds. Please contact support.' );
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
