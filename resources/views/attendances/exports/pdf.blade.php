<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .event-details {
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .summary-box {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .status {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
        .status-present {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-absent {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .status-late {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-excused {
            background-color: #e0e7ff;
            color: #3730a3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Event Attendance Report</h1>
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <div class="event-details">
        <h2>Event Details</h2>
        <table>
            <tr>
                <th>Title</th>
                <td>{{ $event->title }}</td>
            </tr>
            <tr>
                <th>Type</th>
                <td>{{ ucfirst($event->type) }}</td>
            </tr>
            <tr>
                <th>Date & Time</th>
                <td>{{ $event->start_time->format('M d, Y h:i A') }} - {{ $event->end_time->format('h:i A') }}</td>
            </tr>
            <tr>
                <th>Location</th>
                <td>{{ $event->location }}</td>
            </tr>
        </table>
    </div>

    @foreach($attendanceStats as $type => $stats)
        <div class="section">
            <h2>{{ ucfirst($type) }} Attendance</h2>
            
            <div class="summary-box">
                <p><strong>Total Attendees:</strong> {{ $stats['total'] }}</p>
                <p><strong>Present:</strong> {{ $stats['present'] }}</p>
                <p><strong>Attendance Rate:</strong> {{ $stats['rate'] }}%</p>
            </div>

            @if($stats['details']->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['details'] as $attendance)
                            <tr>
                                <td>{{ $attendance->attendee->first_name }} {{ $attendance->attendee->last_name }}</td>
                                <td>
                                    <span class="status status-{{ $attendance->status }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td>{{ $attendance->check_in_time ? $attendance->check_in_time->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->check_out_time ? $attendance->check_out_time->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->remarks ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No attendance records found.</p>
            @endif
        </div>
    @endforeach

    @if(!empty($filters['type']) || !empty($filters['status']) || !empty($filters['search']))
        <div class="section">
            <h2>Applied Filters</h2>
            <ul>
                @if(!empty($filters['type']))
                    <li>Type: {{ ucfirst($filters['type']) }}</li>
                @endif
                @if(!empty($filters['status']))
                    <li>Status: {{ ucfirst($filters['status']) }}</li>
                @endif
                @if(!empty($filters['search']))
                    <li>Search: {{ $filters['search'] }}</li>
                @endif
            </ul>
        </div>
    @endif
</body>
</html> 