@extends( 'layout.default' )

@section( 'title' , 'Creative Preview' )


@section( 'content' )

    @foreach ($creatives as $creative)
       <div md-whiteframe="1" layout="column" style="margin:20px; background:#FFFFFF" class="layout-column flex">
           <md-toolbar class="md-hue-2">
               <div class="md-toolbar-tools">

                   <h4>
                       <span>{{$creative->file_name }}</span>
                   </h4>
                   <span flex></span>
               </div>
           </md-toolbar>
           {!! $creative->creative_html !!}
       </div>
    @endforeach
@stop

@section( 'pageIncludes' )

@stop
