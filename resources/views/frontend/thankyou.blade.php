<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="vh-100 d-flex justify-content-center align-items-center">
        <div>
            <div class="mb-4 text-center">
                @if(isset($success))
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-success" width="75" height="75"
                        fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                        <path
                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="75" height="75"
                        fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                        <path
                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.354 4.646a.5.5 0 1 0-.708.708L7.293 7.5 5.646 9.146a.5.5 0 1 0 .708.708L8 8.207l1.646 1.647a.5.5 0 0 0 .708-.708L8.707 7.5l1.647-1.646a.5.5 0 0 0-.708-.708L8 6.793 6.354 5.146z" />
                    </svg>
                @endif
            </div>

            <div class="text-center">
                <h1>
                    @if(isset($success))
                        Thank You!
                    @else
                        Verification Failed
                    @endif
                </h1>

                <p class="{{ isset($error) ? 'text-danger' : 'text-muted' }}">
                    {{ $success ?? $error ?? "We've sent the link to your inbox." }}
                </p>

                {{-- <a href="{{ url('/') }}" class="btn btn-primary">Back Home</a> --}}
            </div>
        </div>
    </div>
</body>

</html>
