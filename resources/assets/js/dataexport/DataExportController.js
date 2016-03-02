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
  self.paginationCount = 10;
  self.currentlyLoading = 0;
  self.pageCount = 0;
  self.currentPage = 1;


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

  self.getDataExport = function() {
    var currentPath = $location.path();
    var pathParts = currentPath.match(new RegExp(/(\d)/));
    var fillPage = (pathParts !== null) 
      && pathParts.length > 0
      && angular.isNumber(parseInt(pathParts[0]));

    if (fillPage) {
      //self.current;
    }
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

  };

  self.updateDataExport = function(event) {};

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
  }

  /**
   * Methods to handle profile autocomplete
   */

  self.loadProfiles = function () {
    DataExportApiService.getProfiles(self.loadProfilesSuccessCallback, self.loadProfilesFailureCallback);
  };

  self.getProfile = function(searchText) {
    return searchText ? self.profiles.filter( function ( obj ) { 
      return obj.name.toLowerCase().indexOf( searchText.toLowerCase() ) === 0;
    } ) : self.clientTypes;
  };

  self.setProfile = function(profile) {

  };




  /**
   *
   */

  self.loadClientGroups = function () {
    DataExportApiService.getClientGroups(self.loadProfilesSuccessCallback, self.loadProfilesFailureCallback);
  };

  self.getClientGroups = function(searchText) {
    return searchText ? self.profiles.filter( function ( obj ) { 
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
    self.loadClientGroups();
  });


  /**
   * Day Chip/Lookahead Field 
   */
  self.searchClient = function ( searchText ) {
      return searchText ? self.days.filter( function ( obj ) {
          var idRegex = new RegExp( '^' + searchText );

          return (
              obj.username.toLowerCase().indexOf( searchText.toLowerCase() ) === 0
              || idRegex.test( obj.client_id.toString() ) 
          );
      } ) : false;
  }

  self.updateDayCheckboxList = function ( item ) {
      if ( typeof( item ) !== 'undefined' ) {
          $rootScope.selectedDays[ item.client_id ] = item.username;
      }
  };

  self.updateClientFormField = function () {
      var clientDelimitedList = '';
      angular.forEach( self.clientChipList , function ( value , key ) {
          if ( key > 0 ) clientDelimitedList += "\n";

          clientDelimitedList += value.id;
      } );

      self.current.clients = clientDelimitedList;
  };

  self.formatChip = function ( $chip ) {
      if ( typeof( $chip.name ) === 'undefined' ) {
          return {
              'name' : $chip.username ,
              'id' : $chip.client_id
          };
      } else return $chip;
  };

  self.removeClientChip = function ( $chip ) {
      $rootScope.selectedDays[ $chip.id ] = false;
  };


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

  self.prepopPageSuccessCallback = function() {};

  self.prepopPageFailureCallback = function() {};


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


  self.saveDataExportSuccessCallback = function() {};

  self.saveDataExportFailureCallback = function() {};


  self.updateDataExportSuccessCallback = function() {};

  self.updateDataExportFailureCallback = function() {};


  self.copyDataExportSuccessCallback = function() {};

  self.copyDataExportFailureCallback = function() {};


  self.deleteDataExportSuccessCallback = function() {};

  self.deleteDataExportFailureCallback = function() {};


  self.pauseDataExportSuccessCallback = function() {};

  self.pauseDataExportFailureCallback = function() {};


  self.copyDataExportSuccessCallback = function() {};
  self.copyDataExportFailureCallback = function() {};
  self.copyDataExportSuccessCallback = function() {};
  self.copyDataExportFailureCallback = function() {};
  self.copyDataExportSuccessCallback = function() {};
  self.copyDataExportFailureCallback = function() {};

}]);
