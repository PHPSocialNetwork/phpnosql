<?php

// Default is ./_database/ - However, I recommened put it in security folder on your server
// Example: outside public_html and www
define("NOSQL_DATABASE_PATH",dirname(__FILE__)."/_database/");

// Set it to Write Permission change it if you know what you doing.
define("NOSQL_PERMISSION", 0777);

// change to upgrade to new version
define("NOSQL_VERSION","release2.0");

// e-mail report problems - Bugs Tracking
define("NOSQL_EMAIL_ADMIN","khoaofgod@gmail.com");

