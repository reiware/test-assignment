<p>File <strong>{{ $originalName }}</strong> was deleted.</p>
<p>Reason: {{ $reason->value === 'expired' ? 'automatically expired after 24 hours' : 'manually deleted by user' }}</p>
<p>Deleted at: {{ $deletedAt }}</p>
