mt2App.controller( 'DataExportController' , [ '$rootScope' , '$log' , '$window' , '$location' , 'DataExportApiService' , function ( $rootScope , $log , $window , $location , DataExportApiService ) {
  var self = this;
  self.createUrl = '/dataexport/create';
  self.testUser = 217;

  // Page properties
  self.dataExports = [];
  self.viewed = {
    "exportId": 0,
    "filename": "",
    "ftpServer": "",
    "ftpUser": "",
    "ftpPassword": "",
    "ftpFolder": "",
    "NumberOfFiles": "",
    "client_group": {"gid": "", "name": ""},
    "profile": {"pid": "", "name": ""},
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
  self.mdSelectedExports = [];

  // Day-specific properties
  self.days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
  self.viewed.impMonday = self.viewed.SendToImpressionwiseDays[0];
  self.viewed.impTuesday = self.viewed.SendToImpressionwiseDays[1];
  self.viewed.impWednesday = self.viewed.SendToImpressionwiseDays[2];
  self.viewed.impThursday = self.viewed.SendToImpressionwiseDays[3];
  self.viewed.impFriday = self.viewed.SendToImpressionwiseDays[4];
  self.viewed.impSaturday = self.viewed.SendToImpressionwiseDays[5];
  self.viewed.impSunday = self.viewed.SendToImpressionwiseDays[6];


  // Pagination properties
  self.paginationCount = "10";
  self.currentlyLoading = 0;
  self.pageCount = 0;
  self.currentPage = 1;
  self.dataExportTotal = 0;
  self.queryPromise = null;


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
  self.espList = [];
  self.selectedEsps = [];
  self.formEsps = [];
  self.availableWidgetTitle = 'Available ESPs';
  self.chosenWidgetTitle = 'Chosen ESPs';
  self.widgetName = 'esps';

  /**
  * Loading Flags
  */
  self.creatingDataExport = false;
  self.copyingDataExport = false;
  self.deletingDataExport = false;
  self.updatingDataExport = false;

  self.setupPage = function() {
    self.viewed.exportId = 0;
    var currentPath = $location.path();
    var pathParts = currentPath.match(new RegExp(/(\d+)/));
    var fillPage = (pathParts !== null)
      && pathParts.length > 0
      && angular.isNumber(parseInt(pathParts[0]));

    self.loadEsps();

    if (fillPage) {
      self.viewed.exportId = pathParts[ 0 ];
      DataExportApiService.getDataExport(
        self.viewed.exportId ,
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

   self.mdLoadActiveDataExports = function() {
    self.selectedExports = {};
    self.mdSelectedExports = [];
    self.loadActiveDataExports();
   };

  self.loadActiveDataExports = function() {
    self.queryPromise = DataExportApiService.getActiveDataExports(
      self.currentPage,
      self.paginationCount,
      self.loadDataExportsSuccessCallback,
      self.loadActiveDataExportsFailureCallback
    );
  };

  self.loadPausedDataExports = function() {
    self.queryPromise = DataExportApiService.getPausedDataExports(
      self.currentPage,
      self.paginationCount,
      self.loadDataExportsSuccessCallback,
      self.loadPausedDataExportsFailureCallback
    );
  };

  self.loadPage = function() {
    if ('active' === self.displayedStatus) {
      self.loadActiveDataExports();
    }
    else {
      self.loadPausedDataExports();
    }
  }

  self.saveDataExport = function(event) {
    var exportType = self.formEsps.length > 0 ? "ESP" : "Regular";

    var saveData = {
      "exportId": self.viewed.exportId,
      "seeds": self.viewed.seeds,
      "ftpFolder": self.viewed.ftpFolder,
      "ftpServer": self.viewed.ftpServer,
      "ftpUser": self.viewed.ftpUser,
      "ftpPassword": self.viewed.ftpPassword,
      "NumberOfFiles": self.viewed.NumberOfFiles,
      "sendBlueHornet": self.viewed.sendBluehornet,
      "fullPostalOnly": self.viewed.fullPostalOnly,
      "addressOnly": self.viewed.addressOnly,
      "frequency": self.viewed.frequency,
      "doubleQuoteFields": self.viewed.doubleQuoteFields,
      "includeHeaders": self.viewed.includeHeaders,
      "otherField": self.viewed.otherField,
      "otherValue": self.viewed.otherValue,
      "gid": self.viewed.client_group.gid,
      "profileId": self.viewed.profile.pid,
      "imp_monday": self.viewed.impMonday,
      "imp_tuesday": self.viewed.impTuesday,
      "imp_wednesday": self.viewed.impWednesday,
      "imp_thursday": self.viewed.impThursday,
      "imp_friday": self.viewed.impFriday,
      "imp_saturday": self.viewed.impSaturday,
      "imp_sunday": self.viewed.impSunday,
      "exportType": exportType,
      "pname": self.viewed.fileName,
      "fields": Object.keys(self.viewed.fields).filter( function (field) {
        return self.viewed.fields[field];
      }),
      "esp": self.formEsps,
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
  };

  self.mdToggleInclusion = function ( dataExport ) {
    self.toggleInclusion( dataExport.exportID );
  };

  self.pauseSelected = function() {
    if ('active' === self.displayedStatus) {
      DataExportApiService.massPauseDataExports(
        Object.keys(self.selectedExports),
        self.massStatusChangeSuccessCallback,
        self.massStatusChangeFailureCallback
      );
    }
    else {
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
    var newProfile = self.profiles.filter(function(item) {
      return item.name.toLowerCase() === profile.name.toLowerCase();
    });

    if (typeof profile !== "undefined" && 1 === newProfile.length) {
      self.viewed.profile.pid = newProfile[0].id;
      self.viewed.profile.name = newProfile[0].name;
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

  self.findClientGroup = function(searchText) {
    return searchText ? self.clientGroups.filter( function ( obj ) {
      return obj.name !== null && obj.name.toLowerCase().indexOf( searchText.toLowerCase() ) === 0;
    } ) : self.clientGroups;
  };

  self.setClientGroup = function(clientGroup) {

    var newClientGroup = self.findClientGroup(clientGroup);

    if (typeof clientGroup !== 'undefined' && 1 === newClientGroup.length) {
      self.viewed.client_group.gid = clientGroup.id;
    }
  };


  /**
   * Watchers
   */

  $rootScope.$on( 'updatePage' , function () {
    $( '.collapse' ).collapse( 'hide' );
    self.loadPage();
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
    if ( typeof( item ) !== 'undefined' && item.name !== '') {
      self.selectedEsps.push(item);
    }
  }

  self.matchEsps = function() {
    var selectedEspLen = self.selectedEsps.length;
    var espsLen = self.espList.length;

    for (var i = 0; i < selectedEspLen; i++) {
      for (var j = 0; j < espsLen; j++) {
        if (parseInt(self.selectedEsps[i].id) === parseInt(self.espList[j].id)) {
          self.selectedEsps[i].name = self.espList[j].name;
        }
      }
    }
  }

  self.removeEspChip = function ( $chip ) {
    var len = self.selectedEsps.length;
    for (var i = 0; i < len; i++) {
      if (self.selectedEsps.id == $chip.id) {
        self.selectedEsps.splice(i, 1);
      }
    }
  };

  self.selectAllEsps = function ( checked ) {
    angular.forEach( self.espList , function ( value , key ) {
      if ( checked === true ) {
        self.selectedEsps.push(value);
      } else {
        self.removeEspChip(value);
      }
    });
  };

  self.getEsps = function ( espSearchText ) {
    return espSearchText ? self.espList.filter( function ( esp ) {
      return esp.name !== null && esp.name.toLowerCase().indexOf( espSearchText.toLowerCase() ) === 0;
    } ) : self.espList;
  }

  self.returnFilterNestedArrayKey = function(array, key) {
    var output = [];
    var len = array.length;

    for (var i = 0; i < len; i++) {
      output.push(array[i][key]);
    }

    return output;
  }

  /* Callback procedures */

  self.prepopPageSuccessCallback = function(response) {

    var data = response.data[0];

    self.viewed = {
      "exportId": self.viewed.exportId,
      "fileName": data.fileName,
      "ftpServer": data.ftpServer,
      "ftpUser": data.ftpUser,
      "ftpPassword": data.ftpPassword,
      "ftpFolder": data.ftpFolder,
      "NumberOfFiles": data.NumberOfFiles,
      "client_group": {"gid": data.client_group_id, "name": data.group_name},
      "profile": {"pid": data.profile_id, "name": data.profile_name},
      "frequency": data.frequency,
      "fullPostalOnly": data.fullPostalOnly,
      "addressOnly": data.addressOnly,
      "sendBluehornet": data.sendBluehornet,
      "SendToImpressionwiseDays": data.SendToImpressionwiseDays,
      "seeds": data.seeds,
      "includeHeaders": data.includeHeaders,
      "doubleQuoteFields": data.doubleQuoteFields,
      "esp": data.esps,
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

    self.viewed.impMonday = data.SendToImpressionwiseDays[0];
    self.viewed.impTuesday = data.SendToImpressionwiseDays[1];
    self.viewed.impWednesday = data.SendToImpressionwiseDays[2];
    self.viewed.impThursday = data.SendToImpressionwiseDays[3];
    self.viewed.impFriday = data.SendToImpressionwiseDays[4];
    self.viewed.impSaturday = data.SendToImpressionwiseDays[5];
    self.viewed.impSunday = data.SendToImpressionwiseDays[6];

    var espsArr = data.esps.split(',');
    var espArrLen = espsArr.length;
    var currentEspId = null;

    for (var i = 0; i < espArrLen; i++) {
      // blank name for now - will be updated when esps are loaded
        currentEspId = parseInt( espsArr[i] , 10 );

      if ( currentEspId > 0 ) $rootScope[ self.widgetName ].push( currentEspId );
    }

    var fields = data.fieldsToExport.split(',');

    var l = fields.length;
    for (var i = 0; i < l; i++) {
      var field = fields[i];
      self.viewed.fields[field] = field;
    }

  };

  self.prepopPageFailureCallback = function() {
    self.setModalLabel('Error');
    self.setModalBody('Failed to load export.');
    self.launchModal();
  };


  self.loadDataExportsSuccessCallback = function(response) {
    self.dataExports = response.data.data;
    self.pageCount = response.data.last_page;
    self.dataExportTotal = response.data.total;
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

    self.loadPage();
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
  };

  self.loadProfileApiFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to load profiles.' );
    self.launchModal();
  };

  self.loadCGApiSuccessCallback = function(response) {
    self.clientGroups = response.data;
  };

  self.loadCGApiFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to load client groups.' );
    self.launchModal();
  };

  self.loadEspSuccessCallback = function(response) {
    self.espList = response.data;
    self.matchEsps();

  }

  self.loadEspFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to load esps.' );
    self.launchModal();
  }

  self.espMembershipCallback = function () {
    self.formEsps = [];

    angular.forEach( self.selectedEsps , function ( esp , espIndex ) {
        self.formEsps.push( esp.id );
    } );
  }

}]);
