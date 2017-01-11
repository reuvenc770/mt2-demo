mt2App.directive( 'liteMembershipWidget' , [ "$rootScope" , "$log" , function ( $rootScope , $log ) {
    return {
        "replace" : true ,
        "scope" : {} ,
        "controllerAs" : 'ctrl' ,
        "bindToController" : {
            'recordlist' : '=' ,
            'chosenrecordlist' : '=' ,
            'availablerecordtitle' : '=' ,
            'chosenrecordtitle' : '=' ,
            'idfield' : '=?' ,
            'namefield' : '=?' ,
            'updatecallback' : '&',
            'height' : '=?'
        } ,
        "templateUrl" : 'js/templates/lite-membership-widget.html' ,
        "controller" : function () {
            var self = this;

            self.highlightedSelectedRecords = [];
            self.highlightedChosenRecords = [];

            self.namefield = ( typeof( self.namefield ) !== 'undefined' ? self.namefield : 'name' );
            self.idfield = ( typeof( self.idfield ) !== 'undefined' ? self.idfield : 'id' );
            self.height = ( typeof( self.height ) !== 'undefined' ? self.height : '100' );
            self.addSelectedRecords = function () {
                angular.forEach( self.highlightedSelectedRecords , function ( value , key ) {
                    self.chosenrecordlist.push( value );
                    var index = self.recordlist.indexOf( value );

                    if ( index >= 0 ) {
                        self.recordlist.splice( index , 1 );
                    }
                } );


                if ( typeof( self.updatecallback ) !== 'undefined' ) {
                    self.updatecallback();
                }
            };

            self.removeChosenRecords = function () {
                angular.forEach( self.highlightedChosenRecords , function ( selectedValue , selectedKey ) {
                    self.recordlist.push( selectedValue );
                    var index = self.chosenrecordlist.indexOf( selectedValue );

                    if ( index >= 0 ) {
                        self.chosenrecordlist.splice( index , 1 );
                    }
                } );

                if ( typeof( self.updatecallback ) !== 'undefined' ) {
                    self.updatecallback();
                }
            };
        },
    };
} ] );
