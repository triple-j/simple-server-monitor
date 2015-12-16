<?php
namespace trejeraos\SparkTest\Data;

use \Exception;
use \DOMDocument;
use Spark\Auth\Credentials;
use trejeraos\DOMSelector;

//TODO: create configuration parsing exception

class Configuration {

    protected $defualt_file;
    protected $user_file;

    /**
     * @var DOMDocument
     */
    protected $document;

    /**
     * @var trejeraos\DOMSelector
     */
    protected $dom;

    /**
     * @var \Spark\Auth\Credentials
     */
    #protected $credentials;

    public function __construct() {
        $this->defualt_file = __DIR__ . "/../config.xml";
        $this->user_file    = __DIR__ . "/../../data/config.xml";
    
        #$this->my_name = "Steve";

        if (!is_file($this->user_file)) {
            if (!copy($this->defualt_file, $this->user_file)) {
                throw new Exception("Unable to create configuration file.");
            }
        }

        $this->loadXMLFile();
    }
    
    public function getBase()
    {
        return $this->dom->querySelector(':root > base')->nodeValue;
    }

    public function getMyName() {
        return $this->dom->querySelector('option[name="my_name"]')->nodeValue;
    }

    public function setMyName($name) {
        $this->dom->querySelector('option[name="my_name"]')->nodeValue = $name;
        $this->saveXMLFile();
    }

    /**
     * @return \Spark\Auth\Credentials
     */
    public function getCredentials() {
        $cred_node = $this->dom->querySelector('credentials');
        $username = $cred_node->getAttribute('username');
        $password = $cred_node->getAttribute('password');

        if ($username && !is_null($password)) {
            return new Credentials($username, $password);
        } else {
            throw new Exception("No credentials set.");
        }
    }

    protected function loadXMLFile()
    {
        $this->document = new DOMDocument();

        $this->document->preserveWhiteSpace = false;
        $this->document->formatOutput = true;

        $this->document->load($this->user_file);

        $this->dom = new DOMSelector($this->document);
    }

    protected function saveXMLFile()
    {
        $this->document->save($this->user_file);
    }
}
