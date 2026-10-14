<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Tests\Testing;

use Imefisto\SwooleKit\Testing\RequestBuilder;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Imefisto\SwooleKit\Testing\RequestBuilder
 */
class RequestBuilderTest extends TestCase
{
    private RequestBuilder $builder;

    protected function setUp(): void
    {
        $factory = new Psr17Factory();
        $this->builder = new RequestBuilder($factory, $factory);
    }

    public function testDefaultsToGetRoot()
    {
        $request = $this->builder->build();

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/', $request->getUri()->getPath());
    }

    /**
     * @param string $shortcut Builder method name
     * @param string $method   Expected HTTP method
     *
     * @dataProvider methodProvider
     *
     * @return void
     */
    public function testMethodShortcuts(string $shortcut, string $method)
    {
        $request = $this->builder->$shortcut('/users')->build();

        $this->assertSame($method, $request->getMethod());
        $this->assertSame('/users', $request->getUri()->getPath());
    }

    public function methodProvider(): array
    {
        return [
            ['get', 'GET'],
            ['post', 'POST'],
            ['put', 'PUT'],
            ['patch', 'PATCH'],
            ['delete', 'DELETE'],
        ];
    }

    public function testQueryStringInUriFillsQueryParams()
    {
        $request = $this->builder->get('/search?q=swoole&page=2')->build();

        $this->assertSame('/search', $request->getUri()->getPath());
        $this->assertSame('q=swoole&page=2', $request->getUri()->getQuery());
        $this->assertSame(
            ['q' => 'swoole', 'page' => '2'],
            $request->getQueryParams()
        );
    }

    public function testWithQuerySetsUriAndQueryParams()
    {
        $request = $this->builder->get('/search')
            ->withQuery(['q' => 'swoole'])
            ->build();

        $this->assertSame('q=swoole', $request->getUri()->getQuery());
        $this->assertSame(['q' => 'swoole'], $request->getQueryParams());
    }

    public function testWithJsonSetsBodyAndContentTypeButNotParsedBody()
    {
        $request = $this->builder->post('/users')
            ->withJson(['name' => 'Ada'])
            ->build();

        $this->assertSame(
            'application/json',
            $request->getHeaderLine('Content-Type')
        );
        $this->assertSame('{"name":"Ada"}', (string) $request->getBody());
        $this->assertNull($request->getParsedBody());
    }

    public function testWithHeaderAndAttribute()
    {
        $request = $this->builder
            ->withHeader('Authorization', 'Bearer token')
            ->withAttribute('userId', 42)
            ->build();

        $this->assertSame('Bearer token', $request->getHeaderLine('Authorization'));
        $this->assertSame(42, $request->getAttribute('userId'));
    }

    public function testSettingsMadeBeforeMethodShortcutAreKept()
    {
        $request = $this->builder
            ->withHeader('Authorization', 'Bearer token')
            ->post('/users')
            ->build();

        $this->assertSame('Bearer token', $request->getHeaderLine('Authorization'));
    }

    public function testBuilderIsImmutable()
    {
        $base = $this->builder->withHeader('Authorization', 'Bearer token');

        $base->post('/users')->withJson(['name' => 'Ada']);

        $request = $base->build();
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('', (string) $request->getBody());
    }
}
