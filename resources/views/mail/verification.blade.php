@component('mail::message')
    # Your Dummy API Key

    Thanks for requesting access to the Dummy API.

    **Your verification code:** `{{ $secret }}`
    **Your API key:** `{{ $token }}`

    Please verify your code by calling the `/api-key/verify` endpoint.

    > Do **not** lose this key. If you do, request a new one.

    Thanks,<br>
    The Dummy API Team
@endcomponent
