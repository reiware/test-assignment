@extends('layouts.app')

@section('title', 'Uploaded Files')
@section('heading', 'Uploaded files')

@section('content')
    <div class="card">
        <div class="card-header">
            Uploaded files
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Expires</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>

                    <tbody id="files-table">
                    @forelse ($files as $file)
                        @include('files._row', ['file' => $file])
                    @empty
                        @include('files._empty')
                    @endforelse
                    </tbody>

                    <template id="empty-row-template">
                        @include('files._empty')
                    </template>
                </table>

                {{ $files->links() }}
            </div>
        </div>
    </div>
@endsection
