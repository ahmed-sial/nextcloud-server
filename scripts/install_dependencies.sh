#!/bin/bash
cd /var/www/nextcloud-staging/

# Install PHP deps using composer
if [ -f composer.json ]; then
    composer install --no-interaction --prefer-dist
fi

# Set permissions
sudo chown -R www-data:www-data /var/www/nextcloud-staging/
sudo chmod -R 755 /var/www/nextcloud-staging/
