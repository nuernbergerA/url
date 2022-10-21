<?php

use Crwlr\Url\DefaultPorts;

it('returns a port for each listed default protocol', function (string $protocol, int $port) {
    expect((new DefaultPorts())->get($protocol))
        ->toBe($port);
})->with([
    ['ftp', 21],
    ['git', 9418],
    ['http', 80],
    ['https', 443],
    ['imap', 143],
    ['irc', 194],
    ['ircs', 994],
    ['ldap', 389],
    ['ldaps', 636],
    ['nfs', 2049],
    ['sftp', 115],
    ['smtp', 25],
    ['ssh', 22],
]);

it('returns a port for each unlisted protocol', function (string $protocol, int $port) {
    expect((new DefaultPorts())->get($protocol))
        ->toBe($port);
})->with([
    ['about', 2019],
    ['acap', 674],
    ['gopher', 70],
    ['mtqp', 1038],
    ['news', 2009],
    ['rsync', 873],
    ['svn', 3690],
    ['telnet', 23],
    ['videotex', 516],
]);

it('can tell i a port exists for a given protocol', function () {
    expect(new DefaultPorts())
        ->exists('http')->toBeTrue()
        ->exists('notexistingscheme')->toBeFalse();
});

it('returns the path for default ports', function () {
    expect((new DefaultPorts())->getStorePath())
        ->tobe(realpath(dirname(__DIR__) . '/data/default-ports.php'));
});
