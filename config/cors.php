<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CORS Paths
    |--------------------------------------------------------------------------
    |
    | Here you may specify which paths in your application should respond to
    | CORS. The '*' character is supported, for example to allow all routes.
    |
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Adjust the paths where CORS should apply.

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | These are the HTTP methods that are allowed for CORS requests.
    | By default, this will allow all methods. You can specify specific methods if needed.
    |
    */
    'allowed_methods' => ['*'], // Allow all methods like GET, POST, PUT, DELETE, etc.

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Here you can specify which origins are allowed to make requests. You can
    | use a wildcard '*' to allow all origins. In production, you should
    | limit this to specific domains for security reasons.
    |
    */
    'allowed_origins' => ['http://dam-fe.test:3000', 'http://localhost:8080', 'protalentis.eu'], // Allow all origins, change this to specific origins in production.

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | If you want to allow more flexible origin patterns, you can use
    | regular expressions here to match origin requests.
    |
    */
    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | These are the HTTP headers that are allowed in requests. By default,
    | this allows all headers. You can specify specific headers if needed.
    |
    */
    'allowed_headers' => ['*'], // Allow all headers.

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | These are the headers that are exposed to the browser in the response.
    |
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | This value sets how long the response from a preflight request
    | can be cached by the browser, in seconds.
    |
    */
    'max_age' => 0, // No max age for preflight caching.

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Indicates whether the response to the request can be exposed when
    | credentials (cookies, HTTP authentication, client-side SSL certificates) are present.
    |
    */
    'supports_credentials' => true, // Set to true if you're using cookies or authentication.
];
