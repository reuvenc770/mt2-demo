mt2App.directive( 'ymlpcampaignTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
<<<<<<< HEAD:resources/assets/js/dataexport/DataExportTableDirective.js
            "loadingflag" : "=",
            "toggleinclusion": "&",
            "changestatus": "&",
            "statuschangebuttontext": "=",
            "deleteexport": "&",
            "copyexport": "&"
=======
            "loadingflag" : "="
>>>>>>> master:resources/assets/js/ymlpmanager/YmlpCampaignTableDirective.js
        } ,
        "templateUrl" : "js/templates/ymlpcampaign-table.html"
    };
} );
