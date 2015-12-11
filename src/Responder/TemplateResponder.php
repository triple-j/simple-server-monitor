<?php
namespace trejeraos\SimpleServerMonitor\Responder;

use League\Plates\Engine;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Spark\Adr\PayloadInterface;
use Spark\Adr\ResponderInterface;
use Spark\Formatter\PlatesFormatter;

class TemplateResponder implements ResponderInterface
{
    protected $formatter;

    public function __construct()
    {
        $engine = new Engine(__DIR__ . '/../../templates');

        $this->formatter = new PlatesFormatter($engine);
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface      $response,
        PayloadInterface       $payload
    ) {
        $formatter = $this->formatter;

        $response = $response->withStatus($formatter->status($payload));
        $response = $response->withHeader('Content-Type', $formatter->type());

        // Overwrite the body instead of making a copy and dealing with the stream.
        $response->getBody()->write($formatter->body($payload));

        return $response;
    }
}
