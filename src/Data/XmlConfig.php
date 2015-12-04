<?php
namespace trejeraos\SimpleServerMonitor\Data;

use \DOMDocument;
use \DOMXpath;
use \Exception;
use Symfony\Component\CssSelector\CssSelector;

class XmlConfig
{
    private $filename;

    private $dom;

    public function __construct($filename)
    {
        $this->filename = $filename;

        if (!is_file($filename)) {
            throw new Exception("File not found.");
        }

        $this->dom = new DOMDocument;
        $this->dom->load($filename);

        CssSelector::disableHtmlExtension();

        //$this->setBase("walawala");
        //die( $this->dom->saveXML() );
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getBase()
    {
        $baseElement = $this->querySelector(':root > base');
        return $baseElement->nodeValue;
    }

    public function setBase($value)
    {
        $baseElement = $this->querySelector(':root > base');
        $baseElement->nodeValue = $value;
    }

    private function querySelectorAll($cssSelectors) {
        $xpath = new DOMXpath($this->dom);
        $xpathQuery = CssSelector::toXPath($cssSelectors);
        return $xpath->query($xpathQuery);
    }

    private function querySelector($cssSelectors) {
        return $this->querySelectorAll($cssSelectors)->item(0);
    }
}
