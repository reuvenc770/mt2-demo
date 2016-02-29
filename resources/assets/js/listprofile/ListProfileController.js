mt2App.controller( 'ListProfileController' , [ '$rootScope' , '$log' , '$mdBottomSheet' , '$mdDialog' , 'ListProfileApiService' , 'ClientApiService' , 'ClientGroupApiService' , function ( $rootScope , $log , $mdBottomSheet , $mdDialog , ListProfileApiService , ClientApiService , ClientGroupApiService ) {
    var self = this;

    /**
     * Form Fields
     */
    self.current = {
        'profileType' : 'v1' ,
        'clientGroup' : [] ,
        'deliveryDays' : 0 ,
        'isps' : [] ,
        'gender' : 'F'
    };

    /**
     * Gender Switch
     */
    self.genderType = 'any';

    /**
     * Client Group Autocomplete
     */
    self.clientGroupSearchText = '';
    self.clientGroupList = [
        { "id" : 1 , "name" : "Client Group 1" } ,
        { "id" : 2 , "name" : "Client Group 2" } ,
        { "id" : 3 , "name" : "Client Group 3" } ,
        { "id" : 4 , "name" : "Client Group 4" } ,
        { "id" : 5 , "name" : "Client Group 5" } ,
        { "id" : 6 , "name" : "Client Group 6" } ,
        { "id" : 7 , "name" : "Client Group 7" } ,
        { "id" : 8 , "name" : "Client Group 8" } ,
        { "id" : 9 , "name" : "Client Group 9" } ,
        { "id" : 10 , "name" : "Client Group 10" }
    ];

    self.getClientGroups = function ( groupSearchText ) {
        return groupSearchText ? self.clientGroupList.filter( function ( group ) {
            return group.name.toLowerCase().indexOf( groupSearchText.toLowerCase() ) === 0;
        } ) : self.clientGroupList;
    }

    /**
     * ISP AutoComplete
     */
    $rootScope.selectedIsps = {};
    self.currentSelectedIsp = '';
    self.ispChipList = [];

    self.updateIspCheckboxList = function ( item ) {
        if ( typeof( item ) !== 'undefined' ) {
            $rootScope.selectedIsps[ parseInt( item.id ) ] = item.name;
        }
    }

    self.removeIspChip = function ( $chip ) {
        $rootScope.selectedIsps[ parseInt( $chip.id ) ] = false;
    };

    $rootScope.$watchCollection( 'selectedIsps' , function ( newIsps , oldIsps ) {
        angular.forEach( newIsps , function ( value , key ) {
            var currentChip = { "id" : parseInt( key ) , "name" : value };

            var chipIndex = self.ispChipList.map(
                function ( chip ) { return parseInt( chip.id ) }        
            ).indexOf( parseInt( key ) ); 

            var chipExists = ( chipIndex !== -1 );

            if ( value !== false && !chipExists ) {
                self.ispChipList.push( currentChip );
            } else if ( value === false && chipExists ) {
                self.ispChipList.splice( chipIndex , 1 );
            }
        });
    } );

    self.ispSearchText = '';
    self.ispList = [
        { "id" : 1 , "name" : "AOL" } ,
        { "id" : 2 , "name" : "AOLOthers" } ,
        { "id" : 3 , "name" : "AOLUK" } ,
        { "id" : 4 , "name" : "Apple" } ,
        { "id" : 5 , "name" : "ATT" } ,
        { "id" : 6 , "name" : "BTINTERNET" } ,
        { "id" : 7 , "name" : "Cable_Broadband" } ,
        { "id" : 8 , "name" : "Cloudmark" } ,
        { "id" : 9 , "name" : "Comcast" } ,
        { "id" : 10 , "name" : "CoxF" } ,
        { "id" : 11 , "name" : "Facebook" } ,
        { "id" : 12 , "name" : "ForeignAOL" } ,
        { "id" : 13 , "name" : "ForeignHotmail" } ,
        { "id" : 14 , "name" : "ForeignYahoo" } ,
        { "id" : 15 , "name" : "France" } ,
        { "id" : 16 , "name" : "German" } ,
        { "id" : 17 , "name" : "Gmail" } ,
        { "id" : 18 , "name" : "GmailOthers" } ,
        { "id" : 19 , "name" : "GMX" } ,
        { "id" : 20 , "name" : "Hotmail" } ,
        { "id" : 21 , "name" : "HotmailOthers" } ,
        { "id" : 22 , "name" : "HotmailUK" } ,
        { "id" : 23 , "name" : "Italy" } ,
        { "id" : 24 , "name" : "Others" } ,
        { "id" : 25 , "name" : "safeothers" } ,
        { "id" : 26 , "name" : "UK" } ,
        { "id" : 27 , "name" : "VerizonF" } ,
        { "id" : 28 , "name" : "Wanadoo" } ,
        { "id" : 29 , "name" : "Yahoo" } ,
        { "id" : 30 , "name" : "YahooOthers" } ,
        { "id" : 31 , "name" : "YahooUK" }
    ];

    self.getIsps = function ( ispSearchText ) {
        return ispSearchText ? self.ispList.filter( function ( isp ) {
            return isp.name.toLowerCase().indexOf( ispSearchText.toLowerCase() ) === 0;
        } ) : self.ispList;
    }

    /**
     * Targeting Chips
     */
    self.sourceList = [];
    self.seedList = [];
    self.zipList = [];

    self.preventDelimitedChips = function ( listName , chip ) {
        $log.log( 'Chip' );
        $log.log( chip );

        $log.log( 'Found delimiter?' );
        $log.log( chip.search( /[;,|\n]/g ) );

        if ( chip.search( /[;,|]/g ) > -1 ) {
            self.addChips( listName , chip );   
            return null;
        } else return;
    };

    self.addChips = function ( listName , chip ) {
        if ( chip.search( /[;,|]/g ) > -1 ) {
            var chipList = [];
            
            if ( chip.search( /[;]/g ) > -1 ) {
                chipList = chip.split( ';' );
            } else if ( chip.search( /[,]/g ) > -1 ) {
                chipList = chip.split( ',' );
            } else if ( chip.search( /[|]/g ) > -1 ) {
                chipList = chip.split( '|' );
            }

            $log.log( 'Chip List:' );
            $log.log( chipList );

            angular.forEach( chipList , function ( value , key ) {
                if ( self[ listName ].indexOf( value ) === -1 ) {
                    self[ listName ].push( value );
                }
            } );
        }
    };

    /**
     * Range Widget
     */
    self.rangeList = [
        { "type" : "age" , "subtype" : "" , 'min' : "20" , "max" : "30" } ,
        { "type" : "deliverable" , "subtype" : "date" , "min" : "2016-01-01" , "max" : "2016-01-30" } ,
        { "type" : "deliverable" , "subtype" : "" , "min" : "20" , "max" : "1000" }
    ];

    self.rangeData = {
        "count" : {
            "age" : { "subtitle" : 'Age Count Range' , "min" : 0 , "max" : 0 } ,
            "deliverable" : [
                { "min" : 0 , "max" : 0 } ,
                { "min" : 0 , "max" : 0 } ,
                { "min" : 0 , "max" : 0 }
            ] ,
            "openers" : [
                { "min" : 0 , "max" : 0 } ,
                { "min" : 0 , "max" : 0 } ,
                { "min" : 0 , "max" : 0 }
            ] ,
            "clickers" : [
                { "min" : 0 , "max" : 0 } ,
                { "min" : 0 , "max" : 0 } ,
                { "min" : 0 , "max" : 0 }
            ] ,
            "converters" : [
                { "min" : 0 , "max" : 0 } ,
                { "min" : 0 , "max" : 0 } ,
                { "min" : 0 , "max" : 0 }
            ] ,
        } ,
        "date" : {
            "age" : { "min" : 0 , "max" : 0 } ,
            "deliverable" : { "min" : 0 , "max" : 0 } ,
            "openers" : { "min" : 0 , "max" : 0 } ,
            "clickers" : { "min" : 0 , "max" : 0 } ,
            "converters" : { "min" : 0 , "max" : 0 }
        }
    };

    self.addRange = function ( type , subtype ) {
        $log.log( type );
        $log.log( subtype );

        var currentDialog = self.rangeDialogs[ type ][ subtype ];
  
        $log.log( 'Current Dialog:' );
        $log.log( currentDialog );

        $mdDialog.show( currentDialog );
    }

    /**
     * Range Widget
     */
    self.rangeDialogs = {
        "count" : {
            "age" : {
                "templateUrl" : "js/templates/listprofile-range-count-dialog.html" ,
                "controllerAs" : 'ctrl' ,
                "bindToController" : true ,
                "locals" : {
                    "label" : 'Age Range' ,
                    "min" : 0 ,
                    "max" : 0 
                }
            } ,
            "deliverables" : {
                "templateUrl" : "js/templates/listprofile-range-count-dialog.html" ,
                "bindToController" : true ,
                "controllerAs" : 'ctrl' ,
                "locals" : {
                    "subheader" : "" ,
                    "min" : 0 ,
                    "max" : 0
                }
            } ,
            "openers" : {
                "templateUrl" : "js/templates/listprofile-range-count-dialog.html" ,
                "controllerAs" : 'ctrl' ,
                "bindToController" : true ,
                "locals" : {
                    "subheader" : "" ,
                    "min" : 0 ,
                    "max" : 0
                }
            } ,
            "clickers" : {
                "templateUrl" : "js/templates/listprofile-range-count-dialog.html" ,
                "controllerAs" : 'ctrl' ,
                "bindToController" : true ,
                "locals" : {
                    "subheader" : "" ,
                    "min" : 0 ,
                    "max" : 0
                }
            } ,
            "converters" : {
                "templateUrl" : "js/templates/listprofile-range-count-dialog.html" ,
                "controllerAs" : 'ctrl' ,
                "bindToController" : true ,
                "locals" : {
                    "subheader" : "" ,
                    "min" : 0 ,
                    "max" : 0
                }
            }
        } ,
        "date" : {

        }
    };

    self.rangeMenuItems = [
        { "name" : "Age" , "type" : "age" , "subtype" : "" } ,
        { "name" : "Deliverables" , "type" : "deliverables" , "subtype" : "" } ,
        { "name" : "Deliverable Date" , "type" : "deliverables" , "subtype" : "date" } ,
        { "name" : "Openers" , "type" : "openers" , "subtype" : "" } ,
        { "name" : "Openers Date" , "type" : "openers" , "subtype" : "date" } ,
        { "name" : "Clickers" , "type" : "clickers" , "subtype" : "" } ,
        { "name" : "Clickers Date" , "type" : "clickers" , "subtype" : "date" } ,
        { "name" : "Converters" , "type" : "converters" , "subtype" : "" } ,
        { "name" : "Converters Date" , "type" : "converters" , "subtype" : "date" }
    ];

} ] );
