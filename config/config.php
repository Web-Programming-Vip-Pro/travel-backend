<?php

define("DB_HOST", $_ENV['DB_HOST']);
define("DB_DATABASE", $_ENV['DB_DATABASE']);
define("DB_USER", $_ENV['DB_USER']);
define("DB_PASSWORD", $_ENV['DB_PASSWORD']);
define("DB_PORT", isset($_ENV['DB_PORT']) ? $_ENV['DB_PORT'] : 3306);
define("JWT_SECRET", $_ENV['JWT_SECRET']);
