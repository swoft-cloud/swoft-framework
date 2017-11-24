<?php

namespace Swoft\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Swoft\App;
use Swoft\Web\Psr7Request;
use Swoft\Web\Uri;


/**
 * @uses      Client
 * @version   2017-11-22
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Client
{

    /**
     * @var Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        $headers = isset($options['headers']) ? $options['headers'] : [];
        $body = isset($options['body']) ? $options['body'] : null;
        $version = isset($options['version']) ? $options['version'] : '1.1';
        // Merge the URI into the base URI.
        $uri = $this->buildUri($uri, $options);
        if (is_array($body)) {
            $this->invalidBody();
        }
        $request = new Psr7Request($method, $uri, $headers, $body, $version);
        return $this->getAdapter()->request($request, $options);
    }

    /**
     * @param string|UriInterface $uri
     * @param array $options
     * @return UriInterface
     */
    protected function buildUri($uri, array $options): UriInterface
    {
        if (!$uri instanceof UriInterface) {
            $uri = new Uri($uri);
        }
        if (isset($options['base_uri'])) {
            $baseUri = $options['base_uri'] instanceof UriInterface ? $options['base_uri'] : new Uri($options['base_uri']);
            $uri = $this->resolve($baseUri, $uri);
        }

        return !$uri->getScheme() && !$uri->getHost() ? $uri->withScheme('http') : $uri;
    }

    /**
     * @param UriInterface $base
     * @param UriInterface $rel
     * @return UriInterface
     */
    protected function resolve(UriInterface $base, UriInterface $rel)
    {
        if ((string)$rel === '') {
            return $base;
        }
        if ($rel->getScheme() !== '') {
            return $rel->withScheme($this->removeDotSegments($rel->getPath()));
        }
        if ($rel->getAuthority() != '') {
            $targetAuthority = $rel->getAuthority();
            $targetPath = $this->removeDotSegments($rel->getPath());
            $targetQuery = $rel->getQuery();
        } else {
            $targetAuthority = $base->getAuthority();
            if ($rel->getPath() === '') {
                $targetPath = $base->getPath();
                $targetQuery = $rel->getQuery() != '' ? $rel->getQuery() : $base->getQuery();
            } else {
                if ($rel->getPath()[0] === '/') {
                    $targetPath = $rel->getPath();
                } else {
                    if ($targetAuthority != '' && $base->getPath() === '') {
                        $targetPath = '/' . $rel->getPath();
                    } else {
                        $lastSlashPos = strrpos($base->getPath(), '/');
                        if ($lastSlashPos === false) {
                            $targetPath = $rel->getPath();
                        } else {
                            $targetPath = substr($base->getPath(), 0, $lastSlashPos + 1) . $rel->getPath();
                        }
                    }
                }
                $targetPath = $this->removeDotSegments($targetPath);
                $targetQuery = $rel->getQuery();
            }
        }
        return new Uri(Uri::composeComponents(
            $base->getScheme(),
            $targetAuthority,
            $targetPath,
            $targetQuery,
            $rel->getFragment()
        ));
    }

    /**
     * Removes dot segments from a path and returns the new path.
     *
     * @param string $path
     *
     * @return string
     * @link http://tools.ietf.org/html/rfc3986#section-5.2.4
     */
    protected function removeDotSegments($path)
    {
        if ($path === '' || $path === '/') {
            return $path;
        }

        $results = [];
        $segments = explode('/', $path);
        foreach ($segments as $segment) {
            if ($segment === '..') {
                array_pop($results);
            } elseif ($segment !== '.') {
                $results[] = $segment;
            }
        }

        $newPath = implode('/', $results);

        if ($path[0] === '/' && (!isset($newPath[0]) || $newPath[0] !== '/')) {
            // Re-add the leading slash if necessary for cases like "/.."
            $newPath = '/' . $newPath;
        } elseif ($newPath !== '' && ($segment === '.' || $segment === '..')) {
            // Add the trailing slash if necessary
            // If newPath is not empty, then $segment must be set and is the last segment from the foreach
            $newPath .= '/';
        }

        return $newPath;
    }

    /**
     * @return Adapter\AdapterInterface
     */
    public function getAdapter(): Adapter\AdapterInterface
    {
        if (!$this->adapter instanceof Adapter\AdapterInterface) {
            if ($this->isSupportDefer()) {
                $this->setAdapter(new Adapter\DeferAdapter());
            } else {
                $this->setAdapter(new Adapter\CurlAdapter());
            }
        }
        return $this->adapter;
    }

    /**
     * @param Adapter\AdapterInterface $adapter
     * @return Client
     */
    public function setAdapter(Adapter\AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @return bool
     */
    private function isSupportDefer(): bool
    {
        return App::isWorkerStatus();
    }

}