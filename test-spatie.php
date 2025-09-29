<?php

require 'vendor/autoload.php';

if (class_exists('Spatie\Permission\Middleware\PermissionMiddleware')) {
    echo "OK";
} else {
    echo "FALHOU";
}