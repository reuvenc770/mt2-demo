mt2App.controller( 'AppController' , [ '$rootScope' , '$location' , '$window' , '$mdSidenav' , '$mdToast' , '$mdMedia' , '$log' , function ( $rootScope , $location , $window , $mdSidenav , $mdToast , $mdMedia , $log ) {
    var self = this;
    self.lockSideNav = true;

    self.getBaseUrl = function () {
        return $location.protocol() + '://' + $location.host() + '/';
    };

    self.mediumPageWidth = function () {
        return $mdMedia( 'gt-sm' );
    };

    self.largePageWidth = function () {
        return $mdMedia( 'gt-md' );
    };

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

    self.showToastMessage = function ( message , level ) {
        var toast = $mdToast.simple()
                            .textContent( message )
                            .position( 'top right' );

        if ( level === 'warning' || level === 'error' ) {
            toast = toast.action( 'OK' )
                        .highlightAction( true )
                        .hideDelay( false );
        }

        $mdToast.show( toast );
    };
} ] );
