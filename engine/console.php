<?php
namespace NextFW\Engine;

class Console {
    /**
     * Read line from input
     * @param string $string
     * @return string
     */
    function ioRead($string,$default = null) {
        echo $string;
        $st = fopen('php://stdin', 'r');
        $line = trim(fgets($st));
        if($line == "") $line = $default;
        return $line;
    }

    function readRecursive($string,$name = "")
    {
        $name = $this->ioRead($string);
        return $name != "" ? $name : $this->readRecursive($string,$name);
    }

    /**
     * Write line in console
     * @param null $string
     */
    function writeLn($string = null) {
        echo $string."\n";
    }

    /**
     * Write text in console
     * @param null $text
     */
    function write($text) {
        echo $text."\n";
    }

    /**
     * Run shell command
     * @param string $command
     * @return string
     */
    function run($command)
    {
        $sh = popen($command, 'r');
        $output = fgets($sh);
        return $output;
    }
}
