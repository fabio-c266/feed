<?php

namespace src\core;

use ReflectionClass;
use ReflectionMethod;

class File
{
    public static function executeClass(string $fileName, string $classMethod, array $methodParams)
    {
        $class = "src\\Controllers\\{$fileName}";
        $classInstance = new $class();

        echo call_user_func([$classInstance, $classMethod], ...$methodParams);
    }

    public static function resolveClassConstructorDependencies(ReflectionMethod $constructor): array
    {
        $dependencies = [];

        foreach ($constructor->getParameters() as $param) {
            $className = ucfirst($param->getName());
            $class = self::resolveNamespace($className);

            if ($className && $class) {
                array_push($dependencies, new $class());
            }
        }

        return $dependencies;
    }

    public static function resolveNamespace(string $fileName)
    {
        $srcFiles = scandir('src');
        $namespace = false;

        foreach ($srcFiles as $srcFile) {
            if (!str_contains($srcFile, '.php')) {
                $currentDir = "src/{$srcFile}";
                foreach (scandir($currentDir) as $file) {
                    if (str_contains($file, '.php')) {
                        $file = str_replace('.php', '', $file);
                    }

                    if ($file === $fileName) {
                        $namespace = "src\\{$srcFile}\\{$file}";
                        break;
                    }
                }
            }
        }

        return $namespace;
    }
}
