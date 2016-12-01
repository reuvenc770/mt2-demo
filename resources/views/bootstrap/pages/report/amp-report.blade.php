@extends( 'bootstrap.layout.default' )

@section( 'title' , $name )

@section( 'content' )
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ $name }}</h3>
    </div>
    <div class="panel-body">
        <iframe style="width:100%;min-height:700px;border:none;" src="{{ 'http://report.mtroute.com/v2/show_report.amp?norun=1&iat=0&id=' . $ampReportId }}"></iframe>
    </div>
</div>
@stop
