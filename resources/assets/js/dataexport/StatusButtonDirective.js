mt2App.directive( 'statusButton' , function () {
  return {
      "scope" : {} ,
      "controller" : function () {} ,
      "controllerAs" : "ctrl" ,
      "bindToController" : {
        "recordid": "=",
        "changestatus": "&",
        "statuschangebuttontext": "="
    } ,
    "templateUrl" : "js/templates/status-button.html"
  };
});