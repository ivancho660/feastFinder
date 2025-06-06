<?php

return [
<<<<<<< HEAD
=======

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
<<<<<<< HEAD
    */
    'defaults' => [
        'guard' => 'api',  // Cambiado a 'api' como guard por defecto
        'passwords' => 'usuarios',
=======
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
<<<<<<< HEAD
    */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'usuarios',
        ],
        'api' => [
            'driver' => 'jwt',
            'provider' => 'usuarios',
            'hash' => false,
        ],
    ],

    'aliases' => [
    // ...
    'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
    'JWTFactory' => Tymon\JWTAuth\Facades\JWTFactory::class,
],

=======
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | which utilizes session storage plus the Eloquent user provider.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
    ],

>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
<<<<<<< HEAD
    */
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        'usuarios' => [
            'driver' => 'eloquent',
            'model' => App\Models\usuarios::class,
        ],
=======
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | If you have multiple user tables or models you may configure multiple
    | providers to represent the model / table. These providers may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
<<<<<<< HEAD
    */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'usuarios' => [
            'provider' => 'usuarios',
            'table' => 'password_resets',
=======
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
<<<<<<< HEAD
    */
    'password_timeout' => 10800,
];
=======
    |
    | Here you may define the amount of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
>>>>>>> a7e2ba0908f1588fd2c377bd9e8592ad3375dc8a
