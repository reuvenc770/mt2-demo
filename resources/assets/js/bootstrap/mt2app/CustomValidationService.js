mt2App.service( 'CustomValidationService' , [ function () {
    var self = this;

    /**
     * Use this onChange callback if a field's server errors does not disappear
     * after attempting to change the field's value.
     */
    self.onChangeResetValidity = function ( controller , form , fieldName ) {
        form[ fieldName ].$setValidity('isValid', true);

        controller.formErrors[ fieldName ] = [];
    };
} ] );
