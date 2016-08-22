mt2App.directive( 'proxyTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "=",
            "toggle"  : "&"
        } ,
        "templateUrl" : "js/templates/proxy-table.html"
    };
} );
