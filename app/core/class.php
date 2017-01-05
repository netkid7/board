<?php
spl_autoload_register('includeClassFile');

function includeClassFile($class)
{
    if (substr($class, 0, 4) === 'Core') {
        $classPath = APP_PATH.'core/';
    } else {
        $classPath = ClassPath::$subPath;
    }

    $class{0} = strtolower($class{0});  // $class = lcfirst($class); >= ver 5.3.0

    if (file_exists(BASE_PATH.$classPath.$class.'.php')) {
        include_once $classPath.$class.'.php';
    } else {
        echo $class.'<br>';

        exit("No such file '{$classPath}/{$class}.php'");
    }
}

class ClassPath
{
    public static $subPath = '';
}

function &loadClass ($class, $path = '')
{
    static $_classes = array();

    if ($path != '' && substr($path, -1) != '/') {
        $path .= '/';
    }

    $classPath = APP_PATH.$path;

    if (isset($_classes[$classPath.$class])) {
        return $_classes[$classPath.$class];
    }

    ClassPath::$subPath = $classPath;
    if (class_exists($class)) {
        $_classes[$classPath.$class] = new $class();

        // For class parameters, this function added parameter($args = array())
        // if (!empty($args)) {
        //     $reflect = new ReflectionClass($class);
        //     $_classes[$classPath.$class] = $reflect->newInstanceArgs($args);
        // }
        //
        // From stackoverflowㄴ
        // if (version_compare(phpversion(), '5.6.0', '>=')) {
        //     $instance = new $class(eval('...').$args);
        // } else {
        //     $reflect = new ReflectionClass($class);
        //     $instance = $reflect->newInstanceArgs($args);
        // }

        return $_classes[$classPath.$class];
    } else {
        $trace = @debug_backtrace();
        krsort($trace);
        
        $traceInfo = array();
        foreach ($trace as $call) {
            $traceInfo[] = "Error file: $call[file] (line number: $call[line])";
        }
        $traceMsg = "
                Class non-exist<br />
                Error: Make instance of $class <br />";
        $traceMsg .= implode('<br />', $traceInfo);
        print "
                <div style='border: 1px solid #EAEAEA; padding: 10px;'>
                    <p style='padding: 0 10px;'>
                        $traceMsg <br />
                    </p>
                </div>";
        exit;
    }
}
