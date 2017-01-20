
mt2App.service( 'formValidationService' , [ function () {
    var self = this;
    self.loadFieldErrors = function (controllerScope, response ) {
        angular.forEach(response.data, function(value, key) {
            self.setFieldError(controllerScope,key , value);
        });
    };

    self.setFieldError = function (controllerScope, field , errorMessage) {

        controllerScope.formErrors[ field ] = errorMessage;
    };

    self.resetFieldErrors = function (controllerScope) {
        controllerScope.formErrors = {};
    };
} ] );
