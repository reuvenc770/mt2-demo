mt2App.controller( 'NavigationController' , [ 'NavigationApiService' , '$mdToast' , 'modalService' , function ( NavigationApiService , $mdToast , modalService ) {
    var self = this;
    self.navigation = [];
    self.orphans = [];
    self.formSubmitted = false;

    self.loadNavigation = function (){
       NavigationApiService.getPermissionsTree(self.successNavCallback,self.failNavCallback);
       NavigationApiService.getValidOrphans(self.successOrphanCallback,self.failOrphanCallback);
    };


    self.updateNavigation = function(){
        self.formSubmitted = true;
        NavigationApiService.updateNavigation(self.navigation,self.successUpdateCallback,self.failUpdateCallback)
    };

    self.successNavCallback = function (response) {
        self.navigation = response.data;
    };

    self.successOrphanCallback = function (response) {
        self.orphans = response.data;
    };

    self.successUpdateCallback = function (){
        self.formSubmitted = false;
        $mdToast.showSimple( 'Navigation has been updated, please refresh page to see new navigation' );
    };

    self.failNavCallback = function() {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to get Nav Tree' );
        modalService.launchModal();
    };

    self.failOrphanCallback = function () {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load unused routes' );
        modalService.launchModal();
    };

    self.failUpdateCallback = function (){
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to Update Navigation' );
        modalService.launchModal();
    };

} ] );
