mt2App.controller('AppendEidController', ['$log', '$window', '$location', '$timeout', 'AppendEidApiService', '$rootScope', '$mdToast', 'formValidationService', 'modalService', function ($log, $window, $location, $timeout, AppendEidApiService, $rootScope, $mdToast, formValidationService, modalService) {
    var self = this;
    self.$location = $location;
    self.text = "File not ready";
    self.formSubmitted = true;
    self.file ="";
    self.fields = false;
    self.suppress = false;
    self.feed = false;

    self.createFile = function () {
        var data = {
            'fileName' : self.file,
            'feed' : self.feed,
            'suppress' : self.suppress,
            'fields': self.fields
        };
        AppendEidApiService.uploadList(data, self.uploadSuccessCallback,self.uploadFailCallback);
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
