<?php

spl_autoload_register(
    function ($className) {
        $classPath = explode('_', $className);
        if ($classPath[0] != 'IDP') {
            return;
        }
        $classPath = array_slice($classPath, 1, 2);

        $filePath = dirname(__FILE__) . '/' . implode('/', $classPath) . '.php';
        if (file_exists($filePath)) {
            require_once($filePath);
        }
    }
);