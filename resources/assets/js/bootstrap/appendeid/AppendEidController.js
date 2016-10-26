mt2App.controller('AppendEidController', ['$log', '$window', '$location', '$timeout', 'AppendEidApiService', '$rootScope', '$mdToast', 'formValidationService', 'modalService', function ($log, $window, $location, $timeout, AppendEidApiService, $rootScope, $mdToast, formValidationService, modalService) {
    var self = this;
    self.$location = $location;
    self.text = "File not ready";
    self.formSubmitted = true;
    self.file ="";

    self.createFile = function () {
        AppendEidApiService.uploadList(self.file, self.uploadSuccessCallback,self.uploadFailCallback);
    };

    self.uploadSuccessCallback = function(response){
        self.formSubmitted = false;
    };

    self.unlockButtonLoadFile = function($file){
        self.file = $file.relativePath;
        self.text = "Click here to generate file";
        self.formSubmitted = false;
    }

}]);
