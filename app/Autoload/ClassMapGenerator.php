<?php

namespace App\Autoload;

use App\Helpers\Filesystem;

class ClassMapGenerator {
    /**
     * Generates a classmap and writes it to the specified file
     *
     * @param string $directory the directory to scan
     * @param string $file the target file for the classmap to be written to
     */
    public static function generate(string $directory, string $file) {
        file_put_contents($file, self::createMap($directory));
    }

    /**
     * Generates a classmap
     *
     * @param string $path the directory to scan
     * @return string the resulting classmap
     */
    public static function createMap(string $path) {
        $files      = Filesystem::findFiles($path, ['php']);
        $classes    = [];

        foreach($files as $file) {
            $fqcn = self::getFullyQualifiedClassName($file);

            //Check if a classname was found
            if($fqcn === '') {
                continue;
            }

            $classes[$fqcn] = $file;
        }

        //Get the current timestamp
        $timestamp = date("d-m-Y H:m:s");

        //Generate the classmap
        $code = <<<CODE
<?php

// Do not modify - changes will be overwritten
// Automatically @generated by Shoutz0r at $timestamp

return %s;
CODE;

        // Return the code output
        return sprintf($code, var_export($classes, true));
    }


    /**
     * Returns the fully qualified classname from a file (if applicable)
     *
     * @param string $filepath the target file to check
     * @return string the resulting fqcn, empty if not found
     */
    private static function getFullyQualifiedClassName(string $filepath) : string {
        $code   = file_get_contents($filepath);
        $tokens = token_get_all($code);
        $class  = $namespace = '';

        foreach($tokens as $token) {
            if($token[0] === T_NAMESPACE) {
                $namespace = $token[1];
            }
            else if($token[0] === T_CLASS) {
                $class = $token[1];
                break;
            }
        }

        return $namespace . '\\' . $class;
    }
}
