# Captain's Log, the Online Version

Save your daily activities and notes in a friendly format. It's exactly what it sounds like if you've watched Star Trek.

## Requirements

- Apache, Lighttpd, Nginx, whatever web service
- PHP 5.4+ w/ "hash" and "mysqli" extensions (probably already installed)
- MySQL/MariaDB, ability to make a new database

## Setup

Install the database via `captains-log-db.sql`.

Duplicate `dbconn_mysql.sample.php` and rename it `dbconn_mysql.php` with the right MySQL connection info.

The tough part: duplicate `logincheck.sample.php` and rename it `logincheck.php` and edit in your own authentication method. I personally used a SimpleSAMLphp instance to make this, so I didn't have to worry about registration and whatnot, only authentication.

Upload the site files somewhere, and enjoy!

## To-dos

- More mobile-friendly interface / media query breakpoints.
- Port over all of the reporting tools from the original parser!