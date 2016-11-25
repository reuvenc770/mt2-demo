mt2App.controller( 'ListProfileController' , [ 'ListProfileApiService'  , '$mdDialog' , '$timeout' , 'formValidationService' , 'modalService' , 'paginationService' , '$location' , '$window' , '$log' , function ( ListProfileApiService , $mdDialog , $timeout , formValidationService , modalService , paginationService , $location , $window , $log ) {
    var self = this;

    self.nameDisabled = true;
    self.customName = false;

    self.enableAdmiral = false;
    self.showAttrFilters = false;
    self.search = {};
    self.enabledSuppression = { "list" : false , "offer" : false };

    self.selectedProfiles = [];
    self.showCombine = false;
    self.listCombines = [];
    self.combineError = null;
    self.combineName = "";

    self.current = {
        'profile_id' : null ,
        'name' : '' ,
        'countries' : {} ,
        'feeds' : {} ,
        'feedGroups' :{} ,
        'isps' : {} ,
        'categories' : {} ,
        'offers' : [] ,
        'suppression' : {
            'global' : { 1 : "Orange Global" } ,
            'list' : {} ,
            'offer' : {} ,
            'attribute' : { 'cities': [] , 'zips' : [] , 'states' : {} }
        },
        'actionRanges' : {
            'deliverable' : { 'min' : 0 , 'max' : 0 },
            'opener' : { 'min' : 0 , 'max' : 0 , 'multiaction' : 1 },
            'clicker' : { 'min' : 0 , 'max' : 0 , 'multiaction' : 1 },
            'converter' : { 'min' : 0 , 'max' : 0 , 'multiaction' : 1 }
        },
        'attributeFilters' : {
            'age' : { 'min' : 0 , 'max' : 0 , 'unknown' : false },
            'genders' : {},
            'zips' : [],
            'cities' : [],
            'states' : {},
            'deviceTypes' : {},
            'os' : {} ,
            'mobileCarriers' : {}
        },
        'impressionwise' : false ,
        'tower' : {
            'run' : false ,
            'cleanseMonth' : null ,
            'cleanseYear' : null
        } ,
        'selectedColumns' : [] ,
        'includeCsvHeader' : false ,
        'exportOptions' : { 'interval' : [] , 'dayOfWeek' : '' , 'dayOfMonth' : '' } ,
        'admiralsOnly' : false
    };

    self.currentCombine = { 'combineName' : '' , 'selectedProfiles' : [] };
    self.prepopListProfiles = [];
    self.listProfilesList = [];
    self.lpListNameField = 'name';

    self.listProfiles = [];
    self.pageCount = 0;
    self.paginationCount = paginationService.getDefaultPaginationCount();
    self.paginationOptions = paginationService.getDefaultPaginationOptions();
    self.currentPage = 1;
    self.profileTotal = 0;
    self.queryPromise = null;
    self.sort = "name";

    self.countryCodeMap = { 1 : 'US' , 235 : 'UK' };
    self.countryNameMap = { 'United States' : 1 , 'United Kingdom' : 235 };
    self.genderNameMap = { 'Male' : 'M' , 'Female' : 'F' , 'Unknown' : 'U' };

    self.highlightedFeeds = [];
    self.highlightedFeedsForRemoval = [];
    self.feedClientFilters = [];
    self.clientFeedMap = {};
    self.feedNameMap = {};
    self.feedVisibility = {};

    self.highlightedFeedGroup = [];
    self.highlightedFeedGroupForRemoval = [];
    self.feedGroupVisibility = {};
    self.feedGroupNameMap = {};


    self.highlightedIsps = [];
    self.highlightedIspsForRemoval = [];
    self.ispVisibility = {};
    self.ispNameMap = {};

    self.highlightedCategories = [];
    self.highlightedCategoriesForRemoval = [];
    self.categoryVisibility = [];
    self.categoryNameMap = {};

    self.highlightedOffers = [];
    self.highlightedOffersForRemoval = [];
    self.offerVisibility = {};
    self.offerNameMap = {};

    self.highlightedStateFilters = [];
    self.highlightedStateFiltersForRemoval = [];
    self.stateFilterVisibility = {};
    self.stateFilterNameMap = {};

    self.highlightedDeviceTypeFilters = [];
    self.highlightedDeviceTypeFiltersForRemoval = [];
    self.deviceTypeFilterVisibility = { 'mobile' : true , 'desktop' : true , 'unknown' : true };
    self.deviceTypeFilterNameMap = { 'mobile' : 'Mobile' , 'desktop' : 'Desktop' , 'unknown' : "Unknown" };

    self.highlightedOsFilters = [];
    self.highlightedOsFiltersForRemoval = [];
    self.osFilterVisibility = { 'android' : true , 'ios' : true , 'macosx' : true , 'rim' : true , 'windows' : true , 'linux' : true , 'other' : true };
    self.osFilterNameMap = { 'android' : 'Android' , 'ios' : 'iOS' , 'macosx' : 'Mac OS X' , 'rim' : 'Rim OS' , 'windows' : 'Windows' , 'linux' : 'Linux' , 'other' : 'Other' };

    self.highlightedCarrierFilters = [];
    self.highlightedCarrierFiltersForRemoval = [];
    self.carrierFilterVisibility = { 'att' : true , 'sprint' : true , 'tmobile' : true , 'verizon' : true };
    self.carrierFilterNameMap = { 'att' : 'AT&T' , 'sprint' : 'Sprint' , 'tmobile' : 'T-Mobile' , 'verizon' : 'Verizon' };

    self.highlightedGlobalSupp = [];
    self.highlightedGlobalSuppForRemoval = [];
    self.globalSuppVisibility = { 1 : false , 2 : true , 3 : true , 4 : true };
    self.globalSuppNameMap = { 1 : 'Orange Global' , 2 : 'Blue Global' , 3 : 'Green Global' , 4 : 'Gold Global' };

    self.highlightedListSupp = [];
    self.highlightedListSuppForRemoval = [];
    self.listSuppVisibility = { 1 : true , 2 : true , 3 : true , 4 : true };
    self.listSuppNameMap = { 1 : 'Sprint Yahoo' , 2 : 'Verizon Gmail' , 3 : 'Trendr Hotmail' , 4 : 'RMP Hotmail' };

    self.highlightedOfferSupp = [];
    self.highlightedOfferSuppForRemoval = [];
    self.offerSuppVisibility = {};
    self.offerSuppNameMap = {};

    self.highlightedStateSupp = [];
    self.highlightedStateSuppForRemoval = [];
    self.stateSuppVisibility = {};
    self.stateSuppNameMap = {};

    self.columnList = [
        { 'header' : 'email_id' , 'label' : 'Email ID' },
        { 'header' : 'first_name' , 'label' : 'First Name' },
        { 'header' : 'last_name' , 'label' : 'Last Name' },
        { 'header' : 'address' , 'label' : 'Address' },
        { 'header' : 'address2' , 'label' : 'Address 2'},
        { 'header' : 'city' , 'label' : 'City' },
        { 'header' : 'state' , 'label' : 'State' },
        { 'header' : 'zip' , 'label' : 'Zip' },
        { 'header' : 'country' , 'label' : 'Country' },
        { 'header' : 'gender' , 'label' : 'Gender' },
        { 'header' : 'ip' , 'label' : 'IP Address' },
        { 'header' : 'phone' , 'label' : 'Phone Number' },
        { 'header' : 'source_url' , 'label' : 'Source URL' },
        { 'header' : 'age' , 'label' : 'Age' },
        { 'header' : 'device_type' , 'label' : 'Device Type' },
        { 'header' : 'device_name' , 'label' : 'Device Name' },
        { 'header' : 'carrier' , 'label' : 'Carrier' },
        { 'header' : 'capture_date' , 'label' : 'Capture Date' },
        { 'header' : 'esp_account' , 'label' : 'ESP Account' },
        { 'header' : 'email_address' , 'label' : 'Email Address' } ,
        { 'header' : 'lower_md5' , 'label' : 'Lowercase MD5' },
        { 'header' : 'upper_md5' , 'label' : 'Uppercase MD5' } ,
        { 'header' : 'domain_group_id' , 'label' : "ISP" } ,
        { 'header' : 'dob' , 'label' : "Date of Birth" } ,
        { 'header' : 'feed_id' , 'label' : "Feed ID" } ,
        { 'header' : 'feed_name' , 'label' : "Feed Name" } ,
        { 'header' : 'client_name' , 'label' : "Client" } ,
        { 'header' : 'subscribe_date' , 'label' : 'Subscribe Date' } ,
        { 'header' : 'status' , 'label' : 'Status' } ,
        { 'header' : 'tower_date' , 'label' : 'Tower Date' }
    ];
    self.selectedColumns = [];
    self.availableWidgetTitle = "Available Columns";
    self.chosenWidgetTitle = "Selected Columns";
    self.columnLabelField = 'label';
    self.columnHeaderField = 'header';

    self.rangesForName = [];
    self.rangeCodeMap = {
        'deliverable' : 'D' ,
        'opener' : 'O' ,
        'clicker' : 'C' ,
        'converter' : 'CV'
    };

    self.towerDateOptions = [];

    self.formErrors = [];

    self.loadListProfiles = function () {
        self.queryPromise = ListProfileApiService.getListProfiles(
            self.currentPage ,
            self.paginationCount ,
            self.loadListProfilesSuccessCallback ,
            self.loadListProfilesFailureCallback
        );
        self.loadListCombines();
    };

    self.loadListProfilesSuccessCallback = function ( response ) {
        self.listProfiles = response.data.data;
        self.pageCount = response.data.last_page;
        self.profileTotal = response.data.total;

        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );
    };

    self.loadListProfilesFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load list profiles.' );

        modalService.launchModal();
    };

    self.prepop = function ( listProfile ) {
        self.current = listProfile;
        self.generateName();

        self.fixEmptyFields();

        $(function () { $('[data-toggle="tooltip"]').tooltip() });
    };

    self.fixEmptyFields = function () {
        angular.forEach( [ 'countries' , 'feeds' , 'isps' , 'categories' ] , function ( value , index ) {
            if ( self.current[ value ].length == 0 ) {
                self.current[ value ] = {};
            }
        } );

        angular.forEach( [ 'genders' , 'states' , 'deviceTypes' , 'os' , 'mobileCarriers' ] , function ( value , index ) {
            if ( self.current.attributeFilters[ value ].length == 0 ) {
                self.current.attributeFilters[ value ] = {};
            }
        } );
    };

    self.generateTowerDateOptions = function () {
        var lastYear = moment().subtract( 1 , 'years' ).year();
        self.towerDateOptions.push( { "value" : lastYear , "name" : lastYear } );

        var thisYear = moment().year();
        self.towerDateOptions.push( { "value" : thisYear , "name" : thisYear } );
    };

    self.generateName = function () {
        if ( self.customName ) {
            return true;
        }

        var nameParts = [];
        nameParts.push( self.getFormattedName( self.current.feedGroups ) );
        nameParts.push( self.getFormattedName( self.current.feeds ) );
        nameParts.push( self.getFormattedName( self.current.isps ) );
        nameParts.push( self.getFormattedName( self.current.countries , self.countryCodeMap ) );
        nameParts.push( self.getFormattedRangeName() );

        self.current.name = nameParts.join( '_' );
    };

    self.getFormattedName = function ( list , map ) {
        var names = [];

        angular.forEach( list , function ( current ) {
            if ( typeof( map ) !== 'undefined' && Object.keys( map ).length > 0 && typeof( map[ current ] ) !== 'undefined' ) {
                names.push( map[ current ].trim() );
            } else {
                names.push( current.trim() );
            }
        } );

        return names.join( '|' );
    };

    self.getFormattedRangeName = function () {
        var rangeNames = {};
        angular.forEach( self.current.actionRanges , function ( currentRange , rangeType ) {
            if ( currentRange.max > 0 ) {
                var currentRangeValue = currentRange.max;

                if ( currentRange.min > 0 ) {
                    currentRangeValue = currentRange.min + 'to' + currentRangeValue;
                }

                if ( typeof( rangeNames[ currentRangeValue ] ) == 'undefined' ) {
                    rangeNames[ currentRangeValue ] = [];
                }

                rangeNames[ currentRangeValue ].push( self.rangeCodeMap[ rangeType ] );
            }
        } );

        var fullRangeName = '';
        angular.forEach( rangeNames , function ( rangeTypes , rangeValue ) {
            fullRangeName = fullRangeName + rangeValue + rangeTypes.join( '' );
        } )

        return fullRangeName;
    };

    self.toggleEditName = function ( ev , setDefaultName ) {
        if ( setDefaultName ) {
            self.nameDisabled = true;
            self.customName = false;
            self.generateName();
        } else {
            var confirm = $mdDialog.confirm()
                .title( 'Are you sure you want to edit this field?' )
                .ariaLabel( 'Are you sure you want to edit this field?' )
                .targetEvent( ev )
                .ok( 'Yes I Am Sure' )
                .cancel( 'No, Leave Default Name' );

            $mdDialog.show( confirm ).then( function() {
                self.nameDisabled = false;
                self.customName = true;
            } );
        }
    };

    self.confirmMaxDateRange = function ( ev , maxRangeValue ) {
        if ( maxRangeValue.max > 90 && maxRangeValue.allowLargeValue !== true ) {
            var confirm = $mdDialog.confirm()
                            .title( 'Are you sure you want to set this to more than 90 days?' )
                            .ariaLabel( 'Max Date Range Warning' )
                            .targetEvent( ev )
                            .ok( 'Yes I am Sure' )
                            .cancel( 'No, Set to Max Value' );

            $mdDialog.show( confirm ).then(
                function () {
                    maxRangeValue.allowLargeValue = true;
                } ,
                function () {
                    maxRangeValue.max = 90;
                }
            );
        }
    };

    self.sanitizeMultiAction = function ( range ) {
        if ( typeof( range.multiaction ) ==='undefined'  ) {
            range.multiaction = 1;
        }
    };

    self.sanitizeGlobalSupp = function () {
        if ( typeof( self.current.suppression.global ) == 'undefined' ) {
            self.current.suppression.global = [ "1" ];
        }
    };

    self.confirmSuppressionConfig = function ( ev , type ) {
        if ( !self.enabledSuppression[ type ] ) {
            var confirm = $mdDialog.confirm()
                            .title( 'Are you sure you?' )
                            .ariaLabel( 'Suppression Warning' )
                            .targetEvent( ev )
                            .ok( 'Yes' )
                            .cancel( 'Cancel' );

            $mdDialog.show( confirm ).then(
                function () {
                    self.enabledSuppression[ type ] = true;
                } ,
                function () {
                    self.current.suppression[ type ] = [];
                }
            );
        }
    };

    self.addMembershipItems = function ( container , context , callback ) {
        angular.forEach( container.highlighted , function ( id ) {
            container.visibility[ id ] = false;

            context[ id ] = container.map[ id ];
        } );

        container.highlighted = [];

        if ( typeof( callback ) !== 'undefined' ) {
            callback();
        }
    }

    self.removeMembershipItems = function ( container , context , callback ) {
        angular.forEach( container.highlightedForRemoval , function ( id ) {
            container.visibility[ id ] = true;

            delete( context[ id ] );
        } );

        container.highlightedForRemoval = [];

        if ( typeof( callback ) !== 'undefined' ) {
            callback();
        }
    }

    self.addFeeds = function () {
        self.addMembershipItems(
            { "highlighted" : self.highlightedFeeds , "visibility" : self.feedVisibility , "map" : self.feedNameMap } ,
            self.current.feeds ,
            self.generateName
        );
    };

    self.removeFeeds = function () {
        self.removeMembershipItems(
            { "highlightedForRemoval" : self.highlightedFeedsForRemoval , "visibility" : self.feedVisibility } ,
            self.current.feeds ,
            function () {
                self.updateFeedVisibility();
                self.generateName();
            }
        );
    };

    self.addFeedGroups = function () {
        self.addMembershipItems(
            { "highlighted" : self.highlightedFeedGroup , "visibility" : self.feedGroupVisibility , "map" : self.feedGroupNameMap } ,
            self.current.feedGroups ,
            self.generateName
        );
    };

    self.removeFeedGroups = function () {
        self.removeMembershipItems(
            { "highlightedForRemoval" : self.highlightedFeedGroupForRemoval , "visibility" : self.feedGroupVisibility } ,
            self.current.feedGroups ,
            function () {
                self.generateName();
            }
        );
    };

    self.updateFeedVisibility = function () {
        var showAll = false;
        var noClientFiltersSelected = self.feedClientFilters.length <= 0;

        if ( noClientFiltersSelected ) {
            showAll = true;
        }

        angular.forEach( self.feedVisibility , function ( visibility , feedId ) {
            var feedNotSelected = typeof( self.current.feeds[ parseInt( feedId ) ] ) == 'undefined';

            if ( showAll && feedNotSelected ) {
                self.feedVisibility[ feedId ] = true;
            } else {
                self.feedVisibility[ feedId ] = false;

                angular.forEach( self.feedClientFilters , function ( clientId ) {
                    var feedListExistsAndBelongsToClient = ( typeof( self.clientFeedMap[ parseInt( clientId ) ] ) != 'undefined' && self.clientFeedMap[ parseInt( clientId ) ].indexOf( parseInt( feedId ) ) !== -1 );

                    if( feedListExistsAndBelongsToClient && feedNotSelected ) {
                        self.feedVisibility[ feedId ] = true;
                    }
                } );
            }
        } );
    };

    self.clearClientFeedFilter = function () {
        self.feedClientFilters = [];

        self.updateFeedVisibility();
    };


    self.search.populateOffers = function (){
        if(self.search.offer.length >= 3){
            ListProfileApiService.searchOffers(self.search.offer,function(response){
                self.search.offerResults = response.data;
            },function(){});
        }

    };

    self.addIsps = function () {
        self.addMembershipItems(
            { "highlighted" : self.highlightedIsps , "visibility" : self.ispVisibility , "map" : self.ispNameMap } ,
            self.current.isps ,
            self.generateName
        );
    };

    self.removeIsps = function () {
        self.removeMembershipItems(
            { "highlightedForRemoval" : self.highlightedIspsForRemoval , "visibility" : self.ispVisibility } ,
            self.current.isps ,
            self.generateName
        );
    };

    self.addCategories = function () {
        self.addMembershipItems(
            { "highlighted" : self.highlightedCategories , "visibility" : self.categoryVisibility , "map" : self.categoryNameMap } ,
            self.current.categories
        );
    };

    self.removeCategories = function () {
        self.removeMembershipItems(
            { "highlightedForRemoval" : self.highlightedCategoriesForRemoval , "visibility" : self.categoryVisibility } ,
            self.current.categories
        );
    };

    self.addOffers = function () {
        angular.forEach( self.highlightedOffers , function ( value , key ) {
            self.current.offers.push( value );
            var index = self.search.offerResults.indexOf( value );

            if ( index >= 0 ) {
                self.search.offerResults.splice( index , 1 );
            }
        } );
    };

    self.removeOffers = function () {
        angular.forEach( self.highlightedOffersForRemoval , function ( selectedValue , selectedKey ) {
            if ( typeof( self.search.offerResults ) !== 'undefined' && self.search.offerResults.length > 0 ) {
                self.search.offerResults.push( selectedValue );
            }

            var index = self.current.offers.indexOf( selectedValue );

            if ( index >= 0 ) {
                self.current.offers.splice( index , 1 );
            }
        } );

    };

    self.addStateFilters = function () {
        self.addMembershipItems(
            { "highlighted" : self.highlightedStateFilters , "visibility" : self.stateFilterVisibility , "map" : self.stateFilterNameMap } ,
            self.current.attributeFilters.states
        );
    };

    self.removeStateFilters = function () {
        self.removeMembershipItems(
            { "highlightedForRemoval" : self.highlightedStateFiltersForRemoval , "visibility" : self.stateFilterVisibility } ,
            self.current.attributeFilters.states
        );
    };

    self.addDeviceTypeFilters = function () {
        self.addMembershipItems(
            { "highlighted" : self.highlightedDeviceTypeFilters , "visibility" : self.deviceTypeFilterVisibility , "map" : self.deviceTypeFilterNameMap } ,
            self.current.attributeFilters.deviceTypes
        );
    };

    self.removeDeviceTypeFilters = function () {
        self.removeMembershipItems(
            { "highlightedForRemoval" : self.highlightedDeviceTypeFiltersForRemoval , "visibility" : self.deviceTypeFilterVisibility } ,
            self.current.attributeFilters.deviceTypes
        );
    };

    self.addOsFilters = function () {
        self.addMembershipItems(
            { "highlighted" : self.highlightedOsFilters , "visibility" : self.osFilterVisibility , "map" : self.osFilterNameMap } ,
            self.current.attributeFilters.os
        );
    };

    self.removeOsFilters = function () {
        self.removeMembershipItems(
            { "highlightedForRemoval" : self.highlightedOsFiltersForRemoval , "visibility" : self.osFilterVisibility } ,
            self.current.attributeFilters.os
        );
    };

    self.addCarrierFilters = function () {
        self.addMembershipItems(
            { "highlighted" : self.highlightedCarrierFilters , "visibility" : self.carrierFilterVisibility , "map" : self.carrierFilterNameMap } ,
            self.current.attributeFilters.mobileCarriers
        );
    };

    self.removeCarrierFilters = function () {
        self.removeMembershipItems(
            { "highlightedForRemoval" : self.highlightedCarrierFiltersForRemoval , "visibility" : self.carrierFilterVisibility } ,
            self.current.attributeFilters.mobileCarriers
        );
    };

    self.addGlobalSupp = function () {
        self.addMembershipItems(
            { "highlighted" : self.highlightedGlobalSupp , "visibility" : self.globalSuppVisibility , "map" : self.globalSuppNameMap } ,
            self.current.suppression.global
        );
    };

    self.removeGlobalSupp = function () {
        self.removeMembershipItems(
            { "highlightedForRemoval" : self.highlightedGlobalSuppForRemoval , "visibility" : self.globalSuppVisibility } ,
            self.current.suppression.global
        );
    };

    self.addListSupp = function ( $event ) {
        if ( self.enabledSuppression[ 'list' ] ) {
            self.addMembershipItems(
                { "highlighted" : self.highlightedListSupp , "visibility" : self.listSuppVisibility , "map" : self.listSuppNameMap } ,
                self.current.suppression.list
            );

            return true;
        }

        self.confirmSuppressionConfig( $event , 'list' );
    };

    self.removeListSupp = function () {
        self.removeMembershipItems(
            { "highlightedForRemoval" : self.highlightedListSuppForRemoval , "visibility" : self.listSuppVisibility } ,
            self.current.suppression.list
        );
    };

    self.addOfferSupp = function ( $event ) {
        if ( self.enabledSuppression[ 'offer' ] ) {
            self.addMembershipItems(
                { "highlighted" : self.highlightedOfferSupp , "visibility" : self.offerSuppVisibility , "map" : self.offerSuppNameMap } ,
                self.current.suppression.offer
            );

            return true;
        }

        self.confirmSuppressionConfig( $event , 'offer' );
    };

    self.removeOfferSupp = function () {
        self.removeMembershipItems(
            { "highlightedForRemoval" : self.highlightedOfferSuppForRemoval , "visibility" : self.offerSuppVisibility } ,
            self.current.suppression.offer
        );
    };

    self.addStateSupp = function ( $event ) {
        self.addMembershipItems(
            { "highlighted" : self.highlightedStateSupp , "visibility" : self.stateSuppVisibility , "map" : self.stateSuppNameMap } ,
            self.current.suppression.attribute.states
        );
    };

    self.removeStateSupp = function () {
        self.removeMembershipItems(
            { "highlightedForRemoval" : self.highlightedStateSuppForRemoval , "visibility" : self.stateSuppVisibility } ,
            self.current.suppression.attribute.states
        );
    };

    self.toggleSelection = function ( list , nameMap , name , callback ) {
        if ( typeof( list[ name ] ) !== 'undefined' ) {
            delete( list[ name ] );
        } else {
            list[ name ] = nameMap[ name ];
        }

        if ( typeof( callback ) != 'undefined' ) {
            callback();
        }
    };

    self.columnMembershipCallback = function (){
        var columnList = [];
        angular.forEach( self.selectedColumns , function ( column , columnIndex ) {
            columnList.push( column[ self.columnHeaderField ] );
        } );
        self.current.selectedColumns = columnList;
    };

    self.admiralToggleFix = function () {
        if ( !self.enableAdmiral ) { //value is not true till after this is called
            $timeout( function () { window.scrollTo( 0 , ( document.body.scrollHeight + 300 ) ); } , 1 );
        }
    };

    self.toggleExportOption = function ( option ) {
        var optionIndex = self.current.exportOptions.interval.indexOf( option );

        if ( optionIndex < 0 ) {
            var immediatelyExists = ( self.current.exportOptions.interval.indexOf( 'Immediately' ) >= 0 );
            var itemsChosen = self.current.exportOptions.interval.length;

            if (
                ( immediatelyExists && itemsChosen === 1 )
                || ( !immediatelyExists && option == 'Immediately' && itemsChosen === 1 )
                || ( itemsChosen === 0 ) ) {
                self.current.exportOptions.interval.push( option );
            }
        } else {
            self.current.exportOptions.interval.splice( optionIndex , 1 );
            self.current.exportOptions.dayOfWeek = null;
            self.current.exportOptions.dayOfMonth = null;
        }
    };

    self.isSelectedExportOption = function ( option ) {
        return self.current.exportOptions.interval.indexOf( option ) >= 0;
    }

    self.saveListProfile = function () {
        ListProfileApiService.saveListProfile( self.current , self.SuccessCallBackRedirect , self.failureCallback );
    };

    self.updateListProfile = function () {
        ListProfileApiService.updateListProfile( self.current , self.SuccessCallBackRedirect , self.failureCallback );
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/listprofile' );
        $window.location.href = '/listprofile';
    };

    self.failureCallback = function ( response ) {
        formValidationService.loadFieldErrors( self , response );
    };

    self.nameCombine = function (){
        $('#createCombine').modal('show');
    };

    self.createCombine = function (){
        ListProfileApiService.createCombine(self.combineName,self.selectedProfiles, self.createCombineSuccess, self.createCombineFail);
    };

    self.updateCombine = function () {
        ListProfileApiService.updateCombine( self.currentCombine , self.SuccessCallBackRedirect , self.updateCombineFail );
    };

    self.toggleRow = function (selectedValue) {
        var index = self.selectedProfiles.indexOf(selectedValue);
        if (index >= 0) {
            self.selectedProfiles.splice(index, 1);
        } else {
            self.selectedProfiles.push(selectedValue);
        }
        self.showCombine = self.selectedProfiles.length > 1;
    };

    self.isCreatingCombine = function( profile ) {
        return self.selectedProfiles.indexOf( profile ) > -1;
    };

    self.loadListProfileList = function () {
        ListProfileApiService.getAllListProfiles( self.getAllListProfilesSuccess, self.getAllListProfilesFail )
    };

    self.getAllListProfilesSuccess = function ( response ) {

        self.listProfilesList = response.data;

        if ( self.prepopListProfiles.length > 0 ) {
            var profilesToRemove = [];

            angular.forEach( self.listProfilesList , function ( value , index ) {

                if (self.prepopListProfiles.indexOf( value.id) >= 0 ) {
                    profilesToRemove.push( value );
                    self.currentCombine.selectedProfiles.push( value );
                }
            });

            angular.forEach( profilesToRemove , function ( value , index ) {
                self.listProfilesList.splice( self.listProfilesList.indexOf( value ) , 1 );
            });
        }

    };

    self.getAllListProfilesFail = function ( response ) {
        modalService.simpleToast("List of list profiles failed to load.",'top right');
    };

    self.setCombine = function ( combineId , combineName , listProfiles ) {
        self.currentCombine.id = combineId;
        self.currentCombine.combineName = combineName;
        self.prepopListProfiles = listProfiles;
    };

    self.loadListCombines = function (){
        ListProfileApiService.getCombines(self.loadCombinesSuccess,self.loadCombineFail);
    };

    self.loadCombinesSuccess = function (response){
        self.listCombines = response.data;
    };

    self.createCombineSuccess = function (response){
        $('#createCombine').modal('hide');
        modalService.simpleToast("List Combine was Created",'top right');

        self.selectedProfiles = [];
        self.showCombine = false;
        formValidationService.resetFieldErrors( self );

        self.loadListCombines();
        self.combineName = "";
    };

    self.loadCombineFail = function (response) {
        modalService.simpleToast("List Combine failed to load",'top right');

    };

    self.createCombineFail = function ( response) {
        formValidationService.loadFieldErrors( self, response );
    };

    self.updateCombineFail = function ( response ) {
        formValidationService.loadFieldErrors( self, response );
    };

    self.exportCombine = function (id){
       ListProfileApiService.exportCombine(id,self.exportCombineSuccess, self.exportCombineFail)
    };

    self.exportCombineSuccess = function (response){
        modalService.simpleToast("List Combine Export has started",'top right');
    };

    self.exportCombineFail = function (response){
        modalService.simpleToast("List Combine failed to export",'top right');
    };
} ] );
