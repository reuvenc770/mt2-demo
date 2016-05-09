mt2App.directive( 'membershipWidget' , [ "$rootScope" , "$log" , function ( $rootScope , $log ) {
    return {
        "replace" : true ,
        "scope" : {} ,
        "controller" : function () {
            var self = this;

            self.namefield = ( typeof( self.namefield ) !== 'undefined' ? self.namefield : 'name' );
            self.idfield = ( typeof( self.idfield ) !== 'undefined' ? self.idfield : 'id' );

            self.selectAllAvailableRecords = function ( records ) {
                angular.forEach( records , function ( record , key ) {
                    record.selected = true;
                } );
            };

            self.clearAllAvailableRecords = function ( records ) {
                angular.forEach( records , function ( record , key ) {
                    record.selected = false;
                } );
            };

            self.recordMultiSelect = function ( record , recordIndex , records , $event ) {
                var selectionDetails = self.getRecordSelectionDetails( records );

                if ( $event.shiftKey && selectionDetails.count ===  1 ) {
                    var firstRecord = selectionDetails.firstChecked;
                    var lastRecord = record;
                    var currentRecord = null;
                    var increasing = selectionDetails.recordIndex < recordIndex;
                    var boundFound = false;
                    var selectingEnabled = false;

                    for (
                        var currentIndex = ( increasing ? 0 : records.length ) ;       
                        increasing ? currentIndex < records.length : currentIndex >= 0 ;
                        increasing ? currentIndex++ : currentIndex--
                    ) {
                        currentRecord = records[ currentIndex ];
                        boundFound = ( currentRecord === firstRecord || currentRecord === lastRecord );
                        
                        if ( boundFound && selectingEnabled === false ) {
                            selectingEnabled = true;
                            continue;
                        }

                        if ( boundFound ) { break; }

                        if ( selectingEnabled ) { currentRecord.selected = true; }
                    }
                }

                return true;
            };

            self.getRecordSelectionDetails = function ( records ) {
                var count = 0;
                var firstChecked = null;
                var firstCheckedIndex = null;

                angular.forEach( records , function( record , recordIndex ) {
                    if ( record.selected ) {
                        count++;

                        if ( firstChecked === null ) {
                            firstChecked = record;
                            firstCheckedIndex = recordIndex;
                        }
                    }
                } );

                return { "count" : count , "firstChecked" : firstChecked , "firstCheckedIndex" : firstCheckedIndex };
            };

            self.addSelectedRecords = function () {
                angular.forEach( self.recordlist , function ( record , recordIndex ) {
                    if ( record.selected === true ) {
                        record.selected = false;
                        record.chosen = true;

                        self.addSingleRecord( record , false );
                    }
                } );

                if ( typeof( self.updatecallback ) !== 'undefined' ) {
                    self.updatecallback();
                }
            };

            self.addSingleRecord = function ( record , runCallback ) {
                runCallback = ( typeof( runCallback ) === 'undefined' ? true : runCallback );
                record.selected = false;
                record.chosen = true;

                var chosenRecord = {};
                chosenRecord[ self.idfield ] = record[ self.idfield ];
                chosenRecord[ self.namefield ] = record[ self.namefield ];
                chosenRecord.selected = false;
                chosenRecord.original = record;

                self.chosenrecordlist.push( chosenRecord );

                if ( typeof( self.updatecallback ) !== 'undefined' && runCallback === true ) {
                    self.updatecallback();
                }
            };

            self.removeAllSelectedChosenRecords = function () {
                var recordsToDelete = [];

                angular.forEach( self.chosenrecordlist , function ( record , recordIndex ) {
                    if ( record.selected === true ) {
                        recordsToDelete.push( record );
                    }
                } );

                angular.forEach( recordsToDelete , function ( record , recordIndex ) {
                    self.removeSingleChosenRecord( record );
                } );
            };

            self.removeSingleChosenRecord = function ( record ) {
                record.original.chosen = false;

                self.chosenrecordlist.splice( self.chosenrecordlist.indexOf( record ) , 1 );
            };

            $rootScope.$watchCollection( self.widgetname , function ( newClients , oldClients ) {
                angular.forEach( self.recordlist , function ( record , recordIndex ) {
                    if ( newClients.indexOf( parseInt( record[ self.idfield ] , 10 ) ) !== -1 ) {
                        self.addSingleRecord( record );
                    }
                } );

                self.updatecallback();
            } );
        } ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            'recordlist' : '=' ,
            'chosenrecordlist' : '=' ,
            'availablecardtitle' : '=' ,
            'chosenrecordtitle' : '=' ,
            'idfield' : '=?' ,
            'namefield' : '=?' ,
            'updatecallback' : '&' ,
            'widgetname' : '='
        } ,
        "templateUrl" : "js/templates/membership-widget.html"
    };
} ] );
