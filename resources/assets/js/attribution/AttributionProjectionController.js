mt2App.controller( 'AttributionProjectionController' , [ 'AttributionApiService' , 'ClientApiService' , '$log' , function ( AttributionApiService , ClientApiService , $log ) {
    var self = this;

    self.initChart = function () {
        google.charts.load('current', {packages: ['corechart']});
        google.charts.setOnLoadCallback( self.loadChart );
    };

    self.loadChart = function () {
        var liveRev = google.visualization.arrayToDataTable( [
            [ 'Client Name' , 'Live Revenue' ] ,
            [ 'Client 1' , 10000 ] ,
            [ 'Client 2' , 20000 ] ,
            [ 'Client 3' , 5000 ] ,
            [ 'Client 4' , 7000 ] ,
            [ 'Client 5' , 15000 ] ,
        ] );

        var modelRev = google.visualization.arrayToDataTable( [
            [ 'Client Name' , 'Model Revenue' ] ,
            [ 'Client 1' , 10000 ] ,
            [ 'Client 2' , 15000 ] ,
            [ 'Client 3' , 10000 ] ,
            [ 'Client 4' , 6000 ] ,
            [ 'Client 5' , 16000 ] ,
        ] );

        var options = {
            diff: {
                newData: {
                    widthFactor: 0.3 ,
                    opacity: 1 ,
                    color: '#4285F4'
                } 
            } ,
            legend: { position: 'top' }
        };

        var colChartDiff = new google.visualization.BarChart( document.getElementById( 'projectionChart' ) );

        var diffData = colChartDiff.computeDiff( liveRev , modelRev );

        colChartDiff.draw( diffData , options );
    };

    self.loadRecords = function () {
    
    };
} ] );
