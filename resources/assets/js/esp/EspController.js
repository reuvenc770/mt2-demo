mt2App.controller( 'espController' , [ '$window' , '$location' , '$timeout' , '$sce' , '$log' , 'EspApiService' , function ( $window , $location , $timeout , $sce ,$log , EspApiService ) {
    var self = this;

    self.headers = [ '' , 'ID' , 'ESP' , 'Account' , 'Created' , 'Updated' ];
    self.accounts = [];

    self.currentAccount = { "espId" : "" , "id" : "" , "accountName" : "" , "key1" : "" , "key2" : "" };

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/esp\/edit\/(\d{1,})/ );

        EspApiService.getAccount( pathMatches[ 1 ] , function ( response ) {
            self.currentAccount.id = response.data.id;
            self.currentAccount.accountName = response.data.account_name;
            self.currentAccount.key1 = response.data.key_1;
            self.currentAccount.key2 = response.data.key_2;
        } )
    }

    self.loadAccounts = function () {
        EspApiService.getAccounts( function ( response ) {
            self.accounts = response.data;
        } );
    };

    self.viewAdd = function () {
        $window.location.href = "esp/create";
    };

    self.viewEdit = function () {
        $log.log( self.recordid );
        //$window.location.href = "esp/edit/"
    };

    self.saveNewAccount = function () {
        EspApiService.saveNewAccount( self.currentAccount );
    };

    self.editAccount = function () {
        EspApiService.editAccount( self.currentAccount );
    }
} ] );
