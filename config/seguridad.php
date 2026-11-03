<?php

return [
    // enforce | report-only | off  (ver App\Http\Middleware\SecurityHeaders)
    'csp' => env('CSP_MODE', 'enforce'),
];
