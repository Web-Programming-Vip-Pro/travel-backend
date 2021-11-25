<?php

define("DB_HOST", $_ENV['DB_HOST']);
define("DB_DATABASE", $_ENV['DB_DATABASE']);
define("DB_USER", $_ENV['DB_USER']);
define("DB_PASSWORD", $_ENV['DB_PASSWORD']);
define("MAIL_HOST", $_ENV['MAIL_HOST']);
define("MAIL_USER", $_ENV['MAIL_USER']);
define("MAIL_PASSWORD", $_ENV['MAIL_PASSWORD']);
define("MAIL_PORT", $_ENV['MAIL_PORT']);
define("DB_PORT", isset($_ENV['DB_PORT']) ? $_ENV['DB_PORT'] : 3306);
define("JWT_SECRET", $_ENV['JWT_SECRET']);
