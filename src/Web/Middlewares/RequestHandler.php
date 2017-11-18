<?php

namespace Swoft\Web\Middlewares;

use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;

class RequestHandler implements RequestHandlerInterface
{
    /**
     * @var array
     */
    private $middlewares;

    /**
     * @var string
     */
    private $default;

    /**
     * @var integer
     */
    private $offset = 0;

    /**
     * RequestHandler constructor.
     *
     * @param array $middleware
     * @param string $default
     */
    public function __construct(array $middleware, string $default)
    {
        $this->middlewares = array_unique($middleware);
        $this->default = $default;
    }

    /**
     * Process the request using the current middleware.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (empty($this->middlewares[$this->offset])) {
            $handler = App::getBean($this->default);
        } else {
            $handler = $this->middlewares[$this->offset];
            is_string($handler) && $handler = App::getBean($handler);
        }
        if (! $handler instanceof MiddlewareInterface) {
            throw new \InvalidArgumentException('Invalid Handler');
        }
        return $handler->process($request, $this->next());
    }

    /**
     * Get a handler pointing to the next middleware.
     *
     * @return static
     */
    private function next()
    {
        $clone = clone $this;
        $clone->offset++;
        return $clone;
    }

    /**
     * Insert middlewares to the next position
     *
     * @param array $middlewares
     * @return $this
     */
    public function insertMiddlewares(array $middlewares)
    {
        list($before, $after) = array_chunk($this->middlewares, $this->offset);
        $middlewares = array_merge((array)$before, $middlewares, (array)$after);
        $this->middlewares = $middlewares;
        return $this;
    }
}
