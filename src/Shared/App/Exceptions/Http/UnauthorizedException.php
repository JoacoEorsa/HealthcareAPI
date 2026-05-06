<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Exceptions\Http;

class UnauthorizedException extends HttpException
{
    /**
     * An HTTP status code.
     */
    #[\Override]
    protected int $status = 401;

    /**
     * The error code.
     */
    #[\Override]
    protected string $errorCode = 'unauthorized';
}
