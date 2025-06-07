<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Date/Time Functions Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .function-name {
            font-family: 'Courier New', monospace;
            background-color: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        
        .result {
            font-family: 'Courier New', monospace;
            color: #007bff;
        }
        
        .timestamp {
            color: #6c757d;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Laravel Date/Time Functions Comparison</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Function</th>
                    <th>Result</th>
                    <th>Formatted (Y-m-d H:i:s)</th>
                    <th>Timezone</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dateData as $function => $result)
                <tr>
                    <td>
                        <span class="function-name">{{ $function }}</span>
                    </td>
                    <td class="result">
                        @if(is_string($result))
                            {{ $result }}
                        @else
                            {{ $result->toString() }}
                        @endif
                    </td>
                    <td>
                        @if(is_string($result))
                            {{ $result }}
                        @else
                            {{ $result->format('Y-m-d H:i:s') }}
                        @endif
                    </td>
                    <td>
                        @if(is_string($result))
                            <span class="timestamp">Database Server TZ</span>
                        @else
                            {{ $result->timezone->getName() }}
                        @endif
                    </td>
                    <td class="timestamp">
                        @if(is_string($result))
                            {{ strtotime($result) }}
                        @else
                            {{ $result->timestamp }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #e9ecef; border-radius: 4px;">
            <h3>Notes:</h3>
            <ul>
                <li><strong>DB::raw("NOW()")</strong> - Returns database server time</li>
                <li><strong>now()</strong> - Laravel helper, same as Carbon::now()</li>
                <li><strong>now()->utc()</strong> - Current time converted to UTC</li>
                <li><strong>Carbon::now()</strong> - Current time in app's default timezone</li>
                <li><strong>Carbon::now()->utc()</strong> - Current time in UTC</li>
                <li><strong>Carbon::today()</strong> - Today's date at 00:00:00</li>
                <li><strong>Carbon::today()->utc()</strong> - Today's date at 00:00:00 UTC</li>
            </ul>
            <p><small>App Timezone: {{ config('app.timezone') }}</small></p>
        </div>
    </div>
</body>
</html>