<?php
namespace trejeraos\SparkTest\Auth;

use \DateTime;
use \DOMDocument;
use Spark\Auth\Token;
use trejeraos\DOMSelector;

class ValidTokens
{
    protected $token_file = __DIR__ . "/../../data/tokens.xml";

    protected $tokens = array();

    /**
     * @var DOMDocument
     */
    protected $document;

    public function __construct()
    {
        $this->createTokenFile();
        $this->loadXMLFile();
        $this->removeExpiredTokens();
    }

    protected function createTokenFile()
    {
        if (!is_file($this->token_file)) {
            $xml_string =  '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            $xml_string .= '<tokens></tokens>' . PHP_EOL;

            file_put_contents($this->token_file, $xml_string);
        }
    }

    protected function loadXMLFile()
    {
        $this->document = new DOMDocument();

        $this->document->preserveWhiteSpace = false;
        $this->document->formatOutput = true;

        $this->document->load($this->token_file);
    }

    protected function saveXMLFile()
    {
        $this->document->save($this->token_file);
    }


    /**
     * @param \Spark\Auth\Token $token
     */
    public function updateToken(Token $token)
    {
        $token_node = $this->getTokenNode($token->getToken());

        if (is_null($token_node)) {
            $token_node = $this->document->createElement('token', $token->getToken());

            $expiration_attr = $this->document->createAttribute('expiration');
            $expiration_attr->value = $token->getMetadata('expiration');
            $token_node->appendChild($expiration_attr);

            $username_attr = $this->document->createAttribute('username');
            $username_attr->value = $token->getMetadata('username');
            $token_node->appendChild($username_attr);

            $this->document->documentElement->appendChild($token_node);
        } else {
            $token_node->setAttribute('expiration', $token->getMetadata('expiration'));
            $token_node->setAttribute('username', $token->getMetadata('username'));
        }

        $this->saveXMLFile();
    }

    protected function getTokenNodes()
    {
        $dom = new DOMSelector($this->document, false);

        return $dom->querySelectorAll('token');
    }

    protected function getTokenNode($token_string)
    {
        $token_nodes = $this->getTokenNodes();

        foreach ($token_nodes as $token_node) {
            if ($token_node->nodeValue == $token_string) {
                return $token_node;
            }
        }

        return null;
    }

    public function getToken($token_string)
    {
        $token_node = $this->getTokenNode($token_string);

        return is_null($token_node) ? null : $this->nodeToToken($token_node);
    }

    /**
     * @param DOMElement $node
     */
    protected function nodeToToken($node)
    {
        $token_string = $node->nodeValue;

        $metadata = array(
            'username'   => $node->getAttribute('username'),
            'expiration' => $node->getAttribute('expiration')
        );

        $token = new Token($token_string, $metadata);

        return $token;
    }

    protected function removeExpiredTokens()
    {
        $token_nodes = $this->getTokenNodes();
        $update_xml = false;

        $now = new DateTime('NOW');

        foreach ($token_nodes as $token_node) {
            $expiration = new DateTime($token_node->getAttribute('expiration'));

            if ($expiration < $now) {
                // token expired
                $token_node->parentNode->removeChild($token_node);
                $update_xml = true;
            }
        }

        if ($update_xml) {
            $this->saveXMLFile();
        }
    }
}
