<?php

return [
    // Days of no communication (User.last_active) before the first
    // automatic inactivity warning is issued.
    'inactivity_first_warning_days' => env('MODERATION_INACTIVITY_FIRST_WARNING_DAYS', 3),

    // Extra days after the first automatic warning before the second
    // warning is issued (if the user is still inactive).
    'inactivity_second_warning_days' => env('MODERATION_INACTIVITY_SECOND_WARNING_DAYS', 2),

    // Extra days after the second automatic warning before the user
    // is automatically blacklisted.
    'inactivity_blacklist_after_days' => env('MODERATION_INACTIVITY_BLACKLIST_AFTER_DAYS', 1),

    // Length of an automatic (inactivity-based) blacklist, in days.
    'blacklist_duration_days' => env('MODERATION_BLACKLIST_DURATION_DAYS', 7),

    // Number of manually-issued (Lecturer/Admin) warnings that trigger an
    // immediate blacklist. Kept separate from the automatic flow above.
    'manual_warning_limit' => env('MODERATION_MANUAL_WARNING_LIMIT', 2),
];
