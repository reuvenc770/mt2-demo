mt2App.controller( 'ListProfileController' , [ 'ListProfileApiService'  , 'FeedGroupApiService' , 'FeedApiService' , '$mdDialog' , '$timeout' , 'formValidationService' , 'modalService' , 'paginationService' , '$location' , '$window' , '$compile' , '$log' , function ( ListProfileApiService , FeedGroupApiService , FeedApiService , $mdDialog , $timeout , formValidationService , modalService , paginationService , $location , $window , $compile , $log ) {
    var self = this;

    self.nameDisabled = true;
    self.customName = false;
    self.enableAlert = true;

    self.enableAdmiral = false;
    self.showAttrFilters = false;
    self.search = {};
    self.enabledSuppression = { "list" : false , "offer" : false };

    self.selectedProfiles = [];
    self.showCombine = false;
    self.listCombines = [];
    self.combineError = null;
    self.ftpFolderError = null;
    self.combineName = "";
    self.ftpFolder = "lp_combine";
    self.combineParty = '';

    self.current = {
        'profile_id' : null ,
        'name' : '' ,
        'ftp_folder': 'lp',
        'country_id' : '' ,
        'party' : '3',
        'feeds' : {} ,
        'feedGroups' :{} ,
        'feedClients' :{} ,
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
        'selectedColumns' : [ { 'header' : 'email_id' , 'label' : 'Email ID' }, { 'header' : 'email_address' , 'label' : 'Email Address' } ] ,
        'includeCsvHeader' : false ,
        'exportOptions' : { 'interval' : [] , 'dayOfWeek' : '' , 'dayOfMonth' : '' } ,
        'admiralsOnly' : false
    };

    self.currentCombine = { 'combineName' : '' , 'ftpFolder' : '' , 'selectedProfiles' : [] };
    self.prepopListProfiles = [];
    self.listProfilesList = [];
    self.lpListNameField = 'name';

    self.firstPartyListProfiles = [];
    self.secondPartyListProfiles = [];
    self.thirdPartyListProfiles = [];
    self.pageCount = 0;
    self.paginationCount = paginationService.getDefaultPaginationCount();
    self.paginationOptions = paginationService.getDefaultPaginationOptions();
    self.currentPage = 1;
    self.thirdPartyCurrentPage = 1;
    self.firstPartyCurrentPage = 1;
    self.profileTotal = 0;
    self.firstPartyProfileTotal = 0;
    self.secondPartyProfileTotal = 0;
    self.thirdPartyProfileTotal = 0;
    self.queryPromise = null;
    self.sort = "name";

    self.countryCodeMap = { 1 : 'US' , 2 : 'UK' };
    self.countryNameMap = { 'United States' : 1 , 'United Kingdom' : 2 };
    self.genderNameMap = { 'Male' : 'M' , 'Female' : 'F' , 'Unknown' : 'U' };

    self.highlightedFeeds = [];
    self.highlightedFeedsForRemoval = [];
    self.feedClientFilters = [];
    self.clientFeedMap = {};
    self.countryFeedMap = {};
    self.partyFeedMap = {};
    self.feedNameMap = {};
    self.feedVisibility = {};

    self.highlightedFeedGroups = [];
    self.highlightedFeedGroupsForRemoval = [];
    self.feedGroupVisibility = {};
    self.feedGroupNameMap = {};
    self.highlightedFeedClients = [];
    self.highlightedFeedClientsForRemoval = [];
    self.feedClientVisibility = {};
    self.feedClientNameMap = {};


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
        { 'header' : 'email_address' , 'label' : 'Email Address' },
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
        { 'header' : 'lower_case_md5' , 'label' : 'Lowercase MD5' },
        { 'header' : 'upper_case_md5' , 'label' : 'Uppercase MD5' } ,
        { 'header' : 'domain_group_name' , 'label' : "ISP" } ,
        { 'header' : 'dob' , 'label' : "Date of Birth" } ,
        { 'header' : 'feed_id' , 'label' : "Feed ID" } ,
        { 'header' : 'feed_name' , 'label' : "Feed Name" } ,
        { 'header' : 'short_name' , 'label' : "Feed Short Name" } ,
        { 'header' : 'client_name' , 'label' : "Client" } ,
        { 'header' : 'subscribe_date' , 'label' : 'Registration Date' } ,
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

    self.feedGroupName = '';
    self.feedList = [];
    self.feedForFeedGroup = [];
    self.feedGroupFormSubmitting = false;

    self.formErrors = [];

    modalService.setPopover();

    self.loadListProfiles = function () {
        ListProfileApiService.getListProfiles(
            self.thirdPartyCurrentPage ,
            self.paginationCount ,
            3,
            self.loadThirdPartyListProfilesSuccess,
            self.loadListProfilesFailureCallback
        );
        ListProfileApiService.getListProfiles(
            self.firstPartyCurrentPage ,
            self.paginationCount ,
            1,
            self.loadFirstPartyListProfilesSuccess ,
            self.loadListProfilesFailureCallback
        );
        self.loadListCombines();

        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );
    };

    self.loadThirdPartyListProfilesSuccess = function (response ){
        self.thirdPartyListProfiles = [];
        angular.forEach(response.data.data, function (value, index) {
            self.thirdPartyListProfiles.push(value);
        });

        self.pageCount = response.data.last_page;
        self.thirdPartyProfileTotal = response.data.total;
    };

    self.loadFirstPartyListProfilesSuccess = function (response ){
        self.firstPartyListProfiles = [];
        angular.forEach(response.data.data, function (value, index) {
            self.firstPartyListProfiles.push(value);
        });

        self.pageCount = response.data.last_page;
        self.firstPartyProfileTotal = response.data.total;
    };

    self.loadListProfilesFailureCallback = function ( response ) {
        modalService.simpleToast( 'Failed to load list profiles.' );
    };

    self.prepop = function ( listProfile ) {
        self.current = listProfile;
        self.current.country_id = self.current.country_id.toString();

        self.fixEmptyFields();

        $(function () { $('[data-toggle="tooltip"]').tooltip() });

        $timeout( function () {
            angular.forEach( self.current.feeds , function ( value , index ) {
                self.feedVisibility[ index ] = false;
            } );

            angular.forEach( self.current.isps , function ( value , index ) {
                self.ispVisibility[ index ] = false;
            } );

            angular.forEach(self.current.feedClients, function (value, index) {
                self.feedClientVisibility[ index ] = false;
            });

            angular.forEach(self.current.feedGroups, function (value, index) {
                self.feedGroupVisibility[ index ] = false;
            });

            angular.forEach(self.current.categories, function (value, index) {
                self.categoryVisibility[ index ] = false;
            });

            var len = self.current.selectedColumns.length;
            var columnsTemp = self.columnList.filter(function (item) {
                for (var i = 0; i < len; i++) {
                    if (item['header'] === self.current.selectedColumns[i]['header']) {
                        return false;
                    }
                }

                return true;
            });

            self.columnList = columnsTemp;

        } , 1500 );
    };

    self.fixEmptyFields = function () {
        angular.forEach( [ 'feedClients', 'feeds', 'feedGroups' , 'isps' , 'categories' ] , function ( value , index ) {
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

    self.fixInitialExportFields = function () {
        self.columnList.shift();
        self.columnList.shift();
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

        var namePartFeedClients = self.getFormattedName( self.current.feedClients );
        if ( namePartFeedClients != '' ) {
            nameParts.push( namePartFeedClients );
        }

        var namePartFeedGroups = self.getFormattedName( self.current.feedGroups );
        if ( namePartFeedGroups != '' ) {
            nameParts.push( namePartFeedGroups );
        }

        var namePartFeeds = self.getFormattedName( self.current.feeds );
        if ( namePartFeeds != '' ) {
            nameParts.push( namePartFeeds );
        }

        var namePartIsps = self.getFormattedName( self.current.isps );
        if ( namePartIsps != '' ) {
            nameParts.push( namePartIsps );
        }

        var namePartCountry = self.getFormattedName( self.current.country_id , self.countryCodeMap );
        if ( namePartCountry != '' ) {
            nameParts.push( namePartCountry );
        }

        var namePartRangeName = self.getFormattedRangeName();
        if ( namePartRangeName != '' ) {
            nameParts.push( namePartRangeName );
        }

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
        } );

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

    self.addFeeds = function ( ev ) {
        var confirm = $mdDialog.confirm()
                        .title( 'Are you sure you want to choose more than one feed?' )
                        .ariaLabel( 'Mulitple Feed Warning' )
                        .targetEvent( ev )
                        .ok( 'Yes' )
                        .cancel( 'Cancel' );

        if ( ( self.highlightedFeeds.length + Object.keys( self.current.feeds ).length ) > 1 ) {
            $mdDialog.show( confirm ).then(
                function () {
                    $mdDialog.show({
                        contentElement: '#feedGroupFormModal',
                        parent: angular.element(document.body)
                    });
                } ,
                function () {}
            );
        } else {
            self.addMembershipItems(
                { "highlighted" : self.highlightedFeeds , "visibility" : self.feedVisibility , "map" : self.feedNameMap } ,
                self.current.feeds ,
                self.generateName
            );
        }
    };

    self.closeFeedGroupModal = function () {
        $mdDialog.cancel();
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

    self.addFeedGroups = function ( ev ) {
        self.addMembershipItems(
            { "highlighted" : self.highlightedFeedGroups , "visibility" : self.feedGroupVisibility , "map" : self.feedGroupNameMap } ,
            self.current.feedGroups ,
            self.generateName
        );
    };

    self.removeFeedGroups = function () {
        self.removeMembershipItems(
            { "highlightedForRemoval" : self.highlightedFeedGroupsForRemoval , "visibility" : self.feedGroupVisibility } ,
            self.current.feedGroups ,
            function () {
                self.generateName();
            }
        );
    };

    self.addFeedClients = function () {
        self.addMembershipItems(
            { "highlighted" : self.highlightedFeedClients , "visibility" : self.feedClientVisibility , "map" : self.feedClientNameMap } ,
            self.current.feedClients ,
            self.generateName
        );
    };

    self.removeFeedClients = function () {
        self.removeMembershipItems(
            { "highlightedForRemoval" : self.highlightedFeedClientsForRemoval , "visibility" : self.feedClientVisibility } ,
            self.current.feedClients ,
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
            var feedListExistsAndBelongsInCountry = self.countryFeedMap[ parseInt( self.current.country_id ) ].indexOf( parseInt( feedId ) ) !== -1;
            if ( showAll && feedNotSelected ) {
                if(feedListExistsAndBelongsInCountry) {
                    self.feedVisibility[feedId] = true;
                }
            } else {
                self.feedVisibility[ feedId ] = false;

                angular.forEach( self.feedClientFilters , function ( clientId ) {
                    var feedListExistsAndBelongsToClient = ( typeof( self.clientFeedMap[ parseInt( clientId ) ] ) != 'undefined' && self.clientFeedMap[ parseInt( clientId ) ].indexOf( parseInt( feedId ) ) !== -1 );
                    if( feedListExistsAndBelongsToClient && feedNotSelected && feedListExistsAndBelongsInCountry ) {
                        self.feedVisibility[ feedId ] = true;
                    }
                } );
            }
        } );
    };

    self.updateFeedVisibilityFromCountry = function () {
        angular.forEach( self.feedVisibility , function ( visibility , feedId ) {
                    var feedListExistsAndBelongsInCountry = ( typeof( self.countryFeedMap[ parseInt( self.current.country_id ) ] ) != 'undefined' && self.countryFeedMap[ parseInt( self.current.country_id ) ].indexOf( parseInt( feedId ) ) !== -1);
                    if( feedListExistsAndBelongsInCountry ) {
                        self.feedVisibility[ feedId ] = true;
                    } else{
                        self.feedVisibility[ feedId ] = false;
                    }
                } );
    };

    self.updateFeedVisibilityFromParty = function () {
        angular.forEach( self.feedVisibility , function ( visibility , feedId ) {
            var feedListExistsAndBelongsInParty = ( typeof( self.partyFeedMap[ parseInt( self.current.party ) ] ) != 'undefined' && self.partyFeedMap[ parseInt( self.current.party ) ].indexOf( parseInt( feedId ) ) !== -1);
            if( feedListExistsAndBelongsInParty ) {
                self.feedVisibility[ feedId ] = true;
            } else{
                self.feedVisibility[ feedId ] = false;
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

    self.updateCountry = function (){
        self.generateName();
        self.updateFeedVisibilityFromCountry();
    };

    self.updateParty = function (){
        self.updateFeedVisibilityFromParty();
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

    self.deleteListProfile = function ( ev , id ) {
        var confirm = $mdDialog.confirm()
                        .title( 'Are you sure you want to delete this List Profile?' )
                        .ariaLabel( 'Delete List Profile' )
                        .targetEvent( ev )
                        .ok( 'Yes I am Sure' )
                        .cancel( 'No' );

        $mdDialog.show( confirm ).then(
            function () {
                ListProfileApiService.deleteListProfile( id , self.deleteListProfileSuccess ,  self.deleteListProfileFailure );
            } ,
            function () {}
        );
    }

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/listprofile' );
        $window.location.href = '/listprofile';
    };

    self.failureCallback = function ( response ) {
        formValidationService.loadFieldErrors( self , response );
    };


    self.deleteListProfileFailure = function ( response ) {
        modalService.setModalLabel('Failed To Delete List Profile');
        modalService.setModalBodyRawHtml( 'The List Profile is currently used in a Deploy.' );
        modalService.launchModal();
        self.loadListProfiles();
    };

    self.deleteListProfileSuccess = function ( response ) {
        modalService.simpleToast("Successfully Deleted List Profile");
        self.loadListProfiles();
    };

    self.nameCombine = function (){
        $('#createCombine').modal('show');
    };

    self.createCombine = function (){
        ListProfileApiService.createCombine(self.combineName, self.ftpFolder ,self.selectedProfiles, self.combineParty, self.createCombineSuccess, self.createCombineFail);
    };

    self.updateCombine = function () {
        ListProfileApiService.updateCombine( self.currentCombine , self.SuccessCallBackRedirect , self.updateCombineFail );
    };

    self.toggleRow = function (selectedValue, selectedParty) {
        var index = self.selectedProfiles.indexOf(selectedValue);
        if (index >= 0) {
            self.selectedProfiles.splice(index, 1);
        } else {
            self.selectedProfiles.push(selectedValue);
        }
        self.showCombine = self.selectedProfiles.length > 1;
        self.combineParty = selectedParty;
    };

    self.isCreatingCombine = function( profile ) {
        return self.selectedProfiles.indexOf( profile ) > -1;
    };

    self.loadListProfileList = function () {
        ListProfileApiService.getAllListProfiles( self.getAllListProfilesSuccess, self.getAllListProfilesFail )
    };

    self.getAllListProfilesSuccess = function ( response ) {


        angular.forEach(response.data, function (value, index){
            if(value.party == self.currentCombine.party) {
                self.listProfilesList.push(value);
            }
        });

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
        modalService.simpleToast("Failed to load list of list profiles.");
    };

    self.setCombine = function ( combineId , combineFolder, combineName , combineParty, listProfiles ) {
        self.currentCombine.id = combineId;
        self.currentCombine.ftpFolder = combineFolder;
        self.currentCombine.combineName = combineName;
        self.currentCombine.party = combineParty;
        self.prepopListProfiles = listProfiles;
    };

    self.clearSelection = function (){
        self.selectedProfiles = [];
        self.showCombine = false;
        self.combineParty = false;
    };

    self.loadListCombines = function (){
        ListProfileApiService.getCombines(self.loadCombinesSuccess,self.loadCombineFail);
    };

    self.loadCombinesSuccess = function (response){
        self.listCombines = response.data;
    };

    self.createCombineSuccess = function (response){
        $('#createCombine').modal('hide');
        modalService.setModalLabel('Success');
        modalService.setModalBody("List combine was created.");
        modalService.launchModal();

        self.selectedProfiles = [];
        self.showCombine = false;
        formValidationService.resetFieldErrors( self );

        self.loadListCombines();
        self.combineName = "";
        self.ftpFolder = "lp_combine";
    };

    self.loadCombineFail = function (response) {
        modalService.simpleToast("List combine failed to load.");

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
        modalService.setModalLabel('Success');
        modalService.setModalBody("List combine export has started.");
        modalService.launchModal();
    };

    self.exportCombineFail = function (response){
        modalService.setModalLabel('Error');
        modalService.setModalBody("List combine failed to export.");
        modalService.launchModal();
    };

    self.copyListProfile = function ( ev , id, name) {
            var confirm = $mdDialog.confirm()
                .title( 'Are you sure you want to copy '+ name + '?' )
                .ariaLabel( 'Copy Warning' )
                .targetEvent( ev )
                .ok( 'Yes' )
                .cancel( 'Cancel' );

            $mdDialog.show( confirm ).then(
                function () {
                    ListProfileApiService.copyListProfile(id,self.copyProfileSuccess, self.copyProfileFail);
                }
            );
    };

    self.copyProfileSuccess = function (response){
        var newId = response.data.id;

        $location.url( '/listprofile/edit/' + newId );
        $window.location.href = '/listprofile/edit/' + newId;

    };

    self.copyProfileFail = function (response){
        modalService.setModalLabel('Error');
        modalService.setModalBody("Failed to copy list profile.");
        modalService.launchModal();

    };

    self.showAlert = function( message , parentId ) {
        if (self.enableAlert){
            modalService.alertToast(message, parentId );
        }

        self.enableAlert = false;
    }

    self.loadFeedList = function () {
        FeedApiService.getAllFeeds( self.getAllFeedsSuccessCallback , self.getAllFeedsFailureCallback );
    };

    self.getAllFeedsSuccessCallback = function ( response ) {

        var sortFeedList = [];

        angular.forEach( response.data , function ( value ) {
            value.feedListDisplayName = value.short_name + " ( " + value.id + " ) " ;
            sortFeedList.push(value);
        } );

        self.feedList = sortFeedList;
    };

    self.getAllFeedsFailureCallback = function ( response ) {
        modalService.simpleToast( 'Failed to load feed group\'s list of feeds.' );
    };

    self.createFeedGroup = function () {
        self.feedGroupFormSubmitting = true;

        var feeds = [];

        angular.forEach( self.feedForFeedGroup , function ( value ) {
            feeds.push( value.id );
        } );

        FeedGroupApiService.createFeedGroup(
            { "name" : self.feedGroupName , "feeds" : feeds } ,
            self.createFeedGroupSuccessCallback ,
            self.createFeedGroupFailureCallback
        );
    };

    self.createFeedGroupSuccessCallback = function ( response ) {
        self.feedGroupVisibility[ response.data.id ] = false;
        self.feedGroupNameMap[ response.data.id ] = self.feedGroupName;
        self.current.feedGroups[ response.data.id ] = self.feedGroupName;

        //Add new feed group to the widget
        var list = angular.element(document).find( '#feedGroupList' );
        var newOption = angular.element( "<option ng-show='listProfile.feedGroupVisibility[ " + response.data.id  + " ]' value='" + response.data.id  + "'>" + self.feedGroupName + "</option>" );
        //This works for adding feeds to selected but couldn't get the left option to hide.
        $compile( newOption );
        list.append( newOption );

        //Removing current feeds
        self.removeMembershipItems(
            { "highlightedForRemoval" : Object.keys( self.current.feeds ) , "visibility" : self.feedVisibility } ,
            self.current.feeds ,
            function () {
                self.updateFeedVisibility();
                self.generateName();
            }
        );

        self.feedGroupName = '';
        self.feedForFeedGroup = [];

        self.feedGroupFormSubmitting = false;

        self.closeFeedGroupModal();
    };

    self.createFeedGroupFailureCallback = function ( response ) {
        response.feedGroupName = response.name;

        delete( response.name );

        formValidationService.loadFieldErrors( self , response );

        self.feedGroupFormSubmitting = false;
    };
} ] );
