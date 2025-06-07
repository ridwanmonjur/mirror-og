<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Command Error Alert</title>
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
            background-color: #dc3545;
            color: white;
            padding: 20px;
            border-radius: 5px 5px 0 0;
            text-align: center;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 0 0 5px 5px;
        }
        .error-details {
            background-color: #fff;
            padding: 15px;
            border-left: 4px solid #dc3545;
            margin: 15px 0;
        }
        .info-row {
            margin: 10px 0;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            display: inline-block;
            width: 120px;
        }
        .stack-trace {
            background-color: #f1f3f4;
            padding: 10px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            font-size: 14px;
            color: #6c757d;
        }
        .class-name {
            background-color: #007bff;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚨 Command Error Alert</h1>
        <p>First error of the day detected</p>
    </div>
    
    <div class="content">
        <div class="error-details">
            <h2>Error Details</h2>
            
            <div class="info-row">
                <span class="info-label">Class Name:</span>
                <span class="class-name">{{ $class_name }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Error Date:</span>
                {{ $error_date }}
            </div>
            
            <div class="info-row">
                <span class="info-label">Error Time:</span>
                {{ $error_time }}
            </div>
            
            <div class="info-row">
                <span class="info-label">Error Message:</span>
                <div style="margin-top: 5px; color: #dc3545; font-weight: bold;">
                    {{ $error_message }}
                </div>
            </div>
        </div>
        
        <div class="error-details">
            <h3>Stack Trace</h3>
            <div class="stack-trace">{{ $stack_trace }}</div>
        </div>
        
        <div style="background-color: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0;">
            <h4 style="color: #856404; margin-top: 0;">📋 Important Notes:</h4>
            <ul style="color: #856404; margin-bottom: 0;">
                <li>This is the <strong>first error</strong> for {{ $class_name }} today</li>
                <li>No additional emails will be sent for this command today</li>
                <li>Error count will continue to be tracked in the database</li>
                <li>Please investigate and resolve the issue as soon as possible</li>
            </ul>
        </div>
    </div>
    
    <div class="footer">
        <p><strong>System Information:</strong></p>
        <p>
            Server: {{ config('app.name', 'Laravel Application') }}<br>
            Environment: {{ config('app.env', 'production') }}<br>
            Generated: {{ now()->format('Y-m-d H:i:s T') }}
        </p>
        
        <p style="margin-top: 15px;">
            <em>This is an automated alert from your Laravel application's command monitoring system.</em>
        </p>
    </div>
</body>
</html>