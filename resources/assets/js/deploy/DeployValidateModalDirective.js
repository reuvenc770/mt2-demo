mt2App.directive( 'deployValidateModal' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=",
            "massUpload" : "&",
            "uploadErrors" : "="
        } ,
        "templateUrl" : "js/templates/deploy-validate-modal.html"
    };
} );
