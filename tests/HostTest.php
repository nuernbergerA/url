<?php

use Crwlr\Url\Host;

beforeEach(function () {
    $this->exampleHost = new Host('www.example.com');
});

test('parse host', function () {
    $host = new Host('www.example.com');
    $this->assertInstanceOf(Host::class, $host);
    $this->assertEquals('www.example.com', $host->__toString());
    $this->assertEquals('www', $host->subdomain());
    $this->assertEquals('example.com', $host->domain());
    $this->assertEquals('example', $host->domainLabel());
    $this->assertEquals('com', $host->domainSuffix());

    $host = new Host('www.test.local');
    $this->assertInstanceOf(Host::class, $host);
    $this->assertEquals('www.test.local', $host->__toString());
    $this->assertNull($host->domain());

    $host = new Host('xn--f-vgaa.xn--90aiifajq6iua.xn--80asehdb');
    $this->assertInstanceOf(Host::class, $host);
    $this->assertEquals('xn--f-vgaa.xn--90aiifajq6iua.xn--80asehdb', $host->__toString());
    $this->assertEquals('xn--f-vgaa', $host->subdomain());
    $this->assertEquals('xn--90aiifajq6iua.xn--80asehdb', $host->domain());
    $this->assertEquals('xn--90aiifajq6iua', $host->domainLabel());
    $this->assertEquals('xn--80asehdb', $host->domainSuffix());
});

test('subdomain', function (?string $expectedSubdomain, string $expectedString) {
    expect($this->exampleHost)
        ->subdomain()->toBe($expectedSubdomain)
        ->__toString()->toBe($expectedString);
})->with([
    'sub.domain' => ['sub.domain', 'sub.domain.example.com', fn () => $this->exampleHost->subdomain('sub.domain')],
    'empty' => [null, 'example.com', fn () => $this->exampleHost->subdomain('')],
    'foo.bar.yololo' => ['foo.bar.yololo', 'foo.bar.yololo.example.com', fn () => $this->exampleHost->subdomain('foo.bar.yololo')],
]);

test('domain', function (?string $expectedDomain, string $expectedString) {
    expect($this->exampleHost)
        ->domain()->toBe($expectedDomain)
        ->__toString()->toBe($expectedString);
})->with([
    'foo.bar' => ['foo.bar', 'www.foo.bar', fn () => $this->exampleHost->domain('foo.bar')],
    'empty' => [null, '', fn () => $this->exampleHost->domain('')],
    'crwlr.software' => ['crwlr.software', 'www.crwlr.software', fn () => $this->exampleHost->domain('crwlr.software')],
]);

test('domain label', function (?string $expectedLabel, ?string $expectedDomain, string $expectedString) {
    expect($this->exampleHost)
        ->domainSuffix()->toBe('com')
        ->domainLabel()->toBe($expectedLabel)
        ->domain()->toBe($expectedDomain)
        ->__toString()->toBe($expectedString);
})->with([
    'foo' => ['foo', 'foo.com', 'www.foo.com', fn () => $this->exampleHost->domainLabel('foo')],
    'empty' => [null, null, '', fn () => $this->exampleHost->domainLabel('')],
    'google' => ['google', 'google.com', 'www.google.com', fn () => $this->exampleHost->domainLabel('google')],
]);

test('domain suffix', function (?string $expectedSuffix, ?string $expectedDomain, string $expectedString) {
    expect($this->exampleHost)
        ->domainLabel()->toBe('example')
        ->domainSuffix()->toBe($expectedSuffix)
        ->domain()->toBe($expectedDomain)
        ->__toString()->toBe($expectedString);
})->with([
    'org' => ['org', 'example.org', 'www.example.org', fn () => $this->exampleHost->domainSuffix('org')],
    'empty' => [null, null, '', fn () => $this->exampleHost->domainSuffix('')],
    'software' => ['software', 'example.software', 'www.example.software', fn () => $this->exampleHost->domainSuffix('software')],
]);

test('hasIdn', function (string $host, bool $expected) {
    expect((new Host($host))->hasIdn())
        ->toBe($expected);
})->with([
    'www.example.com' => ['www.example.com', false],
    'www.ex-ample.com' => ['www.ex-ample.com', false],
    'www.männersalon.at' => ['www.xn--mnnersalon-q5a.at', true],
    'jobs.müller.de' => ['jobs.xn--mller-kva.de', true],
]);
