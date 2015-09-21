@extends('app')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<table class="table">
				<tbody>
					<tr>
						<th>Name</th>
						<td>{{ $recipe->name }}</td>
					</tr>
					<tr>
						<th>Description</th>
						<td>{{ $recipe->description }}</td>
					</tr>
					<tr>
						<th>Body</th>
						<td><pre><code>{{ $recipe->body }}</code></pre></td>
					</tr>
				</tbody>
			</table>
			{!! link_to_route('recipes.index', 'Back', [], ['class' => 'btn btn-danger']) !!}
			@if (Auth::user()->can('update.recipe'))
				{!! link_to_route('recipes.edit', 'Edit', [$recipe->id], ['class' => 'btn btn-primary']) !!}
			@endif
		</div>
	</div>
</div>
@stop
