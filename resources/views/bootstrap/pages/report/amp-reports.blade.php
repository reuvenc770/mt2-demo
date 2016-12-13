@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Reports' )

@section( 'content' )
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Reports</h3>
    </div>
    <div class="panel-body">
        <iframe style="width:100%;min-height:700px;border:none;" src="http://report.mtroute.com/v2/reports.amp"></iframe>
    </div>
</div>
@stop
