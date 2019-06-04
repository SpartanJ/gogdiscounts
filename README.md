GOG Discounts
=============

This little script will allow you to pull the current discounts from GOG with
the `import.php` file and then view the discounts with a simple interface
in the `index.php`.

The code is awful, it shouldn't be taken seriously, it was a two hours
project just to help me find good discounts on GOG.

Requirements: PHP7 ( PDO+pgsql ) and PostgreSQL 9.4 and up ( requires jsonb support )
with any HTTP Server with PHP7 support ( apache2, nginx, etc ).

Installation:
`db.sql` contains the database ( it's just a single table with jsonb data ).
Check somewhere else how to install the db.
`config.php` contains the basic database connection configuration and the api key
that allows to run the `import.php` script. This script should be called at least
a couple of times a day from a cronjob.

You can find the project running [here](https://gog.ensoft.dev/).
