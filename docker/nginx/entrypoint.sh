#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

# Substitute environment variables in the template and output to default.conf
envsubst '$NGINX_ROOT $NGINX_FPM_HOST $NGINX_FPM_PORT' < /etc/nginx/fpm.tmpl > /etc/nginx/conf.d/default.conf

# Start Nginx
exec nginx -g 'daemon off;'
