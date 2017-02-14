mt2App.controller( 'AWeberController' , [ '$rootScope' , '$log' , '$window' , '$location' , 'AWeberService' , 'modalService'  , function ( $rootScope , $log , $window , $location , AWeberService , modalService  ) {
    var self = this;
    self.$location = $location;
    self.currentMappings = {};
    self.reports = [];
    self.lists = [];
    self.availableWidgetTitle = "Active Lists";
    self.chosenWidgetTitle = "Inactive Lists" ;
    self.idField = 'id';
    self.nameField = 'name';
    self.deactiveLists = [];
    self.currentSelection = "";
    self.shouldHideList = [];
    /**
     * Click Handlers
     */
     self.loadReports = function () {
         AWeberService.getReports(self.getOrphanReportsSuccessCallback);
     };
    self.convertReport = function ( internalId , deployId ) {
        self.currentSelection = deployId;
        AWeberService.convertReport(internalId, deployId, self.getConvertReportSuccessCallback,self.getConvertReportFailCallback);
    };

    self.getLists = function (id){
        AWeberService.getLists(id,self.getListsSuccessCallback,self.somethingWentWrong);
    };

    self.updateLists = function () {
        AWeberService.updateLists(self.deactiveLists, self.updateListSuccessCallback, self.somethingWentWrong);
    };

    self.shouldHide = function (deployId) {
        return (-1 !== self.shouldHideList.indexOf(deployId));
    };

    /**
     * Callbacks
     */
    self.getOrphanReportsSuccessCallback = function ( response ) {
        self.reports = response.data;
    };

    self.getConvertReportSuccessCallback = function ( response ) {
        self.shouldHideList.push(self.currentSelection);
        self.currentSelection = '';
    };

    self.getConvertReportFailCallback = function ( response ) {
        self.reports = response.data;
    };

    self.getListsSuccessCallback = function (response) {
        angular.forEach(response.data, function(value, key) {
            if(self.lists[value.esp_account_id] === undefined) {
                self.lists[value.esp_account_id] = [];
                self.lists[value.esp_account_id]['active'] = [];
                self.lists[value.esp_account_id]['deactive'] = [];
            }
            if(value.is_active){
                self.lists[value.esp_account_id]['active'].push(value);
            } else {
                self.lists[value.esp_account_id]['deactive'].push(value);
            }
        });
    };

    self.inactiveToggle = function (){
        self.deactiveLists = [];
        angular.forEach( self.lists , function ( list , index ) {
           angular.forEach( list['deactive'] , function ( item , index ) {
               self.deactiveLists.push(item.id);
           });
        } );

    };

    self.updateListSuccessCallback = function (){
        modalService.simpleToast("List Status has been updated","bottom left");
    };

    self.somethingWentWrong = function (){
        modalService.simpleToast("Something went wrong updating List Status","bottom left");
    }

}]);
