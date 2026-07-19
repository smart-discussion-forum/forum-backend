<?php

return [
    // Days of no activity (User.last_active) before a user gets their
    // first automatic warning. SDD requirement #4.
    'inactivity_warning_days' => env('MODERATION_INACTIVITY_WARNING_DAYS', 14),

    // Days a user has to become active again after each automatic
    // warning before the next warning / blacklist is triggered.
    'compliance_window_days' => env('MODERATION_COMPLIANCE_WINDOW_DAYS', 7),

    // Default length of an automatic (inactivity-based) blacklist, in days.
    'blacklist_duration_days' => env('MODERATION_BLACKLIST_DURATION_DAYS', 30),

    // Number of manually-issued (Lecturer/Admin) warnings that trigger an
    // immediate blacklist. Kept separate from the automatic flow above.
    'manual_warning_limit' => env('MODERATION_MANUAL_WARNING_LIMIT', 2),
];
