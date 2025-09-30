<!DO
CTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monthly Report Generation Failed</title>
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
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
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
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>❌ Monthly Report Generation Failed</h1>
    </div>

    <div class="content">
        <p>Dear {{ $storeName }} Team,</p>
        
        <p>We're sorry to inform you that the automated monthly report generation for <strong>{{ $reportMonth }}</strong> has failed to complete.</p>
        
        <div class="error-box">
            <strong>Error Details:</strong><br>
            {{ $errorMessage }}
        </div>
        
        <p><strong>What happens next:</strong></p>
        <ul>
            <li>Our technical team has been automatically notified</li>
            <li>We will investigate and resolve the issue promptly</li>
            <li>Your report will be regenerated and sent to you once the issue is fixed</li>
            <li>No action is required from your side</li>
        </ul>
        
        <p>If you need your monthly report urgently, you can:</p>
        <ul>
            <li>Log into your POS Xpress dashboard</li>
            <li>Navigate to Reports → Monthly Reports</li>
            <li>Generate and download the report manually</li>
        </ul>
        
        <p>We apologize for any inconvenience this may cause. Our team is committed to ensuring reliable automated reporting for your business.</p>
        
        <p>If you have any questions or concerns, please contact our support team at support@posxpress.com</p>
        
        <p>Best regards,<br>
        The POS Xpress Team</p>
    </div>

    <div class="footer">
        <p>This is an automated message from POS Xpress.</p>
        <p>© {{ date('Y') }} POS Xpress. All rights reserved.</p>
    </div>
</body>
</html>