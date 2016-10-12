
mt2App.service( 'formValidationService' , [ function () {
    var self = this;
    self.loadFieldErrors = function (controllerScope, response ) {
        angular.forEach(response.data, function(value, key) {
            self.setFieldError( key , value, controllerScope );
        });
    };

    self.setFieldError = function (field , errorMessage, controllerScope ) {

        controllerScope.formErrors[ field ] = errorMessage;
    };

    self.resetFieldErrors = function (controllerScope) {
        controllerScope.formErrors = {};
    };
} ] );
