<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        /* Reset */
        body, table, td, p, a, h1, h2, h3, h4 { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
        body { margin: 0; padding: 0; background: #F3F4F6; -webkit-font-smoothing: antialiased; }
        table { border-collapse: collapse; }
        img { border: 0; display: block; }
        a { color: #1AAD94; text-decoration: none; }

        /* Layout */
        .wrap { width: 100%; background: #F3F4F6; padding: 32px 12px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 6px 24px rgba(7, 48, 87, 0.08); }
        .header { background: linear-gradient(135deg, #073057 0%, #0a4275 100%); padding: 32px 32px; text-align: left; }
        .header h1 { color: #ffffff; font-size: 22px; font-weight: 800; margin: 0; letter-spacing: -0.01em; }
        .header .tag { color: rgba(255,255,255,0.65); font-size: 11px; text-transform: uppercase; letter-spacing: 0.18em; font-weight: 600; }
        .body { padding: 36px 36px 8px 36px; color: #2C2C2C; font-size: 15px; line-height: 1.65; }
        .body h2 { color: #073057; font-size: 22px; font-weight: 700; margin: 0 0 16px 0; line-height: 1.3; }
        .body p { margin: 0 0 16px 0; }
        .body ul, .body ol { padding-left: 22px; margin: 0 0 16px 0; }
        .body a { color: #1AAD94; }

        /* Button */
        .btn-wrap { padding: 16px 0 28px 0; }
        .btn { display: inline-block; padding: 14px 28px; background: #1AAD94; color: #ffffff !important; font-weight: 700; font-size: 14px; text-transform: uppercase; letter-spacing: 0.08em; border-radius: 8px; }
        .btn-secondary { background: #073057; }

        /* Footer */
        .footer { padding: 24px 36px 32px 36px; background: #F9FAFB; border-top: 1px solid #E5E7EB; color: #6B7280; font-size: 12px; line-height: 1.6; }
        .footer a { color: #1AAD94; }
        .footer .brand { color: #073057; font-weight: 700; font-size: 13px; margin-bottom: 6px; }
        .meta { color: #9CA3AF; font-size: 11px; margin-top: 12px; }

        @media only screen and (max-width: 480px) {
            .header, .body, .footer { padding-left: 22px !important; padding-right: 22px !important; }
            .body h2 { font-size: 19px; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <table role="presentation" class="container" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td class="header">
                    <div class="tag">Powered by Jose Consulting Limited</div>
                    <h1>{{ $appName ?? config('app.name', 'JOSEOCEANJOBS') }}</h1>
                </td>
            </tr>
            <tr>
                <td class="body">
                    {!! $body !!}
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <div class="brand">{{ config('app.name', 'JOSEOCEANJOBS') }}</div>
                    <div>10 Engineering Close, off Idowu Taylor, Victoria Island, Lagos</div>
                    <div><a href="mailto:info@joseoceanjobs.com">info@joseoceanjobs.com</a> &nbsp;|&nbsp; <a href="{{ url('/') }}">{{ parse_url(url('/'), PHP_URL_HOST) }}</a></div>
                    <div class="meta">You're receiving this email because you have an account on {{ config('app.name', 'JOSEOCEANJOBS') }}. If you didn't expect this email, you can safely ignore it.</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
