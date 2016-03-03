mt2App.controller( 'ListProfileController' , [ '$rootScope' , '$log' , '$location' , '$window' , '$mdDialog' , 'ListProfileApiService' , 'ClientApiService' , 'ClientGroupApiService' , function ( $rootScope , $log , $location , $window , $mdDialog , ListProfileApiService , ClientApiService , ClientGroupApiService ) {
    var self = this;
    self.testUser = 217;
    self.showVersionField = true;

    self.profileList = [];
    self.createUrl = '/listprofile/create';
    self.creatingListProfile = false;
    self.updatingListProfile = false;

    self.loadListProfiles = function () {
        self.currentlyLoading = true;

        ListProfileApiService.getListProfiles(
            self.currentPage ,
            self.paginationCount ,
            self.loadListProfilesSuccessCallback ,
            self.loadListProfilesFailureCallback
        );
    };

    self.loadListProfilesSuccessCallback = function ( response ) {
        self.currentlyLoading = 0;

        self.profileList = response.data.data; 

        self.pageCount = response.data.last_page;
    };

    self.loadListProfilesFailureCallback = function ( response ) {
        $log.log( response );
    
    };

    self.loadClientGroups = function () {
        ClientGroupApiService.getAllClientGroups( self.loadClientGroupsSuccessCallback , self.loadClientGroupsFailureCallback );
    };

    self.loadClientGroupsSuccessCallback = function ( response ) {
        $log.log( response ); 

        angular.forEach( response.data , function ( clientGroup , key ) {
            self.clientGroupList.push( { "id" : clientGroup.id , "name" : clientGroup.name } );
        } );
    };

    self.loadClientGroupsFailureCallback = function ( response ) {
        $log.log( response ); 
    };

    self.loadListProfile = function () {
        var currentPath = $location.path();
        var pathParts = currentPath.match( new RegExp( /(\d+)/ ) );
        var prepopPage = (
            pathParts !== null
            && angular.isNumber( parseInt( pathParts[ 0 ] ) )
        );
        self.showVersionField = false;

        if ( prepopPage ) {
            self.current.pid = pathParts[ 0 ];

            ListProfileApiService.getListProfile(
                self.current.pid ,
                self.loadListProfileSuccessCallback ,
                self.loadListProfileFailureCallback
            );

            ListProfileApiService.getIspsByProfileId(
                self.current.pid ,
                self.loadIspsSuccessCallback ,
                self.loadIspsFailureCallback
            );

            ListProfileApiService.getSourcesByProfileId(
                self.current.pid ,
                self.loadSourcesSuccessCallback ,
                self.loadSourcesFailureCallback
            );

            ListProfileApiService.getSeedsByProfileId(
                self.current.pid ,
                self.loadSeedsSuccessCallback ,
                self.loadSeedsFailureCallback
            );

            ListProfileApiService.getZipsByProfileId(
                self.current.pid ,
                self.loadZipsSuccessCallback ,
                self.loadZipsFailureCallback
            );
        }
    };
    self.loadZipsSuccessCallback = function ( response ) {
        $log.log( response ); 

        angular.forEach( response.data , function ( zip , key ) {
            self.zipList.push( zip.zip );
        } );
    };

    self.loadZipsFailureCallback = function ( response ) {
        $log.log( response ); 
    
    };

    self.loadSeedsSuccessCallback = function ( response ) {
        $log.log( response );

        angular.forEach( response.data , function ( seed , key ) {
            self.seedList.push( seed.sid );
        } );
    };

    self.loadSeedsFailureCallback = function ( response ) {
        $log.log( response );

    };

    self.loadIspsSuccessCallback = function ( response ) {
        $log.log( response );

        angular.forEach( response.data , function ( isp , key ) {
            $rootScope.selectedIsps[ isp.id ] = isp.name;
        });
    };

    self.loadIspsFailureCallback = function  ( response ) {
        $log.log( response );
    };

    self.loadSourcesSuccessCallback = function ( response ) {
        $log.log( response );

        angular.forEach( response.data , function ( source , key ) {
            self.sourceList.push( source.source_url );
        } );
    };

    self.loadSourcesFailureCallback = function ( response ) {
        $log.log( response );
    };

    self.loadListProfileSuccessCallback = function ( response ) {
        self.prepopNormalFields( response );
        self.prepopGender( response );
        self.prepopAgeRange( response );
        self.prepopCountRanges( response );
        self.prepopDateRanges( response );
    };

    self.prepopNormalFields = function ( response ) {
        self.current.profile_name = response.data.profile_name;
        self.deliveryDays = response.data.DeliveryDays; 
    };

    self.prepopGender = function ( response ) {
        if ( response.data.gender == 'F' || response.data.gender == 'M' ) {
            self.genderType = 'specific';
            self.current.gender = response.data.gender;
        } else if ( response.data.gender == 'Empty' ) {
            self.genderType = 'empty';
        }
    }

    self.prepopAgeRange = function ( response ) {
        if ( response.data.min_age > 0 && response.data.max_age > 0 ) {
            self.rangeData.count.age.min = response.data.min_age;
            self.rangeData.count.age.max = response.data.max_age;
            self.rangeData.count.age.filled = true;

            self.rangeList.push( {
                "type" : "count" ,
                "subtype" : "age" ,
                "min" : response.data.min_age ,
                "max" : response.data.max_age
            } );
        }
    };

    self.prepopCountRanges = function ( response ) {
        var rangeTypes = [ 'deliverable' , 'opener' , 'clicker' , 'convert' ];
        var typeMap = { 'deliverable' : 'deliverable' , 'opener' : 'openers' , 'clicker' : 'clickers' , 'convert' : 'converters' };

        angular.forEach( rangeTypes , function ( type , index ) {
            var typeNumber = null;

            for ( var rangeNumber = 0 ; rangeNumber < 3 ; rangeNumber++ ) {
                typeNumber = ( rangeNumber > 0 ? rangeNumber : '' );

                if (
                    response.data[ type + '_end' + typeNumber ] > 0    
                ) {
                    self.rangeData.count[ typeMap[ type ] ][ rangeNumber ].min =
                        response.data[ type + '_start' + typeNumber ];
                    self.rangeData.count[ typeMap[ type ] ][ rangeNumber ].max =
                        response.data[ type + '_end' + typeNumber ];
                    self.rangeData.count[ typeMap[ type ] ][ rangeNumber ].filled = true;

                    self.rangeList.push( {
                        "type" : "count" ,
                        "subtype" : typeMap[ type ] ,
                        "min" : response.data[ type + '_start' + typeNumber ] ,
                        "max" : response.data[ type + '_end' + typeNumber ]
                    } );
                }
            }
        } );
    };

    self.prepopDateRanges = function ( response ) {
        var rangeTypes = [ 'deliverable' , 'opener' , 'clicker' , 'convert' ];
        var typeMap = { 'deliverable' : 'deliverable' , 'opener' : 'openers' , 'clicker' : 'clickers' , 'convert' : 'converters' };

        angular.forEach( rangeTypes , function ( type , index ) {
            if (
                response.data[ type + '_start_date' ] != '0000-00-00'
                && response.data[ type + '_end_date' ] != '0000-00-00'
            ) {
                self.rangeData.date[ typeMap[ type ] ].min = response.data[ type + '_start_date' ];
                self.rangeData.date[ typeMap[ type ] ].max = response.data[ type + '_end_date' ];
                self.rangeData.date[ typeMap[ type ] ].filled = true;

                self.rangeList.push( {
                    "type" : "date" ,
                    "subtype" : typeMap[ type ] ,
                    "min" : response.data[ type + '_start_date' ] ,
                    "max" : response.data[ type + '_end_date'  ]
                } );
            }
        } );
    };

    self.loadListProfileFailureCallback = function ( response ) {
        $log.log( response );
    };

    $rootScope.$on( 'updatePage' , function () {
        $( '.collapse' ).collapse( 'hide' );
        self.loadListProfiles();
    } );

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

    //Angular UI - Select UI
    self.clientGroupLoading = false;
    self.selectedClientGroup = {};
    self.currentClientGroupPage = 1;
    self.fetchClientGroups = function ( $select , $event ) {
        // no event means first load!
        if (!$event) {
            self.currentClientGroupPage = 1;
            self.clientGroupList = [];
        } else {
            $event.stopPropagation();
            $event.preventDefault();
            self.currentClientGroupPage++;
        }

        self.clientGroupLoading = true;

        $http( {
            "method" : "GET" ,
            "url" : '/api/clientgroup' ,
            "param" : '' 
        } ).then( function ( response ) {
                self.clientGroupList = self.clientGroupList.concat( response.data.data );
            } ,
            function () {

            }
        )[ 'finally' ](function () { self.clientGroupLoading = true; });
    };

    /**
     * ISP AutoComplete
     */

    self.ispSearchText = '';
    $rootScope.selectedIsps = {};
    self.currentSelectedIsp = '';
    self.ispChipList = [];
    self.ispList = [
        { "id" : 1 , "name" : "AOL" } ,
        { "id" : 2 , "name" : "Hotmail" } ,
        { "id" : 3 , "name" : "Yahoo" } , 
        { "id" : 4 , "name" : "Others" } , 
        { "id" : 6 , "name" : "Comcast" } , 
        { "id" : 13 , "name" : "ATT" } , 
        { "id" : 17 , "name" : "Gmail" } , 
        { "id" : 21 , "name" : "Cloudmark" } , 
        { "id" : 45 , "name" : "safeothers" } , 
        { "id" : 47 , "name" : "UK" } , 
        { "id" : 52 , "name" : "GMX" } , 
        { "id" : 53 , "name" : "German" } , 
        { "id" : 54 , "name" : "ForeignYahoo" } , 
        { "id" : 57 , "name" : "France" } , 
        { "id" : 60 , "name" : "YahooOthers" } , 
        { "id" : 65 , "name" : "AOLUK" } , 
        { "id" : 66 , "name" : "AOLOthers" } , 
        { "id" : 67 , "name" : "ForeignAOL" } , 
        { "id" : 68 , "name" : "GmailOthers" } , 
        { "id" : 69 , "name" : "YahooUK" } , 
        { "id" : 70 , "name" : "HotmailUK" } , 
        { "id" : 71 , "name" : "ForeignHotmail" } , 
        { "id" : 72 , "name" : "HotmailOthers" } , 
        { "id" : 73 , "name" : "Facebook" } , 
        { "id" : 74 , "name" : "Apple" } , 
        { "id" : 75 , "name" : "Cable_Broadband" } , 
        { "id" : 76 , "name" : "Italy" } , 
        { "id" : 77 , "name" : "VerizonF" } , 
        { "id" : 78 , "name" : "CoxF" } , 
        { "id" : 79 , "name" : "BTINTERNET" } , 
        { "id" : 80 , "name" : "Wanadoo" } 
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
