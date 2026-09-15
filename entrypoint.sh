#!/bin/sh
set -e
# PHP-FPM'i arka planda, Nginx'i on planda calistir (konteynerin PID 1'i nginx olur)
php-fpm82 --daemonize
exec nginx -g 'daemon off;'
