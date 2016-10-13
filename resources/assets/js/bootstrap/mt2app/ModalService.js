
mt2App.service( 'modalService' , [ function () {
    var self = this;
    self.setModalLabel = function ( labelText ) {
        var modalLabel = angular.element( document.querySelector( '#pageModalLabel' ) );

        modalLabel.text( labelText );
    };

    self.setModalBody = function ( bodyText ) {
        var modalBody = angular.element( document.querySelector( '#pageModalBody' ) );

        modalBody.text( bodyText );
    };
    self.setModalBodyHtml = function ( selector ) {
        var modalBody = angular.element( document.querySelector( '#pageModalBody' ) );
        modalBody.html( angular.element(document.querySelector( selector)).html() );
    };

    self.launchModal = function (modal) {
        var modal = (typeof modal !== 'undefined') ?  modal : '#pageModal';
        $( modal ).modal('show');
    };

    self.resetModal = function () {
        self.setModalLabel( '' );
        self.setModalBody( '' );

        $( '#pageModal' ).modal('hide');
    };
} ] );
