mt2App.controller( 'ListProfileController' , [ 'ListProfileApiService' , 'ClientGroupApiService' , 'IspApiService', '$mdToast' , '$log' , function ( ListProfileApiService , ClientGroupApiService , IspApiService , $mdToast , $log ) {
    var self = this;

    var keycodeEnter = 13 ;
    var keycodeComma = 188 ;
    var keycodeTab = 9 ;
    self.mdChipSeparatorKeys = [ keycodeEnter , keycodeComma , keycodeTab ];

    self.current = {
        'globalSupp' : '' ,
        'listSupp' : '' ,
        'offerSupp' : '' ,
        'cities': [] ,
        'zips' : [] ,
        'states' : [],
        'actionRanges' : {
            'deliverable' : { 'min' : 0 , 'max' : 0 },
            'opener' : { 'min' : 0 , 'max' : 0 , 'multiaction' : 1 },
            'clicker' : { 'min' : 0 , 'max' : 0 , 'multiaction' : 1 },
            'converter' : { 'min' : 0 , 'max' : 0 , 'multiaction' : 1 }
        }
    };

} ] );
