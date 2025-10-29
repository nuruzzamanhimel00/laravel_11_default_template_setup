<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
    <style>
        /* Inline CSS for better email client compatibility */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #edf2f7;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            width: 100%;
            padding: 0;
            margin: 0;
            background-color: #edf2f7;
        }
        .content {
            max-width: 570px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 2px;
            border: 1px solid #e8e5ef;
            box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015);
        }
        .header {
            padding: 25px 0;
            text-align: center;
        }
        .logo {
            height: 75px;
            width: 75px;
        }
        .body {
            padding: 32px;
        }
        h1 {
            color: #3d4852;
            font-size: 18px;
            font-weight: bold;
            margin-top: 0;
        }
        p {
            font-size: 16px;
            line-height: 1.5em;
            margin-top: 0;
        }
        .button {
            display: inline-block;
            padding: 8px 18px;
            background-color: #2d3748;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .footer {
            text-align: center;
            padding: 32px;
            color: #b0adc5;
            font-size: 12px;
        }
        .subcopy {
            border-top: 1px solid #e8e5ef;
            margin-top: 25px;
            padding-top: 25px;
        }
        .break-all {
            word-break: break-all;
        }
    </style>
</head>
<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                            <a href="/" target="_blank" rel="noopener noreferrer">
                                <img src="{{ config('settings.site_logo') ? getStorageImage(config('settings.site_logo'),false,'logo') : getDefaultLogo()}}" class="logo" alt="Laravel Logo">
                            </a>
                        </td>
                    </tr>

                    <!-- Email Body -->
                    <tr>
                        <td class="body">
                            <h1>Hi, {{$data['user']->full_name}}</h1>
                            <p>
                                {{$data['message']}}
                            </p>
                            <div style="text-align: center">
                                <a href="{{ $data['visit_url'] }}" class="button"> Visit Now</a>
                            </div>

                            Best regards, <br>
                            {{ config('settings.site_title') ?? env('APP_NAME', 'Laravel') }}


                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td>
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="content-cell" align="center">
                                        © {{ date('Y') }}
                                        {{ config('settings.site_title') }} <span class="d-none d-sm-inline-block"> - Design & Developed By
                                            <a href="https://itclanbd.com" target="_blank">ITclan BD </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
