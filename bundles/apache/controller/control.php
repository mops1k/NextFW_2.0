<?php
namespace NextFW\Bundles\Apache\Controller;

use NextFW;
use NextFW\Engine as Engine;

class Control extends Engine\Controller {
    private $console;
    function __construct()
    {
        parent::__construct();
        $this->console = new Engine\Console();
        $who = $this->console->run("whoami");
        if($who != "root\n") {
            $this->console->writeLn("Only root can access to this script");
            $this->console->writeLn("Write sudo php ".getcwd()."/console.php apache:control:{action what you want}");
            exit;
        }
    }
    function service($args = null) {
        // Enter code below:
        if(count($args) != 0) $command = $args[0];
        else {
            $command = $this->console->ioRead("Enter action: ","restart");
        }
        try {
            $this->console->run("apache2ctl ".$command);

            $this->console->writeLn("Service action performed.");
        } catch (\Exception $e) {
            $this->console->writeLn($e->getMessage());
        }
    }

    function addVhost($args = null) {
        if(count($args) != 0) $name = $args[0];
        else {
            $name = $this->console->readRecursive("Enter new vhost name: ");
        }
        try{
            $io = new Engine\IO();

            $example = file_get_contents("/etc/apache2/sites-available/000-default.conf");
            $example = str_replace("#ServerName www.example.com", "ServerName {$name}",$example);
            $example = str_replace("ServerAdmin webmaster@localhost", "ServerAdmin webmaster@{$name}",$example);
            $example = str_replace("DocumentRoot /var/www/localhost/www", "DocumentRoot /var/www/{$name}/www",$example);
            $example = str_replace("ErrorLog /var/www/localhost/logs/error.log", "ErrorLog /var/www/{$name}/logs/error.log",$example);
            $example = str_replace("CustomLog /var/www/localhost/logs/access.log combined", "CustomLog /var/www/{$name}/logs/access.log combined",$example);

            $io->fileSave($example,"/etc/apache2/sites-available/{$name}.conf");

            $io->mkDir("/var/www/{$name}");
            $io->mkDir("/var/www/{$name}/www");
            $io->mkDir("/var/www/{$name}/logs");

            $index = "<h4>Vhost {$name} created succesfully</h4>";

            $io->fileSave($index,"/var/www/{$name}/www/index.html");

            $io->fileSave("127.0.0.1\t{$name}\n","/etc/hosts",true);

            try {
                $this->console->run("chown mops1k:mops1k /var/www/{$name}");
                $this->console->run("chown mops1k:mops1k /var/www/{$name}/www/index.html");
                $this->console->run("chown mops1k:mops1k /var/www/{$name}/www/");
                $this->console->run("chown mops1k:mops1k /var/www/{$name}/logs/");
                $this->console->run("a2ensite {$name}");
                $this->service(["restart"]);
                $this->console->run("chmod 0777 /var/www/{$name}/logs/*");

                $this->console->writeLn("vHost <{$name}> added succefully. http://{$name}/index.html");
            } catch (\Exception $e) {
                $this->console->writeLn($e->getMessage());
            }

        } catch (Engine\IOException $e) {
            $this->console->writeLn($e->getMessage());
        }
    }

    function rmVhost($args = null)
    {
        if(count($args) != 0) $name = $args[0];
        else {
            $name = $this->console->readRecursive("Enter new vhost name: ");
        }
        try {
            $io = new Engine\IO();
            $hosts = file_get_contents("/etc/hosts");
            $hosts = str_replace("127.0.0.1\t{$name}\n","",$hosts);
            $io->fileSave($hosts,"/etc/hosts");
            $this->console->run("a2dissite {$name}");
            $io->del("/etc/apache2/sites-available/{$name}.conf");
            $this->console->run("rm -rf /var/www/{$name}");
            $this->service(["restart"]);

            $this->console->writeLn("vHost <{$name}> successfully deleted.");
        } catch (Engine\IOException $e) {
            $this->console->writeLn($e->getMessage());
        }
    }
}