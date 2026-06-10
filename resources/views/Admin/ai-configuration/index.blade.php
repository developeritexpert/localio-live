@extends('admin_layout.master')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>AI Configurations</h5>
        <a href="{{ route('ai-configurations.create') }}" class="btn btn-primary">Add Model</a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Model Name</th>
                    <th>API Key</th>
                    <th>Default</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($configs as $config)
                <tr>
                    <td>{{ $config->model_name }}</td>
                    <td>************</td>
                    <td>{{ $config->is_default ? 'Yes' : 'No' }}</td>
                    <td>
                        <form method="POST" action="{{ route('ai-configurations.destroy', $config) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if($configs->isEmpty())
                <tr><td colspan="4" class="text-center">No Configurations Found</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
