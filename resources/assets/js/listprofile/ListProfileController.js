mt2App.controller( 'ListProfileController' , [ '$rootScope' , '$log' , '$location' , '$window' , '$mdDialog' , 'ListProfileApiService' , 'ClientApiService' , 'ClientGroupApiService' , function ( $rootScope , $log , $location , $window , $mdDialog , ListProfileApiService , ClientApiService , ClientGroupApiService ) {
    var self = this;
    self.testUser = 217;

    self.profileList = [];
    self.createUrl = '/listprofile/create';
    self.creatingListProfile = false;
    self.updatingListProfile = false;

    /**
     * Pagination Properties
     */
    self.currentlyLoading = false;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;

    /**
     * Button Click Handlers
     */
    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };

    /**
     * Form Fields
     */
    self.profileType = 'v1';
    self.genderType = 'any';

    self.current = {
        'profile_name' : '' ,
        'volume_desired' : null ,
        'cgroupid' : [] ,
        'deliveryDays' : 0 ,
        'isps' : [] ,
        'gender' : 'F'
    };

    self.v1Form = {
        'user_id' : self.testUser ,
        'form_version' : 1 ,
        'profile_name' : '' ,
        'DeliveryDays' : 0 ,
        'isps' : '' ,
        'cgroupid' : 0 ,
        'gender' : '' ,
        'surl' : '' ,
        'zips' : '' ,
        'seeds' : '' ,
        'min_age' : 0 ,
        'max_age' : 0 ,
        'ostart' : 0 ,
        'oend' : 0 ,
        'cstart' : 0 ,
        'cend' : 0 ,
        'dstart' : 0 ,
        'dend' : 0 ,
        'convert_start' : 0 ,
        'convert_end' : 0 ,
        'ostart1' : 0 ,
        'oend1' : 0 ,
        'cstart1' : 0 ,
        'cend1' : 0 ,
        'dstart1' : 0 ,
        'dend1' : 0 ,
        'convert_start1' : 0 ,
        'convert_end1' : 0 ,
        'ostart2' : 0 ,
        'oend2' : 0 ,
        'cstart2' : 0 ,
        'cend2' : 0 ,
        'dstart2' : 0 ,
        'dend2' : 0 ,
        'convert_start2' : 0 ,
        'convert_end2' : 0 ,
        'ostart_date' : '' ,
        'oend_date' : '' ,
        'cstart_date' : '' ,
        'cend_date' : '' ,
        'dstart_date' : '' ,
        'dend_date' : '' ,
        'convert_start_date' : '' ,
        'convert_end_date' : '' ,
        'export' : 0 ,
        'clientid' : 0 ,
        'dupCnt' : 0 ,
        'randomize_flag' : 'Y' ,
        'dfactor' : 0 ,
        'send_international' : 'Y'
    };

    self.v2Form = {
        'form_version' : 2 ,
        'profile_name' : '' ,
        'volumne_desired' : 0 ,
        'isps' : '' ,
        'cgroupid' : 0 ,
        'gender' : '' ,
        'surl' : '' ,
        'zips' : '' ,
        'min_age' : 0 ,
        'max_age' : 0 ,
        'ostart' : 0 ,
        'oend' : 0 ,
        'cstart' : 0 ,
        'cend' : 0 ,
        'dstart' : 0 ,
        'dend' : 0 ,
        'convert_start' : 0 ,
        'convert_end' : 0 ,
        'ostart_date' : '' ,
        'oend_date' : '' ,
        'cstart_date' : '' ,
        'cend_date' : '' ,
        'dstart_date' : '' ,
        'dend_date' : '' ,
        'convert_start_date' : '' ,
        'convert_end_date' : '' ,
        'dfactor' : 0 ,
        'send_international' : 'Y'
    };

    self.v3Form = {
        'form_version' : 3 ,
        'profile_name' : '' ,
        'volume_desired' : 0 ,
        'isps' : '' ,
        'cgroupid' : 0 ,
        'gender' : '' ,
        'surl' : '' ,
        'zips' : '' ,
        'min_age' : 0 ,
        'max_age' : 0 ,
        'dfactor' : 0 ,
        'send_international' : 'Y'
    };

    self.saveListProfile = function () {
        var currentFormFields = {};

        switch ( self.profileType ) {
            case 'v1' :
                self.prepV1Fields();
                currentFormFields = self.v1Form;
            break;

            case 'v2' :
                self.prepV2Fields();
                currentFormFields = self.v2Form;
            break;

            case 'v3' :
                self.prepV3Fields();
                currentFormFields = self.v3Form;
            break;
        }

        ListProfileApiService.saveListProfile( currentFormFields , function ( response ) { $log.log( response ) } , function ( response ) { response } );
    };

    self.updateListProfile = function () {

    };

    self.prepV1Fields = function () {
        self.setDefaultFields( self.v1Form );
        self.setDateFields( self.v1Form );
        self.setCountRangeFields( self.v1Form , true ); 

        self.v1Form.DeliveryDays = self.current.deliveryDays;
        self.v1Form.seeds = self.seedList.map( function ( seed ) { return seed; } ).join( "\n" );
    };

    self.prepV2Fields = function () {
        self.v2Form.volume_desired = self.current.volume_desired;    
        self.setDefaultFields( self.v2Form );
        self.setDateFields( self.v2Form );
        self.setCountRangeFields( self.v2Form ); 
    };

    self.prepV3Fields = function () {
        self.v3Form.volume_desired = self.current.volume_desired;    
        self.setDefaultFields( self.v3Form );
    };

    self.setDefaultFields = function ( formObject ) {
        formObject.profile_name = self.current.profile_name;

        formObject.isps = self.ispChipList.map( function ( chip ) {
            return chip.id;
        } );

        formObject.cgroupid = self.current.cgroupid.id;

        if ( self.genderType === 'empty' ) {
            formObject.gender = 'Empty';
        } else if ( self.genderType === 'specific' ) {
            formObject.gender = self.current.gender;
        } else {
            formObject.gender = '';
        }

        formObject.surl = self.sourceList.map( function ( url ) { return url } ).join( "\n" );

        formObject.zips = self.zipList.map( function ( zip ) { return zip; } ).join( "\n" );

        formObject.min_age = self.rangeData.count.age.min;
        formObject.max_age = self.rangeData.count.age.max;
    };

    self.setDateFields = function ( formObject ) {
        if ( self.rangeData.date.openers.min ) formObject.ostart_date = moment( self.rangeData.date.openers.min ).format( 'YYYY-MM-DD' );
        if ( self.rangeData.date.openers.max ) formObject.oend_date = moment( self.rangeData.date.openers.max ).format( 'YYYY-MM-DD' );
        if ( self.rangeData.date.clickers.min ) formObject.cstart_date = moment( self.rangeData.date.clickers.min ).format( 'YYYY-MM-DD' );
        if ( self.rangeData.date.clickers.max ) formObject.cend_date = moment( self.rangeData.date.clickers.max ).format( 'YYYY-MM-DD' );
        if ( self.rangeData.date.deliverable.min ) formObject.dstart_date = moment( self.rangeData.date.deliverable.min ).format( 'YYYY-MM-DD' );
        if ( self.rangeData.date.deliverable.max ) formObject.dend_date = moment( self.rangeData.date.deliverable.max ).format( 'YYYY-MM-DD' );
        if ( self.rangeData.date.converters.min ) formObject.convert_start_date = moment( self.rangeData.date.converters.min ).format( 'YYYY-MM-DD' );
        if ( self.rangeData.date.converters.max ) formObject.convert_end_date = moment( self.rangeData.date.converters.max ).format( 'YYYY-MM-DD' );
    };

    self.setCountRangeFields = function ( formObject , setAll ) {
        angular.forEach( [ 1 , 2 , 3 ] , function ( value , key ) {
            if ( key > 0 && setAll !== true ) return;

            formObject[ 'ostart' + ( key > 0 ? key : '' ) ] = self.rangeData.count.openers[ key ].min;
            formObject[ 'oend' + ( key > 0 ? key : '' ) ] = self.rangeData.count.openers[ key ].max;
            formObject[ 'cstart' + ( key > 0 ? key : '' ) ] = self.rangeData.count.clickers[ key ].min;
            formObject[ 'cend' + ( key > 0 ? key : '' ) ]= self.rangeData.count.clickers[ key ].max;
            formObject[ 'dstart' + ( key > 0 ? key : '' ) ]= self.rangeData.count.deliverable[ key ].min;
            formObject[ 'dend' + ( key > 0 ? key : '' ) ] = self.rangeData.count.deliverable[ key ].max;
            formObject[ 'convert_start' + ( key > 0 ? key : '' ) ] = self.rangeData.count.converters[ key ].min;
            formObject[ 'convert_end' + ( key > 0 ? key : '' ) ] = self.rangeData.count.converters[ key ].max;
        } );
    }

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

    self.ispSearchText = '';
    $rootScope.selectedIsps = {};
    self.currentSelectedIsp = '';
    self.ispChipList = [];
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

    self.selectAllIsps = function ( checked ) {
        angular.forEach( self.ispList , function ( value , key ) {
            if ( checked === true ) {
                $rootScope.selectedIsps[ parseInt( value.id ) ] = value.name;
            } else {
                $rootScope.selectedIsps[ parseInt( value.id ) ] = false;
            }
        } );
    };

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
    self.rangeList = [];

    self.rangeData = {
        "count" : {
            "age" : { "label" : 'Age Range' , "min" : null , "max" : null , "filled" : false } ,
            "deliverable" : [
                { "label" : "Deliverable Range" ,  "min" : 0 , "max" : 0 , "filled" : false } ,
                { "label" : "Deliverable Range" , "min" : 0 , "max" : 0 , "filled" : false } ,
                { "label" : "Deliverable Range" , "min" : 0 , "max" : 0 , "filled" : false }
            ] ,
            "openers" : [
                { "label" : "Openers Range" , "min" : 0 , "max" : 0 , "filled" : false } ,
                { "label" : "Openers Range" , "min" : 0 , "max" : 0 , "filled" : false } ,
                { "label" : "Openers Range" , "min" : 0 , "max" : 0 , "filled" : false }
            ] ,
            "clickers" : [
                { "label" : "Clickers Range" , "min" : 0 , "max" : 0 , "filled" : false } ,
                { "label" : "Clickers Range" , "min" : 0 , "max" : 0 , "filled" : false } ,
                { "label" : "Clickers Range" , "min" : 0 , "max" : 0 , "filled" : false }
            ] ,
            "converters" : [
                { "label" : "Converters Range" , "min" : 0 , "max" : 0 , "filled" : false } ,
                { "label" : "Converters Range" , "min" : 0 , "max" : 0 , "filled" : false } ,
                { "label" : "Converters Range" , "min" : 0 , "max" : 0 , "filled" : false }
            ] ,
        } ,
        "date" : {
            "deliverable" : {
                "label" : "Deliverable Date Range" ,
                "min" : 0 ,
                "max" : 0,
                "filled" : false
            } ,
            "openers" : {
                "label" : "Opener Date Range" ,
                "min" : 0 ,
                "max" : 0 ,
                "filled" : false
            } ,
            "clickers" : {
                "label" : "Clickers Date Range" ,
                "min" : 0 ,
                "max" : 0 ,
                "filled" : false
            } ,
            "converters" : {
                "label" : "Converters Date Range" ,
                "min" : 0 ,
                "max" : 0 ,
                "filled" : false
            }
        }
    };

    self.addCountRange = function ( $event , type , subtype ) {
        var currentDialog = self.rangeDialogs[ type ][ subtype ];
        var parentEl = angular.element(document.body);
        var dialogOptions = {};

        if ( type === 'count' && subtype !== 'age' ) {
            var rangeList = self.rangeData[ type ][ subtype ];
            dialogOptions = self.rangeDialogs[ type ][ subtype ];
            dialogOptions.locals.label = null;
            dialogOptions.locals.number = null;

            angular.forEach( rangeList , function ( value , key ) {
                if ( 
                    value.filled === false
                    && dialogOptions.locals.label === null 
                ) {
                    dialogOptions.locals.label = value.label;
                    dialogOptions.locals.number = key;
                };
            } );
        } else {
            dialogOptions = self.rangeDialogs[ type ][ subtype ];
        }

        dialogOptions.parent = parentEl;
        dialogOptions.targetEvent = $event;

        $mdDialog.show( dialogOptions );
    }

    self.addDateRange = function ( $event , type ) {
        var parentEl = angular.element(document.body);
        var dialogOptions = self.rangeDialogs.date[ type ];

        dialogOptions.parent = parentEl;
        dialogOptions.targetEvent = $event;

        $mdDialog.show( dialogOptions );
    }

    self.filterRangeChips = function ( chip ) {
        if ( typeof( chip.type ) === 'undefined' ) return null;
        
        return;
    };

    self.removeRangeChip = function ( chip ) {
        var currentData = {};

        if ( typeof( chip.number ) != 'undefined' ) {
            currentData = self.rangeData[ chip.type ][ chip.subtype ][ chip.number ]; 

            currentData.number = null;
        } else {
            currentData = self.rangeData[ chip.type ][ chip.subtype ]; 
        }

        currentData.min = 0;
        currentData.max = 0;
        currentData.filled = false;
    };

    self.rangeDialogs = {
        "count" : {
            "age" : {
                "templateUrl" : "js/templates/listprofile-range-count-dialog.html" ,
                "clickOutsideToClose" : true ,
                "controller" : function () {
                    var ctrl = this;

                    ctrl.addThisRange = function () {
                        self.rangeData.count.age.min = ctrl.min;
                        self.rangeData.count.age.max = ctrl.max;
                        self.rangeData.count.age.filled = true;
                        self.rangeList.push( {
                            "type" : "count" ,
                            "subtype" : "age" ,
                            "min" : ctrl.min ,
                            "max" : ctrl.max
                        } );

                        $mdDialog.hide();
                    }
                } ,
                "controllerAs" : 'ctrl' ,
                "bindToController" : true ,
                "locals" : {
                    "label" : self.rangeData.count.age.label ,
                    "type" : "count" ,
                    "subtype" : "age" ,
                    "min" : null ,
                    "max" : null 
                }
            } ,
            "deliverable" : {
                "templateUrl" : "js/templates/listprofile-range-count-dialog.html" ,
                "clickOutsideToClose" : true ,
                "controller" : function () {
                    var ctrl = this;

                    ctrl.addThisRange = function () {
                        self.rangeData.count.deliverable[ ctrl.number ].min = ctrl.min;
                        self.rangeData.count.deliverable[ ctrl.number ].max = ctrl.max;
                        self.rangeData.count.deliverable[ ctrl.number ].filled = true;
                        self.rangeList.push( {
                            "type" : "count" ,
                            "subtype" : "deliverable" ,
                            "number" : ctrl.number ,
                            "min" : ctrl.min ,
                            "max" : ctrl.max
                        } );

                        $mdDialog.hide();
                    }
                } ,
                "bindToController" : true ,
                "controllerAs" : 'ctrl' ,
                "locals" : {
                    "label" : null ,
                    "type" : "count" ,
                    "subtype" : "deliverables" ,
                    "number" : null ,
                    "min" : null ,
                    "max" : null 
                }
            } ,
            "openers" : {
                "templateUrl" : "js/templates/listprofile-range-count-dialog.html" ,
                "clickOutsideToClose" : true ,
                "controller" : function () {
                    var ctrl = this;

                    ctrl.addThisRange = function () {
                        self.rangeData.count.openers[ ctrl.number ].min = ctrl.min;
                        self.rangeData.count.openers[ ctrl.number ].max = ctrl.max;
                        self.rangeData.count.openers[ ctrl.number ].filled = true;
                        self.rangeList.push( {
                            "type" : "count" ,
                            "subtype" : "openers" ,
                            "number" : ctrl.number ,
                            "min" : ctrl.min ,
                            "max" : ctrl.max
                        } );

                        $mdDialog.hide();
                    }
                } ,
                "controllerAs" : 'ctrl' ,
                "bindToController" : true ,
                "locals" : {
                    "label" : null ,
                    "type" : "count" ,
                    "subtype" : "openers" ,
                    "number" : null ,
                    "min" : null ,
                    "max" : null 
                }
            } ,
            "clickers" : {
                "templateUrl" : "js/templates/listprofile-range-count-dialog.html" ,
                "clickOutsideToClose" : true ,
                "controller" : function () {
                    var ctrl = this;

                    ctrl.addThisRange = function () {
                        self.rangeData.count.clickers[ ctrl.number ].min = ctrl.min;
                        self.rangeData.count.clickers[ ctrl.number ].max = ctrl.max;
                        self.rangeData.count.clickers[ ctrl.number ].filled = true;
                        self.rangeList.push( {
                            "type" : "count" ,
                            "subtype" : "clickers" ,
                            "number" : ctrl.number ,
                            "min" : ctrl.min ,
                            "max" : ctrl.max
                        } );

                        $mdDialog.hide();
                    }
                } ,
                "controllerAs" : 'ctrl' ,
                "bindToController" : true ,
                "locals" : {
                    "label" : null ,
                    "type" : "count" ,
                    "subtype" : "clickers" ,
                    "number" : null ,
                    "min" : null ,
                    "max" : null 
                }
            } ,
            "converters" : {
                "templateUrl" : "js/templates/listprofile-range-count-dialog.html" ,
                "clickOutsideToClose" : true ,
                "controller" : function () {
                    var ctrl = this;

                    ctrl.addThisRange = function () {
                        self.rangeData.count.converters[ ctrl.number ].min = ctrl.min;
                        self.rangeData.count.converters[ ctrl.number ].max = ctrl.max;
                        self.rangeData.count.converters[ ctrl.number ].filled = true;
                        self.rangeList.push( {
                            "type" : "count" ,
                            "subtype" : "converters" ,
                            "number" : ctrl.number ,
                            "min" : ctrl.min ,
                            "max" : ctrl.max
                        } );

                        $mdDialog.hide();
                    }
                } ,
                "controllerAs" : 'ctrl' ,
                "bindToController" : true ,
                "locals" : {
                    "label" : null ,
                    "type" : "count" ,
                    "subtype" : "converters" ,
                    "number" : null ,
                    "min" : null ,
                    "max" : null 
                }
            }
        } ,
        "date" : {
            "deliverable" : {
                "templateUrl" : "js/templates/listprofile-range-date-dialog.html" ,
                "clickOutsideToClose" : true ,
                "controller" : function () {
                    var ctrl = this;

                    ctrl.addThisRange = function () {
                        self.rangeData.date.deliverable.min = ctrl.min;
                        self.rangeData.date.deliverable.max = ctrl.max;
                        self.rangeData.date.deliverable.filled = true;
                        self.rangeList.push( {
                            "type" : "date" ,
                            "subtype" : "deliverable" ,
                            "min" : moment( ctrl.min ).format( 'YYYY-MM-DD' ) ,
                            "max" : moment( ctrl.max ).format( 'YYYY-MM-DD' )
                        } );

                        $mdDialog.hide();
                    }
                } ,
                "bindToController" : true ,
                "controllerAs" : 'ctrl' ,
                "locals" : {
                    "label" : self.rangeData.date.deliverable.label ,
                    "type" : "date" ,
                    "subtype" : "deliverables" ,
                    "min" : null ,
                    "max" : null 
                }
            } ,
            "openers" : {
                "templateUrl" : "js/templates/listprofile-range-date-dialog.html" ,
                "clickOutsideToClose" : true ,
                "controller" : function () {
                    var ctrl = this;

                    ctrl.addThisRange = function () {
                        self.rangeData.date.openers.min = ctrl.min;
                        self.rangeData.date.openers.max = ctrl.max;
                        self.rangeData.date.openers.filled = true;
                        self.rangeList.push( {
                            "type" : "date" ,
                            "subtype" : "openers" ,
                            "min" : moment( ctrl.min ).format( 'YYYY-MM-DD' ) ,
                            "max" : moment( ctrl.max ).format( 'YYYY-MM-DD' )
                        } );

                        $mdDialog.hide();
                    }
                } ,
                "bindToController" : true ,
                "controllerAs" : 'ctrl' ,
                "locals" : {
                    "label" : self.rangeData.date.openers.label ,
                    "type" : "date" ,
                    "subtype" : "openers" ,
                    "min" : null ,
                    "max" : null 
                }
            } ,
            "clickers" : {
                "templateUrl" : "js/templates/listprofile-range-date-dialog.html" ,
                "clickOutsideToClose" : true ,
                "controller" : function () {
                    var ctrl = this;

                    ctrl.addThisRange = function () {
                        self.rangeData.date.clickers.min = ctrl.min;
                        self.rangeData.date.clickers.max = ctrl.max;
                        self.rangeData.date.clickers.filled = true;
                        self.rangeList.push( {
                            "type" : "date" ,
                            "subtype" : "clickers" ,
                            "min" : moment( ctrl.min ).format( 'YYYY-MM-DD' ) ,
                            "max" : moment( ctrl.max ).format( 'YYYY-MM-DD' )
                        } );

                        $mdDialog.hide();
                    }
                } ,
                "bindToController" : true ,
                "controllerAs" : 'ctrl' ,
                "locals" : {
                    "label" : self.rangeData.date.clickers.label ,
                    "type" : "date" ,
                    "subtype" : "clickers" ,
                    "min" : null ,
                    "max" : null 
                }
            } ,
            "converters" : {
                "templateUrl" : "js/templates/listprofile-range-date-dialog.html" ,
                "clickOutsideToClose" : true ,
                "controller" : function () {
                    var ctrl = this;

                    ctrl.addThisRange = function () {
                        self.rangeData.date.converters.min = ctrl.min;
                        self.rangeData.date.converters.max = ctrl.max;
                        self.rangeData.date.converters.filled = true;
                        self.rangeList.push( {
                            "type" : "date" ,
                            "subtype" : "converters" ,
                            "min" : moment( ctrl.min ).format( 'YYYY-MM-DD' ) ,
                            "max" : moment( ctrl.max ).format( 'YYYY-MM-DD' )
                        } );

                        $mdDialog.hide();
                    }
                } ,
                "bindToController" : true ,
                "controllerAs" : 'ctrl' ,
                "locals" : {
                    "label" : self.rangeData.date.converters.label ,
                    "type" : "date" ,
                    "subtype" : "converters" ,
                    "min" : null ,
                    "max" : null 
                }
            }
        }
    };
} ] );
