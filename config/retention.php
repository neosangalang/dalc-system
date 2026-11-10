<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Student Record Retention Period (Years)
    |--------------------------------------------------------------------------
    |
    | Defines the mandatory post-graduation and post-archival retention period
    | in years before records become eligible for automated or manual purging.
    | Validated against regional privacy regulations (Default: 5 Years).
    |
    */
    'period_years' => (int) env('DATA_RETENTION_YEARS', 5),

    'min_years' => 1,
    'max_years' => 10,

    /*
    |--------------------------------------------------------------------------
    | Automated Background Purge Protocol
    |--------------------------------------------------------------------------
    |
    | When enabled, Laravel Scheduler executes the purge protocol daily.
    |
    */
    'auto_purge_enabled' => (bool) env('DATA_RETENTION_AUTO_PURGE', true),

    /*
    |--------------------------------------------------------------------------
    | Notification and Logging Options
    |--------------------------------------------------------------------------
    */
    'anonymize_logs' => true,
];

