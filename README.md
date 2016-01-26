 **Jedy CMS Multi-language**
===================
 Jedy CMS Multi-language is created with **Symfony 3** 

----------

**Get started**
-------------

**Installation**

 - <i class="icon-download"></i> Clone the repository from GitHub.  

```
$ git clone git@github.com:JuanLuisGarciaBorrego/jedy.git <path-to-install>
$ cd <path-to-install>
```
- You still need Composer to get the dependencies
```
$ composer install
```
- Set up the Database
```
$ php bin/console doctrine:database:create
$ php bin/console doctrine:schema:create
$ php bin/console doctrine:fixtures:load
```
- Configure a Webserver
```
$ php bin/console server:run
```
- And then access via browser: 
```
http://127.0.0.1:8000 
```

**Configuration**

 - Definition of the main language
```
#bin/config.yml

parameters:
    #Definition of the main language
    locale_active: es
    
    #Translations, insert the locale language separated by | 
    app_locales: es|en|fr
```


> **Note:**

> - At the moment only it has http basic security. You can expand using FOSUserbundle or other
> - It has not set any Wysiwyg editor yet.

**Screenshots**

Public: 
```
http://127.0.0.1:8000/
```
<img src="https://raw.githubusercontent.com/JuanLuisGarciaBorrego/jedy/develop/Resources/doc/images/public_home.png" alt="Jedy CMS Multi-language Home public" align="right" />

Admin: 
```
http://127.0.0.1:8000/{locale}/admin 
```

<img src="https://raw.githubusercontent.com/JuanLuisGarciaBorrego/jedy/develop/Resources/doc/images/admin-contents.png" alt="Jedy CMS Multi-language Admin contents" align="right" />


<img src="https://raw.githubusercontent.com/JuanLuisGarciaBorrego/jedy/develop/Resources/doc/images/admin_content_translation.png" alt="Jedy CMS Multi-language Translations content" align="right" />


