############################################
# Base Image
############################################
FROM serversideup/php:8.3-fpm-nginx AS base

############################################
# Development Image
############################################
FROM base AS development

# Switch to root so we can do root things
USER root

# Install the intl extension with root permissions
RUN install-php-extensions zip mysqli pdo_mysql sockets soap sqlsrv pdo_sqlsrv gd intl

# Downgrade openssl
# COPY docker/openssl.cnf /etc/ssl/openssl.cnf

# Install node & npm
RUN curl -sLS https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm

# Save the build arguments as a variable
ARG USER_ID
ARG GROUP_ID

# Use the build arguments to change the UID
# and GID of www-data while also changing
# the file permissions for NGINX
RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID && \
    \
    # Update the file permissions for our NGINX service to match the new UID/GID
    docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID --service nginx

# Drop back to our unprivileged user
USER www-data

############################################
# Production Image
############################################

# Since we're calling "base", production isn't
# calling any of that permission stuff
FROM base AS production

# Copy our app files as www-data (33:33)
COPY --chown=www-data:www-data . /var/www/html
