<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Service Account
    |--------------------------------------------------------------------------
    |
    | Path to your Firebase service account JSON file.
    |
    */

    'credentials' => storage_path(
        'app/firebase/firebase-service-account.json'
    ),

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID'),

];