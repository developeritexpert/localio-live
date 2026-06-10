@extends('user_layout.master')

@section('content')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (window.opener) {
            // Send success message with redirect URL to the main window
            window.opener.postMessage({
                status: 'success',
                redirectUrl: "{{ $redirectUrl }}"
            }, window.location.origin);

            // Close the popup after a short delay to ensure message is sent
            setTimeout(() => {
                window.close();
            }, 100);
        } else {
            // If no opener (no popup), just redirect directly
            window.location.href = "{{ $redirectUrl }}";
        }
    });
</script>
@endsection
