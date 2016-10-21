mt2App.controller('AppendEidController', ['$log', '$window', '$location', '$timeout', 'AppendEidApiService', '$rootScope', '$mdToast', 'formValidationService', 'modalService', function ($log, $window, $location, $timeout, AppendEidApiService, $rootScope, $mdToast, formValidationService, modalService) {
    var self = this;
    self.$location = $location;
    self.file ="";

    self.fileUpload = function ($file) {
        self.file = $file.relativePath;
        AppendEidApiService.uploadList(self.file, self.uploadSuccessCallback,self.uploadFailCallback);
        var loading = angular.element( document.querySelector( '#loading' ) );
        var canvas = document.createElement("canvas");
        loading.html(canvas);
        var game = new Game(canvas);
        var food = new Food(game);
        var snake = new Snake(game, food);
        game.addEntity(food);
        game.addEntity(snake);
        game.start();
    };
    self.uploadSuccessCallback = function (response){
        if(!response.data.success) {
            var headers = response.headers();
            var blob = new Blob([response.data],{'type':"application/octet-stream"});
            var windowUrl = (window.URL || window.webkitURL);
            var downloadUrl = windowUrl.createObjectURL(blob);
            var anchor = document.createElement("a");
            anchor.href = downloadUrl;
            var fileNamePattern = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
            anchor.download = fileNamePattern.exec(headers['content-disposition'])[1].replace(/['"]+/g, '');
            document.body.appendChild(anchor);
            anchor.click();
            windowUrl.revokeObjectURL(blob);
            var loading = angular.element( document.querySelector( '#loading' ) );
            loading.html("");
        }
    }

}]);
