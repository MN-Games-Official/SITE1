<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Identity
    |--------------------------------------------------------------------------
    */
    'name'    => 'LearnAI',
    'tagline' => 'AI-Powered Education Platform',
    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | URLs & Paths
    |--------------------------------------------------------------------------
    */
    'base_url'   => getenv('APP_URL') ?: 'http://localhost:8080',
    'asset_path' => '/css',
    'js_path'    => '/js',
    'image_path' => '/images',
    'font_path'  => '/fonts',

    /*
    |--------------------------------------------------------------------------
    | User Roles
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'student' => [
            'label' => 'Student',
            'color' => 'blue',
            'home'  => '/dashboard',
        ],
        'teacher' => [
            'label' => 'Teacher',
            'color' => 'emerald',
            'home'  => '/teacher/dashboard',
        ],
        'admin' => [
            'label' => 'Administrator',
            'color' => 'purple',
            'home'  => '/admin/dashboard',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */
    'features' => [
        'ai_assistant'       => true,
        'document_upload'    => true,
        'real_time_collab'   => false,
        'analytics_advanced' => true,
        'plagiarism_check'   => true,
        'auto_grading'       => true,
        'video_lessons'      => false,
        'gamification'       => true,
        'export_reports'     => true,
        'api_access'         => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination & Limits
    |--------------------------------------------------------------------------
    */
    'per_page'           => 15,
    'max_upload_size_mb' => 25,
    'session_lifetime'   => 120, // minutes

    /*
    |--------------------------------------------------------------------------
    | Supported File Types
    |--------------------------------------------------------------------------
    */
    'allowed_uploads' => ['pdf', 'docx', 'pptx', 'txt', 'md', 'jpg', 'png'],

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    */
    'theme' => [
        'primary'   => 'indigo',
        'secondary' => 'slate',
        'accent'    => 'sky',
        'danger'    => 'rose',
        'success'   => 'emerald',
        'warning'   => 'amber',
    ],

    /*
    |--------------------------------------------------------------------------
    | External Services (placeholders — real keys come from env)
    |--------------------------------------------------------------------------
    */
    'services' => [
        'openai_model'    => 'gpt-4o',
        'analytics_id'    => getenv('ANALYTICS_ID') ?: '',
        'support_email'   => 'support@learnai.app',
    ],
];
