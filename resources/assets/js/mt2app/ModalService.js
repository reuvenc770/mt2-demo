mt2App.service( 'modalService' , [ '$mdToast' , '$timeout' , function ( $mdToast , $timeout ) {
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

    self.setModalBodyRawHtml = function ( html ) {
        var modalBody = angular.element( document.querySelector( '#pageModalBody' ) );
        modalBody.html( html);
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

    self.simpleToast = function ( message , screenPosition ) {
        var defaultPosition = 'top right';

        if ( typeof( screenPosition ) == 'undefined' ) {
            screenPosition = defaultPosition;
        }

        var toast = $mdToast.simple().textContent( message ).position( screenPosition );

        $mdToast.show( toast );
    };

    self.setPopover = function ( time ) {
        var defaultTimeout = 1500;

        if ( typeof( time ) == 'undefined' ) {
            time = defaultTimeout;
        }

        $timeout( function () {
            $(function () {
                $('[data-toggle="popover"]').popover({
                    trigger:'hover',
                    template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content cmp-large-popover"></div></div>'
                });
            } );
        } , time );
    };

} ] );
