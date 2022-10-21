<?php

use Crwlr\Url\Helpers;
use Crwlr\Url\Schemes;
use Crwlr\Url\Suffixes;

test('GetHelperClassInstancesStatically', function () {
    expect(Helpers::suffixes())
        ->toBeInstanceOf(Suffixes::class);

    expect(Helpers::schemes())
        ->toBeInstanceOf(Schemes::class);
});

it('can build url from components', function () {
    expect(Helpers::buildUrlFromComponents([
        'scheme' => 'https',
        'user' => 'user',
        'pass' => 'pass',
        'host' => 'www.example.com',
        'port' => 1234,
        'path' => '/foo/bar',
        'query' => 'query=string',
        'fragment' => 'fragment',
    ]))->toBe('https://user:pass@www.example.com:1234/foo/bar?query=string#fragment');
});

it('can build authority from components', function (array $components) {
    expect(Helpers::buildAuthorityFromComponents($components))
        ->toBe('user:password@www.example.com:1234');
})->with([
    'regular' => fn () => [
        'user' => 'user',
        'password' => 'password',
        'host' => 'www.example.com',
        'port' => 1234,
    ],
    'abbreviation' => fn () => [
        'user' => 'user',
        'pass' => 'password',
        'host' => 'www.example.com',
        'port' => 1234,
    ]
]);


it('can build user info from components', function (array $components) {
    expect(Helpers::buildUserInfoFromComponents($components))
        ->toBe('user:password');
})->with([
    'regular' => fn () => ['user' => 'user', 'password' => 'password'],
    'abbreviation' => fn () => ['user' => 'user', 'pass' => 'password'],
]);



/**
 * This test especially targets a problem in parse_str() which is used in the Parser class to convert a query
 * string to array. The problem is, that dots within keys in the query string are replaced with underscores.
 * For more information see https://github.com/crwlrsoft/url/issues/2
 */
it('can convert a query string to an array', function () {
    expect(Helpers::queryStringToArray('k.1=v.1&k.2[s.k1]=v.2&k.2[s.k2]=v.3'))
        ->toBe([
            'k.1' => 'v.1',
            'k.2' => [
                's.k1' => 'v.2',
                's.k2' => 'v.3',
            ]
        ]);
});

it('returns a standard port by scheme', function (string $scheme, ?int $port) {
    expect(Helpers::getStandardPortByScheme($scheme))
        ->toBe($port);
})->with([
    ['ftp', 21],
    ['git', 9418],
    ['http', 80],
    ['https', 443],
    ['imap', 143],
    ['irc', 194],
    ['nfs', 2049],
    ['rsync', 873],
    ['sftp', 115],
    ['smtp', 25],
    ['unknownscheme', null],
]);

it('can strip from end', function (string $strip, string $expected) {
    expect(Helpers::stripFromEnd('examplestring', $strip))
        ->toBe($expected);
})->with([
    ['string', 'example'],
    ['strong', 'examplestring'],
    ['strin', 'examplestring'],
]);

it('can strip from start', function (string $strip, string $expected) {
    expect(Helpers::stripFromStart('examplestring', $strip))
        ->toBe($expected);
})->with([
    ['example', 'string'],
    ['eggsample', 'examplestring'],
    ['xample', 'examplestring'],
]);

it('can replace first occurrence', function () {
    expect(Helpers::replaceFirstOccurrence('bar', 'bas', 'foo bar baz bar'))->toBe('foo bas baz bar');
    expect(Helpers::replaceFirstOccurrence('baz', 'bar', 'foo bar baz'))->toBe('foo bar bar');
});

it('can tell if a string starts with', function () {
    expect(Helpers::startsWith('Raindrops Keep Fallin\' on My Head', 'Raindrops Keep'))->toBeTrue();
    expect(Helpers::startsWith('Raindrops Keep Fallin\' on My Head', 'Braindrops Keep'))->toBeFalse();
});

it('can tell if a string contains x before first y', function () {
    expect(Helpers::containsXBeforeFirstY('one-two-three-two', '-', 'two'))->toBeTrue();
    expect(Helpers::containsXBeforeFirstY('one-two-three-two', 'three', 'two'))->toBeFalse();
});
