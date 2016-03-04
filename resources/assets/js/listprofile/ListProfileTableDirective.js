mt2App.directive( 'listprofileTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "=" ,
            "copy" : "&" ,
            "delete" : "&"
        } ,
        "templateUrl" : "js/templates/list-profile-table.html"
    };
} );
