mt2App.directive( 'domainListTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "=",
            "toggle"  : "&"
        } ,
        "templateUrl" : "js/templates/domain-list-table.html"
    };
} );
