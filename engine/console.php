<?php
namespace NextFW\Engine;

class Console {
    /**
     * Read line from input
     * @param string $string Promt line before input
     * @param string $default Default value if input is emtpy
     * @return string
     */
    function ioRead($string,$default = null) {
        echo $string;
        $st = fopen('php://stdin', 'r');
        $line = trim(fgets($st));
        if($line == "") $line = $default;
        return $line;
    }

    /**
     * Read line recursive, ending reading if input is not empty
     * @param string $string Promt line before input
     * @param string $name
     * @return string
     */
    function readRecursive($string,$name = "")
    {
        $name = $this->ioRead($string);
        return $name != "" ? $name : $this->readRecursive($string,$name);
    }

    /**
     * Write only 1 line in console (only 1st line will be printed)
     * @param string $string
     */
    function writeLn($string = null) {
        $string = explode(PHP_EOL,$string);
        echo $string[0].PHP_EOL;
    }

    /**
     * Write text in console
     * @param string $text
     */
    function write($text) {
        echo $text.PHP_EOL;
    }

    /**
     * Run shell command (Linux/Windows)
     * @param string $command command to run
     * @return string
     */
    function run($command)
    {
        $sh = popen($command, 'r');
        $output = null;
        while($line = fgetss($sh)) {
            $output .= $line;
        }
        return $output;
    }
}
