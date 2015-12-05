<?php
namespace trejeraos\SparkTest;

use \Exception;

class Configuration {
    public $my_name;

    protected $file_path;

    public function __construct($filename, $directories) {
        $this->my_name = "Steve";

        $filepath = $this->locate($filename, $directories);

        if (is_null($filepath)) {
            throw new Exception("The file '{$filename}' was not found");
        }

        $this->file_path = $filepath;
        $this->parseINI();
    }

    protected function locate($filename, $directories)
    {
        $filepath = null;

        foreach ($directories as $dir) {
            if (is_file($dir.$filename)) {
                $filepath = realpath($dir.$filename);
                break;
            }
        }

        return $filepath;
    }

    protected function parseINI()
    {
        $data = parse_ini_file($this->file_path, true);

        if ($data === false) {
            throw new Exception("The file '{$this->file_path}' could not be parsed.");
        }

        if (isset($data['general']['my_name'])) {
            $this->my_name = $data['general']['my_name'];
        }
    }
}
