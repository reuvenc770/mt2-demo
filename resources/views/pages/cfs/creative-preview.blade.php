@extends( 'layout.default' )

@section( 'title' , 'Creative Preview' )

@section( 'content' )

    @foreach ($creatives as $creative)
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title">{{$creative->file_name }}</div>
            </div>
            <div class="panel-body">
                {!! $creative->creative_html !!}
            </div>
        </div>
    @endforeach
@stop