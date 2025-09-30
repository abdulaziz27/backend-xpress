<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report Export Ready</title>
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
            background-color: #f8f9fa;
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
        .download-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .download-button:hover {
            background-color: #0056b3;
        }
        .info-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin: 15px 0;
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
        <h1>📊 Your Report is Ready!</h1>
    </div>

    <div class="content">
        <p>Hello,</p>
        
        <p>Great news! Your <strong>{{ $reportType }}</strong> report has been successfully generated and is ready for download.</p>
        
        <div class="info-box">
            <strong>Report Details:</strong><br>
            📋 Type: {{ $reportType }}<br>
            📄 Format: {{ $format }}<br>
            📁 File: {{ $fileName }}<br>
            ⏰ Generated: {{ now()->format('M j, Y \a\t g:i A') }}
        </div>

        <p>Click the button below to download your report:</p>
        
        <a href="{{ $downloadUrl }}" class="download-button">
            📥 Download Report
        </a>
        
        <p><strong>Important:</strong> This download link will expire on <strong>{{ $expiresAt }}</strong>. Please download your report before this date.</p>
        
        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
        
        <p>Best regards,<br>
        The POS Xpress Team</p>
    </div>

    <div class="footer">
        <p>This is an automated message from POS Xpress. Please do not reply to this email.</p>
        <p>If you're having trouble with the download link, copy and paste this URL into your browser: {{ $downloadUrl }}</p>
    </div>
</body>
</html>