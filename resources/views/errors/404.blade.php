@extends( 'layout.default' )

@section( 'title' , "404'd" )


@section( 'content' )
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <h1>404 Page Not Found</h1>
            <img class="img-responsive" src="/images/404image.png"/>
            <a href="{{redirect("/home")}}">Return Home.</a>
        </div>
    </div>
@stop
