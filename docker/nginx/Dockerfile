FROM nginx:stable

ENV NGINX_ROOT /usr/src/app/public
ENV NGINX_FPM_HOST localhost
ENV NGINX_FPM_PORT 9000

# Remove default configuration files
RUN rm -f /etc/nginx/conf.d/default.conf
RUN rm -f /etc/nginx/nginx.conf

# Copy custom configuration files
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/fpm-template.conf /etc/nginx/fpm.tmpl
COPY ./docker/nginx/entrypoint.sh /usr/local/bin/entrypoint.sh

# Ensure the entrypoint script is executable
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 80
EXPOSE 80

# Use the full path to the entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
