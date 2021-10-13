# MarlonSite
Marlon's Pancit

**install Bagisto from your console.**

##### Execute these commands below, in order

~~~
1. composer create-project bagisto/bagisto <DIR NAME>
~~~

~~~
2. php artisan bagisto:install
~~~


**To execute Bagisto**:

##### On server:

Warning: Before going into production mode we recommend you uninstall developer dependencies.
In order to do that, run the command below:

> composer install --no-dev

~~~
Open the specified entry point in your hosts file in your browser or make an entry in hosts file if not done.
~~~

##### On local:

~~~
php artisan serve
~~~


**How to log in as admin:**

> *http(s)://example.com/admin/login*

~~~
email:admin@example.com
password:admin123
~~~

**DATABASE**
create database utf8_general_ci