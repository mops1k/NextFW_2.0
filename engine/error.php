<?php
namespace NextFW\Engine;

use NextFW\Config;
use NextFW;

class Error extends \Exception {
    private $tpl;
    public static $errors = false;
    public $messageArr = [];

    static function error_handler($errno, $errstr, $errfile, $errline)
    {
        // если ошибка попадает в отчет (при использовании оператора "@" error_reporting() вернет 0)
        if (error_reporting() & $errno)
        {
            $errors = array(
                E_ERROR => 'E_ERROR',
                E_WARNING => 'E_WARNING',
                E_PARSE => 'E_PARSE',
                E_NOTICE => 'E_NOTICE',
                E_CORE_ERROR => 'E_CORE_ERROR',
                E_CORE_WARNING => 'E_CORE_WARNING',
                E_COMPILE_ERROR => 'E_COMPILE_ERROR',
                E_COMPILE_WARNING => 'E_COMPILE_WARNING',
                E_USER_ERROR => 'E_USER_ERROR',
                E_USER_WARNING => 'E_USER_WARNING',
                E_USER_NOTICE => 'E_USER_NOTICE',
                E_STRICT => 'E_STRICT',
                E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
                E_DEPRECATED => 'E_DEPRECATED',
                E_USER_DEPRECATED => 'E_USER_DEPRECATED',
            );
            chdir(ROOT);
            // выводим свое сообщение об ошибке
            $message = "<p>Произошла ошибка типа \"{$errors[$errno]}\": ".str_replace("\n","<br />",$errstr)." в файле $errfile на линии $errline</p>\n";
            $route = new Route();
            $route->run("response/show","error",[
                $errors[$errno],
                $message,
                self::getErrorContent($errfile,$errline)
            ]);
        }

        // не запускаем внутренний обработчик ошибок PHP
        return TRUE;
    }

    static function exception_handler($e) {
        $traceline = "<b>#%s</b> %s(%s): <b>%s</b>(%s)";
        $msg = "<b>Exception</b> '%s'\n<b>with</b> message '%s'\n<b>
        at</b> %s(%s)\n\n<b>Stack trace:\n{main}</b>\n%s\n";
        // обращаем стек
        $trace = array_reverse($e->getTrace());
        $result = array();
        foreach ($trace as $trace_key => $trace_value)
        {
            // выводим аргументы в зависимости от типа
            if(isset($trace_value['args']))
            {
                foreach ($trace_value['args'] as $arg_key => $arg_value)
                {
                    if(is_object($arg_value))
                    {
                        $ref = new ReflectionObject($arg_value);
                        $value = '/*'.gettype($arg_value).' '.$ref->getName().'*/ ';
                        // если есть метод __toString - используем его
                        // в противном случае - универсальный вывод
                        if($ref->hasMethod('__toString'))
                            $value .= $arg_value->__toString();
                        else
                            $value .= var_dump($arg_value);
                    }
                    else if(is_array($arg_value))
                    {
                        $value = '/*'.gettype($arg_value).'('.count($arg_value).')'.'*/ [';
                        foreach ($arg_value as $key => $value) {
                            $value .= "\n";
                            $value .= "$key => /* ".gettype($value)." */ ".$value;
                        }
                        $value .= "\n]";


                    }
                    else
                    {
                        $value = '/*'.gettype($arg_value).'*/ '.$arg_value;
                    }
                    if(strlen($value)>100)
                        $value = substr($value,0,100).'...';
                    $trace_value['args'][$arg_key] = $value;
                }
            }
            // выводим пункт стека
            $fn = $trace_value['function'];
            if(isset($trace_value['class']) && $trace_value['class'] != '')
                $fn = $trace_value['class'].'->'.$fn;
            if(!isset($trace_value['file']))
                $trace_value['file'] = '';
            if(!isset($trace_value['line']) || $trace_value['line']=='')
                $trace_value['line'] = 'internal function';
            $result[] = sprintf($traceline, $trace_key, $trace_value['file'],
                $trace_value['line'], $fn, implode(", ", $trace_value['args']));
        }

        $msg = nl2br(sprintf($msg, get_class($e), $e->getMessage(),
            $e->getFile(), $e->getLine(), implode("\n",
                $result)));
        $route = new Route();
        $route->run("response/show","error",[
            get_class($e),
            $msg,
            self::getErrorContent($e->getFile(),$e->getLine())
        ]);
        exit;
    }

    static function getErrorContent($file,$line){
        if(file_exists($file)) {
            $file = file($file);
            $lines = count($file);
            $startLine = $line-10 >= 0 ? $line-10 : 0;
            $endLine = $line+10 <= $lines ? $line+10 : $lines;
            $content = null;
            for($startLine; $startLine < $endLine; $startLine++)
            {
                $content .= $file[$startLine];
            }
            return $content;
        }
    }
    /**
     * Функция перехвата фатальных ошибок
     */
    function fatal_error_handler()
    {
        // если была ошибка и она фатальна
        if ($error = error_get_last() AND $error['type'] & ( E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR))
        {
            // очищаем буффер (не выводим стандартное сообщение об ошибке)
            ob_end_clean();
            // запускаем обработчик ошибок
            self::error_handler($error['type'], $error['message'], $error['file'], $error['line']);
            die();
        }
        else
        {
            // отправка (вывод) буфера и его отключение
            ob_end_flush();
        }
    }
}
