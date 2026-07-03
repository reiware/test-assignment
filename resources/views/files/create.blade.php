@extends('layouts.app')

@section('title', 'Upload Files')
@section('heading', 'Upload files')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            Upload PDF/DOCX
        </div>

        <div class="card-body">
            <form id="upload-form" data-max-file="10" enctype="multipart/form-data" data-action="{{ route('files.store') }}">
                <div class="mb-3">
                    <input class="form-control" type="file" name="files[]" accept=".pdf,.docx" multiple>
                    <div id="file-error" class="text-danger small mt-2"></div>
                </div>

                <button class="btn btn-primary" type="submit">
                    Upload
                </button>

                <a href="{{ route('files.index') }}" class="btn btn-outline-secondary">
                    Uploaded files
                </a>
            </form>
        </div>
    </div>
@endsection
