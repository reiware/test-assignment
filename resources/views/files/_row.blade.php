<tr id="file-{{ $file->id }}">
    <td class="text-break" title="{{ $file->original_name }}">
        {{ pathinfo($file->original_name, PATHINFO_FILENAME) }}
    </td>
    <td>{{ strtoupper($file->extension) }}</td>
    <td>{{ number_format($file->size / 1024, 1) }} KB</td>
    <td>{{ $file->expires_at->diffForHumans() }}</td>
    <td class="text-end">
        <a href="{{ route('files.download', $file) }}" class="btn btn-sm btn-outline-secondary">
            Download
        </a>
        <button class="btn btn-sm btn-outline-danger delete-file"
                data-url="{{ route('files.destroy', $file) }}" data-id="{{ $file->id }}">
            Delete
        </button>
    </td>
</tr>
