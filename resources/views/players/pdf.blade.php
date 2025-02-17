<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Player Details - {{ $player->first_name }} {{ $player->last_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .header p {
            font-size: 14px;
            margin: 5px 0;
        }
        .profile-image {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #8B4513;
            margin-bottom: 15px;
            border-bottom: 1px solid #8B4513;
            padding-bottom: 5px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-row {
            display: table-row;
        }
        .label {
            display: table-cell;
            font-weight: normal;
            padding: 5px 0;
            width: 150px;
        }
        .value {
            display: table-cell;
            padding: 5px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            background-color: #e5e7eb;
        }
        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
        .logo {
            width: 20px;
            height: 20px;
            margin-right: 5px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>YOUNG SILVER SPORTS CLUB</h1>
        <p>Player Profile</p>
    </div>

    @if($player->profile_picture)
    <div class="profile-image">
        <img src="{{ public_path('storage/' . $player->profile_picture) }}" alt="Profile Picture">
    </div>
    @endif

    <!-- Personal Information -->
    <div class="section">
        <div class="section-title">Personal Information</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="label">Player ID</span>
                <span class="value">{{ $player->player_id }}</span>
            </div>
            <div class="info-row">
                <span class="label">Full Name</span>
                <span class="value">{{ $player->first_name }} {{ $player->last_name }}</span>
            </div>
            @if($player->nic)
            <div class="info-row">
                <span class="label">NIC</span>
                <span class="value">{{ $player->nic }}</span>
            </div>
            @endif
            @if($player->date_of_birth)
            <div class="info-row">
                <span class="label">Date of Birth</span>
                <span class="value">{{ $player->date_of_birth->format('Y-m-d') }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Contact Information -->
    <div class="section">
        <div class="section-title">Contact Information</div>
        <div class="info-grid">
            @if($player->phone)
            <div class="info-row">
                <span class="label">Phone</span>
                <span class="value">{{ $player->phone }}</span>
            </div>
            @endif
            @if($player->address)
            <div class="info-row">
                <span class="label">Address</span>
                <span class="value">{{ $player->address }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Player Information -->
    <div class="section">
        <div class="section-title">Player Information</div>
        <div class="info-grid">
            @if($player->position)
            <div class="info-row">
                <span class="label">Position</span>
                <span class="value">{{ $player->position }}</span>
            </div>
            @endif
            @if($player->joined_date)
            <div class="info-row">
                <span class="label">Joined Date</span>
                <span class="value">{{ $player->joined_date->format('Y-m-d') }}</span>
            </div>
            @endif
            @if($player->status)
            <div class="info-row">
                <span class="label">Status</span>
                <span class="value">
                    <span class="status-badge status-{{ $player->status }}">
                        {{ ucfirst($player->status) }}
                    </span>
                </span>
            </div>
            @endif
        </div>
    </div>

    <div class="footer">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="logo">
        © {{ date('Y') }} Young Silver Sports Club
    </div>
</body>
</html> 