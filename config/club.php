<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Club Information
    |--------------------------------------------------------------------------
    |
    | Basic information about the sports club.
    |
    */

    'name' => 'Young Silver Sports Club',
    'short_name' => 'YSSC',
    'established' => '1967',
    'email' => 'info@yssc.com',
    'phone' => '+94 XX XXX XXXX',
    'address' => [
        'line1' => '',
        'line2' => '',
        'city' => '',
        'state' => '',
        'postal_code' => '',
        'country' => 'Sri Lanka',
    ],

    /*
    |--------------------------------------------------------------------------
    | Club Settings
    |--------------------------------------------------------------------------
    |
    | Various settings for club operations.
    |
    */

    'currency' => 'LKR',
    'timezone' => 'Asia/Colombo',
    'date_format' => 'Y-m-d',
    'time_format' => 'H:i:s',

    /*
    |--------------------------------------------------------------------------
    | Club Features
    |--------------------------------------------------------------------------
    |
    | Enable/disable various features of the system.
    |
    */

    'features' => [
        'events' => true,
        'attendance' => true,
        'finance' => true,
        'reports' => true,
        'sponsors' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Club Roles
    |--------------------------------------------------------------------------
    |
    | Define various roles in the club.
    |
    */

    'roles' => [
        'admin' => 'Administrator',
        'staff' => 'Staff Member',
        'member' => 'Club Member',
        'player' => 'Player',
    ],

    /*
    |--------------------------------------------------------------------------
    | Member Types
    |--------------------------------------------------------------------------
    |
    | Different types of memberships available.
    |
    */

    'member_types' => [
        'regular' => 'Regular Member',
        'lifetime' => 'Lifetime Member',
        'honorary' => 'Honorary Member',
        'student' => 'Student Member',
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction Categories
    |--------------------------------------------------------------------------
    |
    | Categories for financial transactions.
    |
    */

    'transaction_categories' => [
        'membership_fee' => 'Membership Fee',
        'sponsorship' => 'Sponsorship',
        'donation' => 'Donation',
        'event_fee' => 'Event Fee',
        'salary' => 'Salary',
        'allowance' => 'Allowance',
        'equipment' => 'Equipment',
        'maintenance' => 'Maintenance',
        'utilities' => 'Utilities',
        'other' => 'Other',
    ],
];