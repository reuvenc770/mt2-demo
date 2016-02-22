mt2App.directive( 'clientgroupTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadchildren" : "&" ,
            "children" : "=" ,
            "loadingflag" : "="
        } ,
        "templateUrl" : "js/templates/clientgroup-table.html"
    };
} );
