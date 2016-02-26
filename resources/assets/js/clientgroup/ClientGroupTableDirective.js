mt2App.directive( 'clientgroupTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadchildren" : "&" ,
            "copygroup" : "&" ,
            "deletegroup" : "&" ,
            "children" : "=" ,
            "loadingflag" : "=" ,
            "copyingflag" : '=' ,
            "deletingflag" : '='
        } ,
        "templateUrl" : "js/templates/clientgroup-table.html"
    };
} );
