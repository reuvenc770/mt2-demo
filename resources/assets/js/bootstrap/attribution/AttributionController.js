mt2App.controller( 'AttributionController' , [ 'AttributionApiService' , 'FeedApiService' , 'AttributionProjectionService' , 'ThreeMonthReportService' , '$mdToast'  , '$log' , '$location' , 'formValidationService', 'modalService', '$mdDialog' , function ( AttributionApiService , FeedApiService , AttributionProjectionService , ThreeMonthReportService , $mdToast , $log , $location, formValidationService, modalService , $mdDialog ) {
    var self = this;

    self.current = { "id" : 0 , "name" : '' };

    self.models = [];
    self.feeds = [];
    self.clientLevels = {};

    self.levelCopySideNavId = 'levelCopy';
    self.levelCopyModelId = 0;
    self.levelCopyClients = [];
    self.selectFeedCount = 0;
    self.levelCopyClientIndex = {};
    self.disableCopyButton = true;
    self.draggingLevels = false;

    self.currentlyLoading = false;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.pageCount = 0;
    self.reachedFirstPage = true;
    self.reachedMaxPage = false;
    self.formErrors = {};
    self.showModelActions = false;
    self.selectedModelId = 0;
    self.selectedModel = [];
    self.modelQueryPromise = null;
    self.disableProjection = false;
    self.formSubmitted = false;
    self.rowLimit = 30;
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
    };

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
    };

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

    self.initLevelCopyPanel = function () {  ///THIS NEEDS TO BE FIXED CALLING PAGER TO JUST FILL IN ID AND NAME
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
            self.getModel( modelId );
        }
    };

    self.getModel = function ( modelId ) {
        AttributionApiService.getModel(
            modelId ,
            function ( response ) {
                self.current.name = response.data[ 0 ].name;
                self.current.live = response.data[ 0 ].live;
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

    self.saveModel = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);
        var levels = [];
        angular.forEach( self.feeds , function ( value , key ) {
            levels.push( { "id" : value.id , "level" : key + 1 } );
        } );

        AttributionApiService.saveNewModel(
            self.current.name ,
            levels ,
            function ( response ) {
                self.formSubmitted = false;
                self.displayToast( 'Successfully saved Model.' );
            } ,
            function ( response ) {
                self.formSubmitted = false;
                formValidationService.loadFieldErrors(self,response);
                self.displayToast( 'Failed to save Model. Please contact support.' );
        } );
    };

    self.updateModel = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);
        var levels = [];

        angular.forEach( self.feeds , function ( value , key ) {
            levels.push( { "id" : value.id , "level" : key + 1 } );
        } );

        AttributionApiService.updateModel(
            self.getModelId() ,
            self.current.name ,
            levels ,
            function ( response ) {
                self.formSubmitted = false;
                self.displayToast( 'Successfully updated Model.' );
            } ,
            function ( response ) {
                self.formSubmitted = false;
                formValidationService.loadFieldErrors(self,response);
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
        var modelText = "";
        if ( modelRun ) {
            modelId = self.selectedModelId;

            modelText = " for model " + modelId;
        }

        var confirm = $mdDialog.confirm()
            .title( 'Attribution Run Confirmation' )
            .ariaLabel( 'Attribution Run Confirmation' )
            .textContent( 'Are you sure you want to run attribution' + modelText + '?' )
            .ok( 'Yes, I am sure.' )
            .cancel( 'No' );

        $mdDialog.show( confirm ).then( function () {
            AttributionApiService.runAttribution(
                modelId ,
                function ( response ) {
                    self.displayToast( 'Attribution is running.' );
                    self.loadModels();
                } , function ( response ) {
                    self.displayToast( 'Failed to start Attribution Run. Please contact support.' );
                }
            );
        } );
    };

    self.copyModelPreview = function ( $event , currentModelId ) {
        if ( typeof( currentModelId ) !== 'undefined' ) {
            self.current.id = currentModelId;
            self.loadClients( currentModelId );
        }
        self.initLevelCopyPanel();
        modalService.launchModal("#loadModels");

    };

    self.copyLevels = function () {
        AttributionApiService.copyLevels(
            self.getModelId() ,
            self.levelCopyModelId ,
            function ( response ) {
                self.loadClients();

                $mdSidenav( self.levelCopySideNavId ).close();

                self.displayToast( 'Successfully copied feed levels.' );
            } , 
            function ( response ) {
                self.displayToast( 'Failed to copy client levels. Please contact support.' );
            } 
        );
    };

    self.resetLevelFields = function () {
        angular.forEach( self.feeds , function ( currentFeed , currentIndex ) {
            currentFeed.newLevel = currentIndex + 1;
            currentFeed.selected = false;
        } );
        selectFeedCount = 0;
    }; 

    self.changeLevel = function ( feed , index ) {
        var newLevel = feed.newLevel - 1;

        if ( newLevel < 0 || newLevel >= self.feeds.length ) {
            self.displayToast( 'You must choose a level between 1 and ' + self.feeds.length );

            self.resetLevelFields();

            return false;
        }

        if ( newLevel != index ) {
            var startingFeeds = self.feeds.slice( 0 , index );
            var endingFeeds = self.feeds.slice( index + 1 );

            var cleanFeeds = startingFeeds.concat( endingFeeds );

            cleanFeeds.splice( newLevel , 0 , feed );

            self.feeds = cleanFeeds;

            self.resetLevelFields();
        }
    };

    self.confirmDeletion = function ( feedId , ev ) {
        var confirm = $mdDialog.confirm()
            .title( 'Feed Removal' )
            .textContent( 'Would you like to remove this feed from attribution?' )
            .ariaLabel( 'Remove from Attribution' )
            .targetEvent( ev )
            .ok( 'Remove Feed' )
            .cancel( 'Cancel' );

        $mdDialog.show( confirm ).then(
            function () {
                //delete feed
                AttributionApiService.deleteFeed(
                    self.getModelId() ,
                    feedId ,
                    function ( response ) {
                        self.prepopModel();

                        self.displayToast( 'Successfully Removed Feed from Attribution' ); 
                    } ,
                    function ( response ) {
                        self.displayToast( "Failed to remove feed. Please contact support." );
                    }
                );
            } ,
            function () {
                //canceled
                self.displayToast( 'Removal Canceled' );
            }
        );
    };

    self.toggleGroupController = function ( feed , index ) {
        feed.selected ? self.selectFeedCount++:  self.selectFeedCount--;
    };

    self.onLevelRise = function ( feed , index ) {
        if ( feed.selected ) {
            var selectedFeeds = [];
            var otherFeeds = [];
            var firstIndex = null;
                
            angular.forEach( self.feeds , function ( currentFeed , currentIndex ) {
                if ( currentFeed.selected ) {
                    if ( firstIndex === null ) {
                        firstIndex = currentIndex - 1;
                    }

                    selectedFeeds.push( currentFeed );
                } else {
                    otherFeeds.push( currentFeed );
                }
            } );

            if ( firstIndex < 0 ) {
                self.displayToast( 'No room for selected feeds. Please uncheck the first checkbox.' );

                return false;
            }

            var startingFeeds = otherFeeds.slice( 0 , firstIndex );
            var endingFeeds = otherFeeds.slice( firstIndex );

            self.feeds = startingFeeds.concat( selectedFeeds ).concat( endingFeeds );
        } else {
            if ( index === 0 ) {
                return true;
            }

            var startingFeeds = self.feeds.slice( 0 , index - 1 );
            var prevFeed = self.feeds.slice( index - 1 , index );
            var endingFeeds = self.feeds.slice( index + 1 );

            self.feeds = startingFeeds.concat( [ feed ] ).concat( prevFeed ).concat( endingFeeds );
        }

        self.resetLevelFields();
    };

    self.onLevelDrop = function ( feed , index ) {
        if ( feed.selected ) {
            var selectedFeeds = [];
            var otherFeeds = [];
            var lastIndex = -1;
                
            angular.forEach( self.feeds , function ( currentFeed , currentIndex ) {
                if ( currentFeed.selected ) {
                    lastIndex = currentIndex + 2;

                    selectedFeeds.push( currentFeed );
                } else {
                    otherFeeds.push( currentFeed );
                }
            } );

            var startingFeeds = otherFeeds.slice( 0 , lastIndex - selectedFeeds.length );
            var endingFeeds = otherFeeds.slice( lastIndex - selectedFeeds.length );

            self.feeds = startingFeeds.concat( selectedFeeds ).concat( endingFeeds );
        } else {
            var startingFeeds = self.feeds.slice( 0 , index );
            var nextFeed = self.feeds.slice( index + 1 , index + 2 );
            var endingFeeds = self.feeds.slice( index + 2 );

            self.feeds = startingFeeds.concat( nextFeed ).concat( [ feed ] ).concat( endingFeeds );
        }

        self.resetLevelFields();
    };

    self.moveToTop = function ( feed , index ) {
        if ( feed.selected ) {
            var selectedFeeds = [];
            var otherFeeds = [];
                
            angular.forEach( self.feeds , function ( currentFeed , index ) {
                if ( currentFeed.selected ) {
                    selectedFeeds.push( currentFeed );
                } else {
                    otherFeeds.push( currentFeed );
                }
            } );

            self.feeds = selectedFeeds.concat( otherFeeds );
        } else {
            var startingFeeds = self.feeds.slice( 0 , index );
            var endingFeeds = self.feeds.slice( index + 1 );

            self.feeds = [ feed ].concat( startingFeeds ).concat( endingFeeds );
        }

        self.resetLevelFields();
    };

    self.moveToMiddle = function ( feed , index ) {
        if ( feed.selected ) {
            var selectedFeeds = [];
            var otherFeeds = [];
                
            angular.forEach( self.feeds , function ( currentFeed , index ) {
                if ( currentFeed.selected ) {
                    selectedFeeds.push( currentFeed );
                } else {
                    otherFeeds.push( currentFeed );
                }
            } );

            var middleIndex = otherFeeds.length / 2;

            var startingFeeds = otherFeeds.slice( 0 , middleIndex );
            var endingFeeds = otherFeeds.slice( middleIndex );

            self.feeds = startingFeeds.concat( selectedFeeds ).concat( endingFeeds );
        } else {
            var startingFeeds = self.feeds.slice( 0 , index );
            var endingFeeds = self.feeds.slice( index + 1 );

            var newFeeds = startingFeeds.concat( endingFeeds );
            var middleIndex = newFeeds.length / 2;

            var newStartingFeeds = newFeeds.slice( 0 , middleIndex );
            var newEndingFeeds = newFeeds.slice( middleIndex );

            self.feeds = newStartingFeeds.concat( [ feed ] ).concat( newEndingFeeds );
        }

        self.resetLevelFields();
    };

    self.moveToBottom = function ( feed , index ) {
        if ( feed.selected ) {
            var selectedFeeds = [];
            var otherFeeds = [];
                
            angular.forEach( self.feeds , function ( currentFeed , index ) {
                if ( currentFeed.selected ) {
                    selectedFeeds.push( currentFeed );
                } else {
                    otherFeeds.push( currentFeed );
                }
            } );

            self.feeds = otherFeeds.concat( selectedFeeds );
        } else {
            var startingFeeds = self.feeds.slice( 0 , index );
            var endingFeeds = self.feeds.slice( index + 1 );

            self.feeds = startingFeeds.concat( endingFeeds ).concat( [ feed ] );
        }

        self.resetLevelFields();
    };
    self.loadMore = function () {
        self.rowLimit = self.rowLimit + 10;
    };
    self.loadLess = function () {
        self.rowLimit = self.rowLimit - 10;
    };

    //DO WE NEED ANYMORE
    self.syncMt1Levels = function () {
        AttributionApiService.syncMt1Levels(
            function ( response ) {
                self.loadLevels( self.getModelId() );
                self.displayToast( 'Successfully synced MT1 feed levels.' );
            } , 
            function ( response ) {
                self.displayToast( 'Failed to sync MT1 feed levels. Please contact support.' );
            } 
        );
    };

    self.loadClients = function ( altModelId , altSuccessCallback ) {
        var successCallback = function ( response ) { 
            self.feeds = response.data;

            angular.forEach( self.feeds , function ( value , key ) {
                self.clientLevels[ value.id ] = key + 1;
                value.selected = false;
                value.newLevel = key + 1;
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
                        feedList.push( { "id" : client.client_id , "name" : client.username , "selected" : false } );
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
