mt2App.directive( 'dbaTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "=",
            "toggle"      : "&"
        } ,
        "templateUrl" : "js/templates/dba-table.html"
    };
} );
