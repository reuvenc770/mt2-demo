mt2App.controller( 'NavigationController' , [ 'NavigationApiService' , '$mdToast' , '$mdDialog' , '$log' , function ( NavigationApiService , $mdToast , $mdDialog , $log ) {
    var self = this;
    self.navigation = [];
    self.orphans = [];

    self.loadNavigation = function (){
       NavigationApiService.getPermissionsTree(self.successNavCallback,self.failNavCallback);
       NavigationApiService.getValidOrphans(self.successOrphanCallback,self.failOrphanCallback);
    };


    self.updateNavigation = function(){
        NavigationApiService.updateNavigation(self.navigation,self.successUpdateCallback,self.failNavCallback)
    }

    self.successNavCallback = function (response) {
        self.navigation = response.data;
    };

    self.successOrphanCallback = function (response) {
        self.orphans = response.data;
    };

    self.successUpdateCallback = function (){

    }
} ] );
