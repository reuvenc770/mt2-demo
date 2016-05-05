mt2App.directive( 'membershipWidget' , [ "$log" , function ( $log ) {
    return {
        "replace" : true ,
        "scope" : {} ,
        "controller" : function () {
            var self = this;

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

                        self.addSingleRecord( record );
                    }
                } );
            };

            self.addSingleRecord = function ( record ) {
                record.selected = false;
                record.chosen = true;

                self.chosenrecordlist.push( {
                    "id" : record.id ,
                    "name" : record.name ,
                    "selected" : false ,
                    "original" : record
                } );
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
        } ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            'recordlist' : '=' ,
            'chosenrecordlist' : '=' ,
            'availablecardtitle' : '=' ,
            'chosenrecordtitle' : '='
        } ,
        "templateUrl" : "js/templates/membership-widget.html"
    };
} ] );
