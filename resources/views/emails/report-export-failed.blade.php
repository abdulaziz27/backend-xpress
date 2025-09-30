<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report Export Failed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }
        .error-box {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-left: 4px solid #dc3545;
            margin: 15px 0;
            border-radius: 4px;
        }
        .info-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #6c757d;
            margin: 15px 0;
        }
        .retry-button {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .retry-button:hover {
            background-color: #218838;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>❌ Report Export Failed</h1>
    </div>

    <div class="content">
        <p>Hello,</p>
        
        <p>We're sorry to inform you that your <strong>{{ $reportType }}</strong> report export has failed to complete.</p>
        
        <div class="info-box">
            <strong>Export Details:</strong><br>
            📋 Type: {{ $reportType }}<br>
            📄 Format: {{ $format }}<br>
            ⏰ Attempted: {{ now()->format('M j, Y \a\t g:i A') }}
        </div>

        <div class="error-box">
            <strong>Error Details:</strong><br>
            {{ $errorMessage }}
        </div>
        
        <p><strong>What you can do:</strong></p>
        <ul>
            <li>Try generating the report again with the same or different parameters</li>
            <li>Check if your date range is not too large (try smaller periods)</li>
            <li>Ensure you have sufficient permissions for the requested report type</li>
            <li>Contact support if the problem persists</li>
        </ul>
        
        <p>You can try generating the report again by logging into your POS Xpress dashboard and navigating to the Reports section.</p>
        
        <p>If you continue to experience issues, please contact our support team with the error details above.</p>
        
        <p>We apologize for any inconvenience caused.</p>
        
        <p>Best regards,<br>
        The POS Xpress Team</p>
    </div>

    <div class="footer">
        <p>This is an automated message from POS Xpress. Please do not reply to this email.</p>
        <p>For support, please contact us through your dashboard or email support@posxpress.com</p>
    </div>
</body>
</html>