# SnowTricks

Communitary platform on snowboard theme for the project 6 on OpenClassroom

## Requirements

For works this project require PHP7 and Composer.

## Installation

### With Git

You can use git and clone the repository on your folder.

```sh
cd /path/to/myfolder
git clone https://github.com/yanb94/blog.git
```  

### With Folder

You can download the repository at zip format and unzip it on your folder

### Install dependencies 

Install dependencies with composer.

```sh
composer update
```
### Install asset

In production environnement

```sh
cd /path/to/myproject
npm run build
```

In developpement environnement

```sh
cd /path/to/myproject
npm run dev
```

### Virtual Host

For optimal working it is recommended to use a virtual host who are pointing on the folder public.

```apache
<VirtualHost *:80>
	ServerName snowtricks
	DocumentRoot "path/to/project/public"
	<Directory  "path/to/project/public">
		AllowOverride None
        Order Allow,Deny
        Allow from All
        
        <IfModule mod_rewrite.c>
            Options -MultiViews
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^(.*)$ index.php [QSA,L]
        </IfModule>
	</Directory>
</VirtualHost>
```

## Configuration

For that project works it is necessary to add a file **.env** 

**.env**

```
###> symfony/framework-bundle ###
APP_ENV=<Your environnement 'dev' or 'prod'>
###< symfony/framework-bundle ###

###> symfony/swiftmailer-bundle ###
MAILER_URL=<Your email connection>
###< symfony/swiftmailer-bundle ###

###> doctrine/doctrine-bundle ###
DATABASE_URL=<Your database connection>
###< doctrine/doctrine-bundle ###

```

## Initialize Project

### Create DataBase

For create the database of project execute this following command
```sh
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
```

### Load the init data

For init the data of the project execute this following command

```sh
php bin\console doctrine:fixtures:load
```