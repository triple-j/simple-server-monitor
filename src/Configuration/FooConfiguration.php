<?php
namespace trejeraos\SimpleServerMonitor\Configuration;

use Auryn\Injector;
use Spark\Auth\AuthHandler;
use Spark\Auth\AdapterInterface;
use Spark\Auth\Token\ExtractorInterface as TokenExtractorInterface;
use Spark\Auth\Token\QueryExtractor;
use Spark\Auth\Credentials\ExtractorInterface as CredentialsExtractorInterface;
use Spark\Auth\Credentials\BodyExtractor;
use Spark\Configuration\ConfigurationInterface;
use trejeraos\SimpleServerMonitor\Auth\FooHandler as FooAuthHandler;

class FooConfiguration implements ConfigurationInterface
{
    public function apply(Injector $injector)
    {
        
        //START: auth
        $injector->alias(AdapterInterface::class, \trejeraos\SimpleServerMonitor\Auth\FooAdapter::class);
        $injector->alias(AuthHandler::class, FooAuthHandler::class);
        $injector->share(FooAuthHandler::class);
        
        // get auth token
        $injector->alias(TokenExtractorInterface::class, QueryExtractor::class);
        $injector->define(QueryExtractor::class, [':parameter' => 'tok']);
        
        // get auth credentials
        $injector->alias(CredentialsExtractorInterface::class, BodyExtractor::class);
        $injector->define(BodyExtractor::class, [':identifier' => 'user', ':password' => 'password']);
        
        // share valid auth token class
        $injector->share(\trejeraos\SimpleServerMonitor\Auth\ValidTokens::class);
        //END: auth
        
        
        // share global config class
        $injector->share(\trejeraos\SimpleServerMonitor\Data\Configuration::class);
    }
}