FROM php:8.2-apache

# Installation des extensions PHP nécessaires pour la base de données
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copie de tous vos fichiers du projet dans le dossier web d'Apache
COPY . /var/www/html/

# Configuration des permissions pour Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
