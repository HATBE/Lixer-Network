<?php
    define('DB_HOST', 'localhost');
    define('DB_USERNAME', 'lixer');
    define('DB_PASSWORD', 'password');
    define('DB_NAME', 'lixer');

    define('ROOT_PATH', 'https://lixer.hatbe.ch');
    define('SITE_NAME', 'LIXER');

    define('USERNAME_REGEX', '/^(?=.{3,20}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/'); // 3-20 chars, no _ or . at beginning, no __ or _. or ._ or .., (azAZ09._), no _ or . at end
    define('PASSWORD_REGEX', '/^.{7,255}$/'); // chars from 7-255
