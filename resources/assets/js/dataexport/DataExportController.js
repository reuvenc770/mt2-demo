mt2App.controller( 'DataExportController' , [ '$rootScope' , '$log' , '$window' , '$location' , 'DataExportApiService' , function ( $rootScope , $log , $window , $location , DataExportApiService ) { 
  var self = this;
  self.createUrl = '/dataexport/create';
  self.testUser = 217;

  // Page properties
  self.dataExports = [];
  self.current = {
    "exportId": 0,
    "filename": "",
    "ftpServer": "",
    "ftpUser": "",
    "ftpPassword": "",
    "ftpFolder": "",
    "NumberOfFiles": "",
    "client_group": {"id": "", "name": ""},
    "profile": {"id": "", "name": ""},
    "frequency": "",
    "fullPostalOnly": "",
    "addressOnly": "",
    "sendBluehornet": "",
    "SendToImpressionwiseDays": "NNNNNNN",
    "seeds": "",
    "includeHeaders": "",
    "doubleQuoteFields": "",
    "fields": {
      "email_addr": false,
      "eid": false,
      "first_name": false,
      "last_name": false,
      "sdate": false,
      "Status": false,
      "ISP": false,
      "url": false,
      "gender": false,
      "client_id": false,
      "username": false,
      "cdate": false,
      "address": false,
      "address2": false,
      "city": false,
      "state": false,
      "zip": false,
      "dob": false,
      "phone": false,
      "ip": false,
      "country": false,
      "client_network": false,
      "MD5": false,
      "UMD5": false,
      "adate": false
    },
    "repull": '',
    "esp": '',
    "otherField": "",
    "otherValue": "" 
  };
  self.formErrors = [];
  self.selectedExports = {};

  // Day-specific properties
  self.days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
  self.current.impMonday = self.current.SendToImpressionwiseDays[0];
  self.current.impTuesday = self.current.SendToImpressionwiseDays[1];
  self.current.impWednesday = self.current.SendToImpressionwiseDays[2];
  self.current.impThursday = self.current.SendToImpressionwiseDays[3];
  self.current.impFriday = self.current.SendToImpressionwiseDays[4];
  self.current.impSaturday = self.current.SendToImpressionwiseDays[5];
  self.current.impSunday = self.current.SendToImpressionwiseDays[6];


  // Pagination properties
  self.paginationCount = "10";
  self.currentlyLoading = 0;
  self.pageCount = 0;
  self.currentPage = 1;


  // Index page setup
  self.displayedStatus = 'active';
  self.displayedStatusButtonText = 'View Paused Exports';
  self.massActionButtonText = 'Pause';


  // Profile search
  self.profileSearchText = "";
  self.profiles = [];

  // Client Group search
  self.clientGroupSearchText = "";
  self.clientGroups = [];

  // ESPs search
  self.espSearchText = '';
  $rootScope.selectedEsps = {};
  self.currentSelectedEsp = '';
  self.espChipList = [];

  /**
  * Loading Flags
  */
  self.creatingDataExport = false;
  self.copyingDataExport = false;
  self.deletingDataExport = false;
  self.updatingDataExport = false;

  self.setupPage = function() {
    self.current.exportId = 0;
    var currentPath = $location.path();
    var pathParts = currentPath.match(new RegExp(/(\d+)/));
    var fillPage = (pathParts !== null) 
      && pathParts.length > 0
      && angular.isNumber(parseInt(pathParts[0]));

    self.loadEsps();

    if (fillPage) {
      self.current.exportId = pathParts[ 0 ];
      console.log("running data pull");
      DataExportApiService.getDataExport(
        self.current.exportId ,
        self.prepopPageSuccessCallback ,
        self.prepopPageFailureCallback
      );
    }

    self.loadProfiles();
    self.loadClientGroups();
    
  };

  /**
   * Main CRUD methods
   */

  self.loadActiveDataExports = function() {
    self.currentlyLoading = 1;
    DataExportApiService.getActiveDataExports(
      self.currentPage,
      self.paginationCount,
      self.loadDataExportsSuccessCallback,
      self.loadActiveDataExportsFailureCallback
    );
  };

  self.loadPausedDataExports = function() {
    self.currentlyLoading = 1;
    DataExportApiService.getPausedDataExports(
      self.currentPage,
      self.paginationCount,
      self.loadDataExportsSuccessCallback,
      self.loadPausedDataExportsFailureCallback
    );
  };

  self.saveDataExport = function(event) {

    var saveData = {
      "exportId": self.current.exportId,
      "seeds": self.current.seeds,
      "ftpFolder": self.current.ftpFolder,
      "ftpServer": self.current.ftpServer,
      "ftpUser": self.current.ftpUser,
      "ftpPassword": self.current.ftpPassword,
      "NumberOfFiles": self.current.NumberOfFiles,
      "sendBlueHornet": self.current.sendBluehornet,
      "fullPostalOnly": self.current.fullPostalOnly,
      "addressOnly": self.current.addressOnly,
      "frequency": self.current.frequency,
      "doubleQuoteFields": self.current.doubleQuoteFields,
      "includeHeaders": self.current.includeHeaders,
      "otherField": self.current.otherField,
      "otherValue": self.current.otherValue,

      "gid": self.current.client_group.id,
      "profileId": self.current.profile.id,
      "imp_monday": self.current.impMonday,
      "imp_tuesday": self.current.impTuesday,
      "imp_wednesday": self.current.impWednesday,
      "imp_thursday": self.current.impThursday,
      "imp_friday": self.current.impFriday,
      "imp_saturday": self.current.impSaturday,
      "imp_sunday": self.current.impSunday,
      "exportType": "Regular",
      "pname": self.current.fileName,
      "fields": Object.keys(self.current.fields).filter( function (field) {
        return self.current.fields[field];
      }),
      "esp": Object.keys($rootScope.selectedEsps),
      "outname": '',
      "repull": '',
      "SendToEmail": '',
      "suppname":  '',
      "ConfirmEmail": ''

    };

    DataExportApiService.saveDataExport(
      saveData,
      self.saveDataExportSuccessCallback,
      self.saveDataExportFailureCallback
    );
  };

  self.copyDataExport = function(id) {
    console.log('copying data export' + id);
    DataExportApiService.copyDataExport(
      id,
      self.copyDataExportSuccessCallback,
      self.copyDataExportFailureCallback
    );
  };

  self.deleteDataExport = function(id) {    
    DataExportApiService.deleteDataExport(
      id,
      self.deleteDataExportSuccessCallback,
      self.deleteDataExportFailureCallback
    );
  };

  self.changeDataExportStatus = function(id) {
    if ('active' === self.displayedStatus) {
      DataExportApiService.pauseDataExport(
        id,
        self.changeStatusDataExportSuccessCallback,
        self.changeStatusDataExportFailureCallback
      );
    }
    else {
      DataExportApiService.activateDataExport(
        id,
        self.changeStatusDataExportSuccessCallback,
        self.changeStatusDataExportFailureCallback
      );
    }

  };

  self.viewAdd = function() {
    $location.url(self.createUrl);
    $window.location.href = self.createUrl;
  };


  /**
   * Index page mass methods
   */

  self.switchDisplayedStatus = function() {
    // Clear out the selects
    self.selectedExports = {};

    if ('active' === self.displayedStatus) {
      self.loadPausedDataExports();
      self.displayedStatus = 'paused';
      self.displayedStatusButtonText = 'View Active Exports';
      self.massActionButtonText = "Activate";
    }
    else {
      self.loadActiveDataExports();
      self.displayedStatus = 'active';
      self.displayedStatusButtonText = 'View Paused Exports';
      self.massActionButtonText = "Pause";
    }
  };

  self.toggleInclusion = function(id) {
    if (self.selectedExports[id] === undefined) {
      // does not exist in hash. Add to hash
      self.selectedExports[id] = 1;
    }
    else {
      // does; remove
      delete self.selectedExports[id];
    }
  }

  self.pauseSelected = function() {
    if ('active' === self.displayedStatus) {
      console.log('Pausing ... ');
      DataExportApiService.massPauseDataExports(
        Object.keys(self.selectedExports),
        self.massStatusChangeSuccessCallback,
        self.massStatusChangeFailureCallback
      );
    }
    else {
      console.log('Activating ... ');
      DataExportApiService.massActivateDataExports(
        Object.keys(self.selectedExports),
        self.massStatusChangeSuccessCallback,
        self.massStatusChangeFailureCallback
      );
    }
    
  };

  self.rePullSelected = function() {
    DataExportApiService.massRePullDataExports(
      Object.keys(self.selectedExports),
      self.rePullSuccessCallback,
      self.rePullFailureCallback
    );
  };


  /**
   * Methods to handle profile autocomplete
   */

  self.loadProfiles = function () {
    DataExportApiService.getProfiles(
      self.loadProfileApiSuccessCallback, 
      self.loadProfileApiFailureCallback
    );
  }; 

  self.getProfile = function(searchText) {
    return searchText ? self.profiles.filter( function ( obj ) { 
      return obj.name !== null && obj.name.toLowerCase().indexOf( searchText.toLowerCase() ) === 0;
    }) : self.profiles;
  };

  self.setProfile = function(profile) {
    if (typeof profile !== "undefined") {
      self.current.profile.id = profile.id;
    }
  };

  /**
   * Methods to handle esp autocomplete
   */

  self.loadEsps = function() {
    DataExportApiService.getEsps(
      self.loadEspSuccessCallback,
      self.loadEspFailureCallback
    );
  };

  self.getEspForText = function(searchText) {
    return searchText ? self.espList.filter( function ( obj ) { 
      return obj.name !== null && obj.name.toLowerCase().indexOf( searchText.toLowerCase() ) === 0;
    } ) : self.espList;
  }

  /**
   * Methods to handle client group autocompletes
   */

  self.loadClientGroups = function () {
    DataExportApiService.getClientGroups(
      self.loadCGApiSuccessCallback, 
      self.loadCGApiFailureCallback
    );
  };

  self.getClientGroups = function(searchText) {
    return searchText ? self.clientGroups.filter( function ( obj ) { 
      return obj.name !== null && obj.name.toLowerCase().indexOf( searchText.toLowerCase() ) === 0;
    } ) : self.clientGroups;
  };

  self.setClientGroups = function(profile) {
    
  };


  /**
   * Watchers
   */

  $rootScope.$on( 'updatePage' , function () {
    $( '.collapse' ).collapse( 'hide' );
    self.loadActiveDataExports();
  });


  $rootScope.$watchCollection( 'selectedDays' , function ( newDays , oldDays ) {
    angular.forEach( newDays , function ( value , key ) {
        var currentChip = { "id" : key , "name" : value };

        var chipIndex = self.dayChipList.map(
            function ( chip ) { return chip.id }        
        ).indexOf( key ); 

        var chipExists = ( chipIndex !== -1 );

        if ( value !== false && !chipExists ) {
            self.dayChipList.push( currentChip );
        } else if ( value === false && chipExists ) {
            self.dayChipList.splice( chipIndex , 1 );
        }
    });
  });



  // Modal methods

  self.setModalLabel = function ( labelText ) {
    var modalLabel = angular.element( document.querySelector( '#pageModalLabel' ) );

    modalLabel.text( labelText );
  };

  self.setModalBody = function ( bodyText ) {
    var modalBody = angular.element( document.querySelector( '#pageModalBody' ) );

    modalBody.text( bodyText );
  }

  self.launchModal = function () {
    $( '#pageModal' ).modal('show');
  };

  self.resetModal = function () {
    self.setModalLabel( '' );
    self.setModalBody( '' );

    $( '#pageModal' ).modal('hide');
  };


  // Methods used for ESP chips

  self.updateEspCheckboxList = function ( item ) {
    if ( typeof( item ) !== 'undefined' ) {
      $rootScope.selectedEsps[ parseInt( item.id ) ] = item.name;
    }
  }

  self.removeEspChip = function ( $chip ) {
    $rootScope.selectedEsps[ parseInt( $chip.id ) ] = false;
  };

  self.selectAllEsps = function ( checked ) {
    angular.forEach( self.espList , function ( value , key ) {
      if ( checked === true ) {
        $rootScope.selectedEsps[ parseInt( value.id ) ] = value.name;
      } else {
        $rootScope.selectedEsps[ parseInt( value.id ) ] = false;
      }
    } );
  };

  self.getEsps = function ( espSearchText ) {
    return espSearchText ? self.espList.filter( function ( esp ) {
      return esp.name !== null && esp.name.toLowerCase().indexOf( espSearchText.toLowerCase() ) === 0;
    } ) : self.espList;
  }

  /* Callback procedures */

  self.prepopPageSuccessCallback = function(response) {
    
    var data = response.data[0];
    self.current = {
      "exportId": self.current.exportId,
      "fileName": data.fileName,
      "ftpServer": data.ftpServer,
      "ftpUser": data.ftpUser,
      "ftpPassword": data.ftpPassword,
      "ftpFolder": data.ftpFolder,
      "NumberOfFiles": data.NumberOfFiles,
      "client_group": {"id": data.client_group_id, "name": data.group_name},
      "profile": {"id": data.profile_id, "name": data.profile_name},
      "frequency": data.frequency,
      "fullPostalOnly": data.fullPostalOnly,
      "addressOnly": data.addressOnly,
      "sendBluehornet": data.sendBluehornet,
      "SendToImpressionwiseDays": data.SendToImpressionwiseDays,
      "seeds": data.seeds,
      "includeHeaders": data.includeHeaders,
      "doubleQuoteFields": data.doubleQuoteFields,
      "fields": {
        "email_addr": false,
        "eid": false,
        "first_name": false,
        "last_name": false,
        "sdate": false,
        "Status": false,
        "ISP": false,
        "url": false,
        "gender": false,
        "client_id": false,
        "username": false,
        "cdate": false,
        "address": false,
        "address2": false,
        "city": false,
        "state": false,
        "zip": false,
        "dob": false,
        "phone": false,
        "ip": false,
        "country": false,
        "client_network": false,
        "MD5": false,
        "UMD5": false,
        "adate": false
      },
      "otherField": data.otherField,
      "otherValue": data.otherValue
    };

    self.current.impMonday = data.SendToImpressionwiseDays[0];
    self.current.impTuesday = data.SendToImpressionwiseDays[1];
    self.current.impWednesday = data.SendToImpressionwiseDays[2];
    self.current.impThursday = data.SendToImpressionwiseDays[3];
    self.current.impFriday = data.SendToImpressionwiseDays[4];
    self.current.impSaturday = data.SendToImpressionwiseDays[5];
    self.current.impSunday = data.SendToImpressionwiseDays[6];

    var espsArr = data.esps.split(',');
    var espArrLen = espsArr.len;
    
    for (var i = 0; i < espArrLen; i++) {
      //espList
      var id = espsArr[i];
      $rootScope.selectedEsps[i] = 'test';
    }
    
    var fields = data.fieldsToExport.split(',');

    var l = fields.length;
    for (var i = 0; i < l; i++) {
      var field = fields[i];
      self.current.fields[field] = field;
    }

  };

  self.prepopPageFailureCallback = function() {
    self.setModalLabel('Error');
    self.setModalBody('Failed to load export.');
    self.launchModal();
  };


  self.loadDataExportsSuccessCallback = function(response) {
    self.currentlyLoading = 0;
    self.dataExports = response.data.data;
    self.pageCount = response.data.last_page;
  };

  self.loadActiveDataExportsFailureCallback = function(response) {
    self.setModalLabel('Error');
    self.setModalBody('Failed to load data exports.');
    self.launchModal();
  };

  self.loadPausedDataExportsFailureCallback = function(response) {
    self.setModalLabel('Error');
    self.setModalBody('Failed to load paused data exports.');
    self.launchModal();
  };


  self.saveDataExportSuccessCallback = function(response) {
    self.setModalLabel( 'Success!' );
    self.setModalBody( 'Saved export data.' );
    self.launchModal();
    $location.url( '/dataexport' );
    $window.location.href = '/dataexport';
  };

  self.saveDataExportFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to save export data.' );
    self.launchModal();
  };


  self.copyDataExportSuccessCallback = function(response) {
    self.setModalLabel( 'Success!' );
    self.setModalBody( 'Copied data export.' );
    self.launchModal();
  };

  self.copyDataExportFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to load client.' );
    self.launchModal();
  };


  self.deleteDataExportSuccessCallback = function(response) {
    self.setModalLabel( 'Success!' );
    self.setModalBody('Deleted export.');
    self.launchModal();
  };

  self.deleteDataExportFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to delete export.' );
    self.launchModal();
  };


  self.changeStatusDataExportSuccessCallback = function(response) {
    self.setModalLabel('Success!');
    self.setModalBody('Updated data export status.');
    self.launchModal();
  };

  self.changeStatusDataExportFailureCallback = function(response) {
    self.setModalLabel('Error');
    self.setModalBody('Failed to update data export status.');
    self.launchModal();
  };


  self.copyDataExportSuccessCallback = function(response) {
    self.setModalLabel( 'Success!' );
    self.setModalBody( 'Copied export.' );
    self.launchModal();
    $location.url('/dataexport');
    $window.location.href = '/dataexport/edit/' + response.data;
    console.log(response);
  };

  self.copyDataExportFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to copy export.' );
    self.launchModal();
  };

  self.rePullSuccessCallback = function(response) {
    self.setModalLabel( 'Success!' );
    self.setModalBody( 'Set exports to be re-pulled.' );
    self.launchModal();
  };

  self.rePullFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to set up re-pull.' );
    self.launchModal();
  };

  self.massStatusChangeSuccessCallback = function(response) {};

  self.massStatusChangeFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to change statuses.' );
    self.launchModal();
  };


  self.loadProfileApiSuccessCallback = function(response) {
    self.profiles = response.data;
    console.dir(self.profiles);
  };

  self.loadProfileApiFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to load profiles.' );
    self.launchModal();
  };

  self.loadCGApiSuccessCallback = function(response) {
    self.clientGroups = response.data;
    console.dir(self.clientGroups);
  };

  self.loadCGApiFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to load client groups.' );
    self.launchModal();
  };

  self.loadEspSuccessCallback = function(response) {
    self.espList = response.data;
    console.dir(self.espList);
  }

  self.loadEspFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to load esps.' );
    self.launchModal();
  }

}]);