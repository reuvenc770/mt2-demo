mt2App.directive( 'domainListTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "inactive" : "&",
            "glythmap" : "=",
            "loadingflag" : "="
        } ,
        "templateUrl" : "js/templates/domain-list-table.html"
    };
} );
