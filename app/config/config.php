<?php
// app/config/config.php
define('APP_NAME', 'Movie Streaming');
define('APP_URL', 'http://localhost/movie-streaming');
define('PUBLIC_PATH', APP_URL . '/public');
define('UPLOADS_PATH', PUBLIC_PATH . '/assets/uploads');
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/views');

// Session
define('SESSION_NAME', 'movie_streaming_session');
define('SESSION_LIFETIME', 86400); // 24 hours

// Socket Server
define('SOCKET_HOST', '0.0.0.0');
define('SOCKET_PORT', 8080);
