<?php

namespace Zoho\Crm\Contracts;

interface MiddlewareInterface
{
    /**
     * Apply the middleware on the request.
     *
     * @param RequestInterface $request The request
     * @return void
     */
    public function __invoke(RequestInterface $request): void;
}
