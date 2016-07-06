mt2App.controller( 'AppController' , [ '$rootScope' , '$location' , '$window' , '$mdSidenav' , '$log' , function ( $rootScope , $location , $window , $mdSidenav , $log ) {
    var self = this;

    self.fullMenu = false;

    self.redirect = function ( redirectURL ) {
        $log.log( 'Redirecting to ' + redirectURL );
        $location.url( redirectURL );
        $window.location.href = redirectURL;
    };

    self.toggleMenu = function ( navId ) { $mdSidenav( navId ).toggle(); }

    self.openDropdownMenu = function( $mdOpenMenu , ev ) {
        originatorEv = ev;
        $mdOpenMenu( ev );
    };
} ] );
