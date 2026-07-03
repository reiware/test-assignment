<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UploadFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $extensions = $this->allowedExtensions();

        return [
            'files' => ['required', 'array', 'min:1', 'max:10'],
            'files.*' => [
                'required',
                File::types(config('files.allowed_extensions'))
                    ->max(config('files.max_upload_size_kb')),
            ],
        ];
    }

    public function messages(): array
    {
        $maxMb = (int) (config('files.max_upload_size_kb') / 1024);
        $extensions = strtoupper(implode(', ', config('files.allowed_extensions', [])));

        return [
            'files.required' => 'Please select at least one file.',
            'files.array' => 'Invalid upload request.',
            'files.min' => 'Please select at least one file.',
            'files.max' => 'You can upload up to 10 files at once.',

            'files.*.required' => 'Please select a valid file.',
            'files.*.file' => 'One of the selected files is invalid.',
            'files.*.uploaded' => "File upload failed. The file may be larger than {$maxMb} MB.",
            'files.*.max' => "Each file must not be larger than {$maxMb} MB.",
            'files.*.mimes' => "Only {$extensions} files are allowed.",
            'files.*.extensions' => "Only {$extensions} files are allowed.",
        ];
    }

    public function attributes(): array
    {
        return [
            'files' => 'files',
            'files.*' => 'file',
        ];
    }

    private function allowedExtensions(): string
    {
        return implode(',', config('files.allowed_extensions'));
    }
}
