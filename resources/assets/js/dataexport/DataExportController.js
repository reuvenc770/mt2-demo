mt2App.controller( 'DataExportController' , [ '$rootScope' , '$log' , '$window' , '$location' , 'DataExportApiService' , function ( $rootScope , $log , $window , $location , DataExportApiService ) { 
  var self = this;
  self.createUrl = '/dataexport/create';
  self.testUser = 217;

  // Page properties
  self.dataExports = [];
  self.current = {
    "filename": "",
    "ftpServer": "",
    "ftpUser": "",
    "ftpPassword": "",
    "ftpFolder": "",
    "NumberOfFiles": "",
    "client_group_id": "",
    "profile_id": "",
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

  /**
  * Loading Flags
  */
  self.creatingDataExport = false;
  self.copyingDataExport = false;
  self.deletingDataExport = false;
  self.updatingDataExport = false;

  self.setupPage = function() {
    var currentPath = $location.path();
    var pathParts = currentPath.match(new RegExp(/(\d+)/));
    var fillPage = (pathParts !== null) 
      && pathParts.length > 0
      && angular.isNumber(parseInt(pathParts[0]));

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
    DataExportApiService.createDataExport(
      self.current,
      self.saveDataExportSuccessCallback,
      self.saveDataExportFailureCallback
    );
  };

  self.updateDataExport = function(event) {
    //
    DataExportApiService.updateDataExport(
      self.current,
      self.saveDataExportSuccessCallback,
      self.saveDataExportFailureCallback
    );

  };

  self.copyDataExport = function(emailId) {
    DataExportApiService.copyDataExport(
      emailId

    );
  };

  self.deleteDataExport = function(event) {};

  self.pauseDataExport = function(event) {};

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
    //DataExportApiService.getProfiles(self.loadProfileApiSuccessCallback, self.loadProfileApiFailureCallback);

    // Temporary override:
    self.profiles = [{"id":1,"name":"jim test"}, {"id":2,"name":"Jim test"}, {"id":3,"name":"deantest"}, {"id":4,"name":"AolHotmailL90"}, {"id":5,"name":"DL30AHOY"}, {"id":6,"name":"DL15AHOY"},
 {"id":7,"name":"UKL180(OC)"}, {"id":8,"name":"AOL_D_L90"}, {"id":9,"name":"testDLAHOY30"}, {"id":10,"name":"OthersL90"}, {"id":11,"name":"jim test"}, {"id":12,"name":"jim test"},
{"id":13,"name":"HotmailL60"}, {"id":14,"name":"aolhotmaill60"}, {"id":15,"name":"AHOY300"}, {"id":16,"name":"OthersL30"}, {"id":17,"name":"YahooOnlyL90(OCNR)"},
{"id":18,"name":"AOL_OC_L30"}, {"id":19,"name":"SmallerIsps180"}, {"id":20,"name":"AOL180OC"}, {"id":21,"name":"HotmailL30"}, {"id":22,"name":"HotmailL90"}, {"id":23,"name":"YahooL30"},
{"id":24,"name":"YahooL60"}, {"id":25,"name":"OthersL60"}, {"id":26,"name":"ACCEEHVL90(OC)"}, {"id":27,"name":"HAY Newest Records"}, {"id":28,"name":"YahooL10-90"},
{"id":29,"name":"UKYahooL60"}, {"id":30,"name":"Default300"}, {"id":31,"name":"ACGHYL90(OCL30)"}, {"id":32,"name":"OthersL90(OCL30)"}, {"id":33,"name":"OthersL15"},
{"id":34,"name":"HotmailL15"}, {"id":35,"name":"AOLL60"}, {"id":36,"name":"AOLL15"}, {"id":37,"name":"YahooL15"}, {"id":38,"name":"YahooL90"}, {"id":39,"name":"AOLHotmailYahooL15"},
{"id":40,"name":"AOLHotmailYahooL30"}, {"id":41,"name":"AOLHotmailYahooL60"}, {"id":42,"name":"AOLHotmailYahooL90"}, {"id":43,"name":"HotmailYahooL15"},
{"id":44,"name":"HotmailYahooL30"}, {"id":45,"name":"HotmailYahooL60"}, {"id":46,"name":"HotmailYahooL90"}, {"id":47,"name":"OthersL15(OC)"}, {"id":48,"name":"OthersL30(OC)"}, 
{"id":49,"name":"OthersL60(OC)"}, {"id":50,"name":"OthersL90(OC)"}, {"id":51,"name":"YahooL15(OC)"}, {"id":52,"name":"YahooL60(OC)"}, {"id":53,"name":"YahooL30(OC)"}, 
{"id":54,"name":"YahooL90(OC)"}, {"id":55,"name":"AOLL15(OC)"}, {"id":56,"name":"AOLL30(OC)"}, {"id":57,"name":"AOLL60(OC)"}, {"id":58,"name":"AOL_OC_L90"}, 
{"id":59,"name":"HotmailL15(OC)"}, {"id":60,"name":"HotmailL30(OC)"}, {"id":61,"name":"HotmailL60(OC)"}, {"id":62,"name":"HotmailL90(OC)"}, {"id":63,"name":"HotmailYahooL15(OC)"},
 {"id":64,"name":"HotmailYahooL30(OC)"}, {"id":65,"name":"HotmailYahooL60(OC)"}, {"id":66,"name":"HotmailYahooL90(OC)"}, {"id":67,"name":"AOLHotmailYahooL15(OC)"}, 
 {"id":68,"name":"AOLHotmailYahooL30(OC)"}, {"id":69,"name":"AOLHotmailYahooL60(OC)"}, {"id":70,"name":"AOLHotmailYahooL90(OC)"}, {"id":71,"name":"UKL60(OC)"}, 
 {"id":72,"name":"CCEEHVL90"}, {"id":73,"name":"CEHL90"}, {"id":74,"name":"UKYahoo(L90OC)"}, {"id":75,"name":"ACCEEHVL15(OC)"}, {"id":76,"name":"ACCEEHVL30(OC)"}, 
 {"id":77,"name":"ACCEEHVL60(OC)"}, {"id":78,"name":"VerizonL90(OC)"}, {"id":79,"name":"UKHotmailYahooL15"}, {"id":80,"name":"ALL30"}, {"id":81,"name":"ALL3060"}, 
 {"id":82,"name":"ALL6090"}, {"id":83,"name":"ALL90120"}, {"id":84,"name":"ALL120180"}, {"id":85,"name":"Others120(OC)"}, {"id":86,"name":"allISPsL30NoYahoo"}, {"id":87,"name":"ALL15"}, 
 {"id":88,"name":"All3060ny"}, {"id":89,"name":"All120180ny"}, {"id":90,"name":"All6090ny"}, {"id":91,"name":"All90120ny"}, {"id":92,"name":"ALL15ny"}, {"id":93,"name":"ALL30ny"}, 
 {"id":94,"name":"ALL30ny"}, {"id":95,"name":"allISPsL15"}, {"id":96,"name":"VerizonL30(OC)"}, {"id":97,"name":"ACEEL30(OC)"}, {"id":98,"name":"ACEEL60(OC)"}, 
 {"id":99,"name":"ComcastL30(OC)"}, {"id":100,"name":"ComcastL60(OC)"}, {"id":101,"name":"VerizonL60(OC)"}, {"id":102,"name":"UKYahooL90"}, {"id":103,"name":"CVL90"}, 
 {"id":104,"name":"OthersHL60(OC)"}, {"id":105,"name":"ALLispsotherL60(OC)"}, {"id":106,"name":"ALLispNsothersL90(OC)"}, {"id":107,"name":"VerizonOL90"}, 
 {"id":108,"name":"OthersYL60(OC)"}, {"id":109,"name":"OthersYL90(OC)"}, {"id":110,"name":"OthersHYL90(OC)"}, {"id":111,"name":"ACCEEYVL90(OC)"}, {"id":112,"name":"ACCEEYVL60(OC)"}, 
 {"id":113,"name":"ACCEEVL60(OC)"}, {"id":114,"name":"ACCEEVL60(OC)"}, {"id":115,"name":"ACCEEVL90(OC)"}, {"id":116,"name":"SmallerispsYL90(OC)"}, {"id":117,"name":"ComRizonL90(OC)"}, 
 {"id":118,"name":"VerizonOthersL90(OC)"}, {"id":119,"name":"UKOthersL60"}, {"id":120,"name":"TestingTime"}, {"id":121,"name":"AOLYahooL30"}, {"id":122,"name":"GMSORDDAOLCoxYahoo"}, 
 {"id":123,"name":"DDMidNightDropJan01ToMar5"}, {"id":124,"name":"YahooFeb22ToApr15"}, {"id":125,"name":"CCEEGVYL30(OC)"}, {"id":126,"name":"ACCEEGOVL60(OC)"}, 
 {"id":127,"name":"AOLYahooL60"}, {"id":128,"name":"VerizonL15(OC)"}, {"id":129,"name":"ComcastL15(OC)"}, {"id":130,"name":"GmailL15(OC)"}, {"id":131,"name":"ISPsApril1To15"}, 
 {"id":132,"name":"AOLYahooTWL20To55(OC20To30)"}, {"id":133,"name":"CHPHotmail10k"}, {"id":134,"name":"History_Home_Biz"}, {"id":135,"name":"History_Cash_Cred_Fin_No_Other"}, 
 {"id":136,"name":"GMSUK3-200"}, {"id":137,"name":"GMSUS60"}, {"id":138,"name":"AOL_Deliverables_L15-45"}, {"id":139,"name":"OthersL90(OC)"}, {"id":140,"name":"ISPsL30(OC)"}, 
 {"id":141,"name":"ISPsL30-60(OC)"}, {"id":142,"name":"ISPsL60-90(OC)"}, {"id":143,"name":"ISPsL30-180"}, {"id":144,"name":"ISPsL30(OC)-Y"}, {"id":145,"name":"ISPsL30-60(OC)-Y"}, 
 {"id":146,"name":"ISPsL60-90(OC)-Y"}, {"id":147,"name":"ISPsL30-180-Y"}, {"id":148,"name":"AOL_Deliverables_L45-60"}, {"id":149,"name":"UKYahooJan1ToApr15"}, 
 {"id":150,"name":"Yahoo_Deliverables_L40-80"}, {"id":151,"name":"ISPsL90-120(OC)"}, {"id":152,"name":"AOLHotmailOthersL30-60(OC)"}, {"id":153,"name":"HotmailOthersL120(OC)"}, 
 {"id":154,"name":"ISPSL90-120NoAOL"}, {"id":155,"name":"ISPSOthersL30-60(OC)"}, {"id":156,"name":"OthersYahooL60-90"}, {"id":157,"name":"YahooHotmailOthersL30-60"}, 
 {"id":158,"name":"AOLOthersL60-90(OC)"}, {"id":159,"name":"AOLOthersL30-180"}, {"id":160,"name":"HotmailOthers90(OC)"}, {"id":161,"name":"AOLOthersL30"}, 
 {"id":162,"name":"YahooOthersL30"}, {"id":163,"name":"ISPSOthersL30NoHotmail"}, {"id":164,"name":"AOLOthersL30-60"}, {"id":165,"name":"AOLHotmailOthersL30-60"}, 
 {"id":166,"name":"HotmailOthersL30-60"}, {"id":167,"name":"YahooOthersL90-120"}, {"id":168,"name":"HotmailOthersL60-90(OC)"}, {"id":169,"name":"ISPSL30NoAOL"}, 
 {"id":170,"name":"YahooOthersL30-60"}, {"id":171,"name":"YahooOthersL60-90"}, {"id":172,"name":"YahooOthersL120(OC)"}, {"id":173,"name":"ISPsOthersL30-180NoAOLHotmail"}, 
 {"id":174,"name":"ISPsOthersL30"}, {"id":175,"name":"AOL_History_A_B_Test"}, {"id":176,"name":"YahooOthersL30-90"}, {"id":177,"name":"ISPSOthersL60-90(OC)"}, 
 {"id":178,"name":"HotmailOthers30-90"}, {"id":179,"name":"OthersL30-60(OC)"}, {"id":180,"name":"OthersL60-90(OC)"}, {"id":181,"name":"OthersL90-120(OC)"}, 
 {"id":182,"name":"AOLHotmailOthersL90(OC)"}, {"id":183,"name":"AOLOthersL90(OC)"}, {"id":184,"name":"AOLOthersL90-120(OC)"}, {"id":185,"name":"ISPsL60(OC)"}, 
 {"id":186,"name":"YahooHistoryBuildingL30-60"}, {"id":187,"name":"UKYahooL20-80(OC20To30)"}, {"id":188,"name":"YahooL20To65(OC20To25)"}, {"id":189,"name":"ISPsL30(OC)-small"}, 
 {"id":190,"name":"ISPsL30-60(OC)-small"}, {"id":191,"name":"ISPsL60-90(OC)-small"}, {"id":192,"name":"ISPsL90-120(OC)-small"}, {"id":193,"name":"UKOthersL180"}, 
 {"id":194,"name":"ISPsL90(OC)"}, {"id":195,"name":"AOLYahooL60(OC)"}, {"id":196,"name":"UKYahooL90(OC20-35)"}, {"id":197,"name":"Others180(OC)"}, {"id":198,"name":"GMSAOLYahooL30(OC30)"}];
  };

  self.getProfile = function(searchText) {
    return searchText ? self.profiles.filter( function ( obj ) { 
      return obj.name.toLowerCase().indexOf( searchText.toLowerCase() ) === 0;
    } ) : self.profiles;
  };

  self.setProfile = function(profile) {
    if (typeof profile !== "undefined") {
      self.current.profile_id = profile.id;
    }
  };




  /**
   * Methods to handle client group autocompletes
   */

  self.loadClientGroups = function () {
    DataExportApiService.getClientGroups(self.loadCGApiSuccessCallback, self.loadCGApiFailureCallback);
  };

  self.getClientGroups = function(searchText) {
    return searchText ? self.clientGroups.filter( function ( obj ) { 
      return obj.name.toLowerCase().indexOf( searchText.toLowerCase() ) === 0;
    } ) : self.clientTypes;
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

  /* Callback procedures */

  self.prepopPageSuccessCallback = function(response) {
    
    var data = response.data[0];
    self.current = {
      "fileName": data.fileName,
      "ftpServer": data.ftpServer,
      "ftpUser": data.ftpUser,
      "ftpPassword": data.ftpPassword,
      "ftpFolder": data.ftpFolder,
      "NumberOfFiles": data.NumberOfFiles,
      "client_group_id": data.client_group_id,
      "profile_id": data.profile_id,
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
    $location.url( '/dataexport' );
    $window.location.href = '/dataexport';
  };

  self.saveDataExportFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to save export data.' );
    self.launchModal();
  };


  self.copyDataExportSuccessCallback = function(response) {};

  self.copyDataExportFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to load client.' );
    self.launchModal();
  };


  self.deleteDataExportSuccessCallback = function(response) {};

  self.deleteDataExportFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to load client.' );
    self.launchModal();
  };


  self.pauseDataExportSuccessCallback = function(response) {};

  self.pauseDataExportFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to pause data export.' );
    self.launchModal();
  };


  self.copyDataExportSuccessCallback = function(response) {};

  self.copyDataExportFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to copy export.' );
    self.launchModal();
  };

  self.rePullSuccessCallback = function(response) {};

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
    console.log('this is the response:');
    console.log(response);
    self.profiles = response.data.data;
  };

  self.loadProfileApiFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to load profiles.' );
    self.launchModal();
  };

  self.loadCGApiSuccessCallback = function(response) {
    self.clientGroups = response.data.data;
  };

  self.loadCGApiFailureCallback = function(response) {
    self.setModalLabel( 'Error' );
    self.setModalBody( 'Failed to load client groups.' );
    self.launchModal();
  };

}]);
