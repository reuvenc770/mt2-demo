mt2App.directive( 'dbaTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "=",
            "toggle"      : "&",
            "formatBox"   : '&'
        } ,
        "templateUrl" : "js/templates/dba-table.html"
    };
} );
