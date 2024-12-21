<?php

use Http\Factory\Guzzle\ResponseFactory;
use Http\Factory\Guzzle\StreamFactory;
use Http\Factory\Guzzle\UploadedFileFactory;
use Http\Factory\Guzzle\UriFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

return [
    ResponseFactoryInterface::class => \DI\get(ResponseFactory::class),
    StreamFactoryInterface::class => \DI\get(StreamFactory::class),
    UploadedFileFactoryInterface::class => \DI\get(UploadedFileFactory::class),
    UriFactoryInterface::class => \DI\get(UriFactory::class),
];
