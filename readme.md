Turbo
=====

Turbolinks but for your PHP application; powered by [PJAX](https://github.com/defunkt/jquery-pjax).

[![Build Status](https://travis-ci.org/rcrowe/Turbo.png?branch=master)](https://travis-ci.org/rcrowe/Turbo)

Installation
------------

Turbo has only been tested installing through [Composer](http://getcomposer.org/).

Add `rcrowe\turbo` as a requirement to composer.json:

```javascript
{
    "require": {
        "rcrowe/turbo": "0.1.*"
    }
}
```

Update your packages with `composer update` or install with `composer install`.

Providers
---------

Providers enable instant usage of Turbo within different frameworks, we currently provide the following intergrations:

*Laravel*

Add `Turbo\Provider\Laravel\TurboServiceProvider` to `app/config/app.php` and your good to go


Always happy to recieve pull requests with new providers.

PJAX
----


 
License
-------

Turbo is released under the MIT public license.
