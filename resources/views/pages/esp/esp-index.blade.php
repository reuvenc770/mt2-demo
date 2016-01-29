@extends( 'layout.default' )

@section( 'title' , 'ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1>ESP</h1></div>
</div>

<div class="row">
    <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn"><span class="glyphicon glyphicon-plus"></span> Add ESP Account</button>
</div>

<div class="row">
    <div class="col-xs-12">
        <div id="mtTableContainer" class="table-responsive">
            <esp-table></esp-table>

<!-- COMMENT TOP
            <table class="table table-striped table-bordered table-hover text-center">
                <thead>
                    <tr>
                        <th></th>
                        <th class="text-center">ESP</th>
                        <th class="text-center">Account</th>
                        <th class="text-center">Active</th>
                        <th class="text-center">Account Manager</th>
                        <th class="text-center">Last Processed</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="text-right">
                            <button type="button" class="btn btn-danger btn-xs" title="Deactivate"><span class="glyphicon glyphicon-off"></span> <span class="hidden-xs">Deactivate</span></button>
                            <button type="button" class="btn btn-primary btn-xs" title="Edit"><span class="glyphicon glyphicon-pencil"></span> <span class="hidden-xs">Edit</span></button>
                        </td>
                        <td>BlueHornet</td>
                        <td>BH001</td>
                        <td class="bg-success">Active</td>
                        <td>Amanda Shun</td>
                        <td>2016-01-28 14:22:56</td>
                    </tr>
                    <tr>
                        <td class="text-right">
                            <button type="button" class="btn btn-success btn-xs" title="Activate"><span class="glyphicon glyphicon-off"></span> <span class="hidden-xs">Activate</span></button>
                            <button type="button" class="btn btn-primary btn-xs" title="Edit"><span class="glyphicon glyphicon-pencil"></span> <span class="hidden-xs">Edit</span></button>
                        </td>
                        <td>BlueHornet</td>
                        <td>BH002</td>
                        <td class="bg-danger">Inactive</td>
                        <td>Amanda Shun</td>
                        <td>2016-01-28 15:12:10</td>
                    </tr>
                    <tr>
                        <td class="text-right">
                            <button type="button" class="btn btn-danger btn-xs" title="Deactivate"><span class="glyphicon glyphicon-off"></span> <span class="hidden-xs">Deactivate</span></button>
                            <button type="button" class="btn btn-primary btn-xs" title="Edit"><span class="glyphicon glyphicon-pencil"></span> <span class="hidden-xs">Edit</span></button>
                        </td>
                        <td>BlueHornet</td>
                        <td>BH003</td>
                        <td class="bg-success">Active</td>
                        <td>Amanda Shun</td>
                        <td>2016-01-28 15:33:20</td>
                    </tr>
                </tbody>
            </table>
COMMENT BOTTOM -->

        </div>
    </div>
</div>
@stop
