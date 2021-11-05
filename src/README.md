# **API php website**
## **General Info**
Project PHP 
## **TestLocal**
Make sure you have installed Apache, php,composer and git.<br />
```bash
git clone https://github.com/iamsrujal/nodejs-file-stucture-express.git

cd nodejs-file-stucture-express

composer install
```
## **Config apache**
apache2.conf :
```bash
<Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride All 
        Require all granted
</Directory>
```
Linux run terminal to rewrite url <br/>
```bash
sudo a2enmod rewrite && sudo service apache2 restart
```

