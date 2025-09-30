<!DOC
TYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monthly Business Report</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }
        .metric-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            text-align: center;
        }
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .metric-label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .growth-positive {
            color: #28a745;
        }
        .growth-negative {
            color: #dc3545;
        }
        .growth-neutral {
            color: #6c757d;
        }
        .section {
            margin: 30px 0;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
        .recommendations {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .recommendation-item {
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border-radius: 6px;
            border-left: 4px solid #ffc107;
        }
        .recommendation-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 5px;
        }
        .recommendation-text {
            color: #856404;
            font-size: 14px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #6c757d;
            text-align: center;
        }
        .attachment-notice {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Monthly Business Report</h1>
        <p>{{ $reportMonth->format('F Y') }} - {{ $store->name }}</p>
    </div>

    <div class="content">
        <p>Dear Store Owner,</p>
        
        <p>Your comprehensive monthly business report for <strong>{{ $reportMonth->format('F Y') }}</strong> is ready! Here's a quick overview of your business performance:</p>

        <div class="section">
            <div class="section-title">📈 Executive Summary</div>
            
            <div class="summary-grid">
                <div class="metric-card">
                    <div class="metric-value">${{ number_format($executiveSummary['revenue']['current'], 2) }}</div>
                    <div class="metric-label">Total Revenue</div>
                    <div class="growth-{{ $executiveSummary['revenue']['growth'] > 0 ? 'positive' : ($executiveSummary['revenue']['growth'] < 0 ? 'negative' : 'neutral') }}">
                        {{ $executiveSummary['revenue']['growth'] > 0 ? '+' : '' }}{{ number_format($executiveSummary['revenue']['growth'], 1) }}%
                    </div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($executiveSummary['orders']['current']) }}</div>
                    <div class="metric-label">Total Orders</div>
                    <div class="growth-{{ $executiveSummary['orders']['growth'] > 0 ? 'positive' : ($executiveSummary['orders']['growth'] < 0 ? 'negative' : 'neutral') }}">
                        {{ $executiveSummary['orders']['growth'] > 0 ? '+' : '' }}{{ number_format($executiveSummary['orders']['growth'], 1) }}%
                    </div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-value">${{ number_format($executiveSummary['profit']['current'], 2) }}</div>
                    <div class="metric-label">Net Profit</div>
                    <div class="growth-{{ $executiveSummary['profit']['growth'] > 0 ? 'positive' : ($executiveSummary['profit']['growth'] < 0 ? 'negative' : 'neutral') }}">
                        {{ $executiveSummary['profit']['growth'] > 0 ? '+' : '' }}{{ number_format($executiveSummary['profit']['growth'], 1) }}%
                    </div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-value">${{ number_format($executiveSummary['orders']['average_order_value'], 2) }}</div>
                    <div class="metric-label">Avg Order Value</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">🎯 Key Performance Indicators</div>
            
            <div class="summary-grid">
                <div class="metric-card">
                    <div class="metric-value">${{ number_format($kpis['revenue_per_day'], 2) }}</div>
                    <div class="metric-label">Revenue per Day</div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($kpis['orders_per_day'], 1) }}</div>
                    <div class="metric-label">Orders per Day</div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($kpis['customer_retention_rate'], 1) }}%</div>
                    <div class="metric-label">Customer Retention</div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($kpis['unique_customers']) }}</div>
                    <div class="metric-label">Unique Customers</div>
                </div>
            </div>
        </div>

        @if(!empty($recommendations))
        <div class="section">
            <div class="section-title">💡 Key Recommendations</div>
            
            <div class="recommendations">
                @foreach(array_slice($recommendations, 0, 3) as $recommendation)
                <div class="recommendation-item">
                    <div class="recommendation-title">{{ $recommendation['title'] }} - {{ ucfirst($recommendation['priority']) }} Priority</div>
                    <div class="recommendation-text">{{ $recommendation['description'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="attachment-notice">
            📎 <strong>Complete Report Attached</strong><br>
            The full detailed report with charts, analytics, and actionable insights is attached as a PDF file.
        </div>

        <p>This automated report includes:</p>
        <ul>
            <li>📊 Detailed financial performance analysis</li>
            <li>📈 Sales trends and forecasting</li>
            <li>🛍️ Product performance insights</li>
            <li>👥 Customer behavior analytics</li>
            <li>💰 Profitability analysis</li>
            <li>🎯 Actionable business recommendations</li>
        </ul>

        <p>We recommend reviewing the complete report to identify opportunities for growth and optimization.</p>
        
        <p>If you have any questions about your report or need assistance with implementing the recommendations, please don't hesitate to contact our support team.</p>
        
        <p>Best regards,<br>
        The POS Xpress Analytics Team</p>
    </div>

    <div class="footer">
        <p>This report was automatically generated on {{ $reportData['generated_at']->format('M j, Y \a\t g:i A') }}</p>
        <p>© {{ date('Y') }} POS Xpress. All rights reserved.</p>
    </div>
</body>
</html>