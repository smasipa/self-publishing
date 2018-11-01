<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/* -------------------------------Custom----------------------------------- */

defined('DS')              OR define('DS', DIRECTORY_SEPARATOR);

// payfast 
defined('PF_USER_AGENT') or define('PF_USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
defined('PF_TIMEOUT') or define('PF_TIMEOUT', 400);
defined('PAYFAST_SANDBOX_MODE') or define('PAYFAST_SANDBOX_MODE', false);
defined('PAYFAST_PASS_PHRASE') or define('PAYFAST_PASS_PHRASE', null);
//defined('PAYFAST_PASS_PHRASE') or define('PAYFAST_PASS_PHRASE', '');
// Actual account
defined('MERCHANT_ID') or define('MERCHANT_ID', '');
defined('MERCHANT_KEY') or define('MERCHANT_KEY', '');
// Test account
/*defined('MERCHANT_ID') or define('MERCHANT_ID', '');
defined('MERCHANT_KEY') or define('MERCHANT_KEY', '');*/

// Source Code Dirs
defined('MODELS_DIR') OR define('MODELS_DIR', APPPATH.'models'.DS);

defined('INTERFACES_DIR') OR define('INTERFACES_DIR', MODELS_DIR.'interfaces'.DS);

// Source Code Dirs

defined('UPLOADS_PATH')    OR define('UPLOADS_PATH', dirname(dirname(__FILE__)));
// defined('UPLOADS_DIR')     OR define('UPLOADS_DIR', './uploads');

defined('BASE_URL')  	   OR define('BASE_URL', 'http://gamalami.com/');

// defined('UPLOADS_DIR')     OR define('UPLOADS_DIR', MAIN_APP_DIR . 'uploads');
defined('UPLOADS_DIR')     OR define('UPLOADS_DIR', dirname(APPPATH) . DS . 'uploads');

defined('DOCUMENTS_PATH')  OR define('DOCUMENTS_PATH', UPLOADS_PATH . DS . 'Documents' . DS);
defined('DOCUMENTS_DIR')   OR define('DOCUMENTS_DIR', UPLOADS_DIR . DS . 'Documents' . DS);

defined('PUBLICATIONS_DIR')OR define('PUBLICATIONS_DIR', DOCUMENTS_DIR . 'publications' . DS);
defined('BOOKS_DIR')       OR define('BOOKS_DIR', DOCUMENTS_DIR . 'books' . DS);
defined('WRITERS_DOCS_DIR')OR define('WRITERS_DOCS_DIR', DOCUMENTS_DIR . 'writers_verification' . DS);

defined('IMAGES_PATH')   OR define('IMAGES_PATH', dirname(dirname(__FILE__)) . DS .'assets' . DS . 'uploads');
defined('IMAGES_DIR')   OR define('IMAGES_DIR', '.'.DS.'assets'.DS.'uploads'.DS);
defined('THUMBS_DIR')   OR define('THUMBS_DIR',IMAGES_DIR . 'thumbs/');
defined('THUMBS_PATH')   OR define('THUMBS_PATH',IMAGES_PATH . DS .'thumbs');

defined('THUMBS_SMALL_PATH')   OR define('THUMBS_SMALL_PATH', THUMBS_PATH . DS .'small');
defined('THUMBS_LARGE_PATH')   OR define('THUMBS_LARGE_PATH', THUMBS_PATH . DS .'large');

defined('THUMBS_SMALL_DIR')   OR define('THUMBS_SMALL_DIR', THUMBS_DIR . 'small'.DS);
defined('THUMBS_LARGE_DIR')   OR define('THUMBS_LARGE_DIR', THUMBS_DIR . 'large'.DS);

defined('ACCESS_LOGIN')   OR define('ACCESS_LOGIN', "login");
defined('ACCESS_GET_APPROVED')   OR define('ACCESS_GET_APPROVED', "get_approved");
defined('ACCESS_PREMIUM_MEMBER')   OR define('ACCESS_PREMIUM_MEMBER', "go_premium");
defined('ACCESS_PAGE_NOT_FOUND')   OR define('ACCESS_PAGE_NOT_FOUND', "404");
defined('ACCESS_FILE_NOT_FOUND')   OR define('ACCESS_FILE_NOT_FOUND', "file");
defined('ACCESS_RENEW_ACCOUNT')   OR define('ACCESS_RENEW_ACCOUNT', "renew_account");
defined('ACCESS_BLOCKED_ITEM')   OR define('ACCESS_BLOCKED_ITEM', "blocked_item");


defined('ADMIN_CELLPHONE')   OR define('ADMIN_CELLPHONE', "011 123 45678");
defined('ADMIN_EMAIL')   OR define('ADMIN_EMAIL', "admin@gamalami.com");
defined('SUPPORT_EMAIL')   OR define('SUPPORT_EMAIL', "");
defined('SUPPORT_CELLPHONE')   OR define('SUPPORT_CELLPHONE', "011 123 45678");


//XML
defined('SITEMAP_XML_DIR')   OR define('SITEMAP_XML_DIR', dirname(APPPATH).DS.'xml'.DS.'inits'.DS); 

defined('SITEMAP_SIZE_LIMIT')   OR define('SITEMAP_SIZE_LIMIT', 7*1024*1024); //7mb
// defined('SITEMAP_SIZE_LIMIT')   OR define('SITEMAP_SIZE_LIMIT', 200);

// /XML


/* -------------------------------/Custom----------------------------------- */

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
