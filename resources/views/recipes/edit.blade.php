@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Edit Recipe</div>
                <div class="panel-body">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {!! Form::open(['route' => ['recipes.update', $recipe->surrogateId()->value()], 'method' => 'put', 'role' => 'form','class' => 'form-horizontal']) !!}
                        <div class="form-group required">
                            <label for="name" class="col-md-4 control-label">Name</label>
                            <div class="col-md-6">
                                {!! Form::text('name', $recipe->name()->value(), ['class' => 'form-control', 'id' => 'name']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description" class="col-md-4 control-label">Description</label>
                            <div class="col-md-6">
                                {!! Form::textarea('description', $recipe->description()->value(), ['class' => 'form-control', 'id' => 'description']) !!}
                            </div>
                        </div>
                        <div class="form-group required">
                            <label for="body" class="col-md-4 control-label">Body</label>
                            <div class="col-md-6">
                                {!! Form::textarea('body', $recipe->body()->value(), ['class' => 'form-control', 'id' => 'body', 'data-editor' => 'php']) !!}
                                <p class="help-block">You can define a <a href="http://deployer.org/docs/recipes">recipe PHP file</a> here.</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                {!! link_to_route('recipes.index', 'Cancel', [], ['class' => 'btn btn-danger']) !!}
                                {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
