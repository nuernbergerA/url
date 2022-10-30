<?php

use Crwlr\Url\Validator;

test('valid urls', function (string $url, string $expectedValidUrl) {
    expect(Validator::url($url))
        ->toBe($expectedValidUrl);
})->with([
    'regular' => ['https://www.crwlr.software/packages/url/v0.1.2#installation', 'https://www.crwlr.software/packages/url/v0.1.2#installation'],
    'special characters' => ['https://u§er:pássword@sub.domäin.example.org:345/föô/bár?quär.y=strïng#frägmänt', 'https://u%C2%A7er:p%C3%A1ssword@sub.xn--domin-ira.example.org:345' . '/f%C3%B6%C3%B4/b%C3%A1r?qu%C3%A4r.y=str%C3%AFng#fr%C3%A4gm%C3%A4nt'],
]);

test('invalid urls', function (string $url) {
    expect(Validator::url($url))
        ->toBeNull();
})->with([
    '1http://example.com/stuff',
    '  https://wwww.example.com  ',
    'http://',
    'http://.',
    'https://..',
    'https://../',
    'http://?',
    'http://#',
    '//',
    '///foo',
    'http:///foo',
    '://',
]);

test('valid UrlAndComponents', function (string $url, array $expected) {
    expect(Validator::urlAndComponents($url))
        ->toBeArray()
        ->toMatchArray($expected);
})->with([
    [
        'https://www.crwlr.software/packages/url/v0.1.2#installation',
        [
            'url' => 'https://www.crwlr.software/packages/url/v0.1.2#installation',
            'scheme' => 'https',
            'host' => 'www.crwlr.software',
            'path' => '/packages/url/v0.1.2',
            'fragment' => 'installation',
        ],
    ],
    [
        '/foo/bar?query=string#fragment',
        [
            'url' => '/foo/bar?query=string#fragment',
            'path' => '/foo/bar',
            'query' => 'query=string',
            'fragment' => 'fragment',
        ],
    ],
    [
        'ftp://username:password@example.org',
        [
            'url' => 'ftp://username:password@example.org',
            'scheme' => 'ftp',
            'user' => 'username',
            'pass' => 'password',
            'host' => 'example.org',
        ],
    ],
    [
        'mailto:you@example.com?subject=crwlr software',
        ['url' => 'mailto:you@example.com?subject=crwlr%20software'],
    ],
    [
        'http://✪df.ws/123',
        [
            'url' => 'http://xn--df-oiy.ws/123',
            'scheme' => 'http',
            'host' => 'xn--df-oiy.ws',
            'path' => '/123',
        ],
    ],
    [
        'https://www.example.онлайн/stuff',
        [
            'url' => 'https://www.example.xn--80asehdb/stuff',
            'scheme' => 'https',
            'host' => 'www.example.xn--80asehdb',
            'path' => '/stuff',
        ],
    ],
]);

test('invalid UrlAndComponents', function (string $url) {
    expect(Validator::urlAndComponents($url))
        ->toBeNull();
})->with([
    '1http://example.com/stuff',
    '  https://wwww.example.com  ',
    'http://',
    'http://.',
    'https://..',
    'https://../',
    'http://?',
    'http://#',
    '//',
    '///foo',
    'http:///foo',
    '://',
]);

test('AbsoluteUrl', function (string $url, ?string $expected) {
    expect(Validator::absoluteUrl($url))
        ->toBe($expected);
})->with([
    'valid' => [
        'https://www.crwlr.software/packages/url/v0.1.2#installation',
        'https://www.crwlr.software/packages/url/v0.1.2#installation',
    ],
    'invalid' => [
        '/foo/bar?query=string#fragment',
        null,
    ],
]);

test('ValidateAbsoluteUrlAndComponents', function() {
    expect(Validator::absoluteUrlAndComponents('https://www.crwlr.software/packages/url/v0.1.2#installation'))
        ->toBeArray()
        ->toMatchArray([
            'url' => 'https://www.crwlr.software/packages/url/v0.1.2#installation',
            'scheme' => 'https',
            'host' => 'www.crwlr.software',
            'path' => '/packages/url/v0.1.2',
            'fragment' => 'installation',
        ]);

    expect(Validator::absoluteUrlAndComponents('/foo/bar?query=string#fragment'))
        ->toBeNull();
});

test('Scheme', function (string $scheme, ?string $expected) {
    expect(Validator::scheme($scheme))
        ->toBe($expected);
})->with([
    ['http', 'http'],
    ['mailto', 'mailto'],
    ['ssh', 'ssh'],
    ['ftp', 'ftp'],
    ['sftp', 'sftp'],
    ['wss', 'wss'],
    ['HTTPS', 'https'],
    ['1invalidscheme', null],
    ['mäilto', null],
]);

test('Authority', function(string $authority, ?string $expected) {
    expect(Validator::authority($authority))
        ->toBe($expected);
})->with([
    ['12.34.56.78','12.34.56.78'],
    ['localhost','localhost'],
    ['www.example.com:8080','www.example.com:8080'],
    ['user:password@www.example.org:1234','user:password@www.example.org:1234'],
    ['user:password@:1234', null],
    ['', null],
]);

test('ValidateAuthorityComponents', function () {
    expect(Validator::authorityComponents('user:password@www.example.org:1234'))
        ->toBeArray()
        ->toMatchArray([
            'userInfo' => 'user:password',
            'user' => 'user',
            'password' => 'password',
            'host' => 'www.example.org',
            'port' => 1234,
        ]);
});

test('ValidateInvalidAuthorityComponents', function (string $authority) {
    expect(Validator::authorityComponents($authority))
        ->toBeNull();
})->with([
    'user:password@:1234',
    '',
]);

test('ValidateUserInfo', function (string $userInfo, ?string $expectedComponents) {
    expect(Validator::userInfo($userInfo))
        ->toBe($expectedComponents);
})->with([
    ['user:password', 'user:password'],
    ['u§er:pássword', 'u%C2%A7er:p%C3%A1ssword'],
    [':password', null],
]);

test('ValidateUserInfoComponents', function () {
    expect(Validator::userInfoComponents('crwlr:software'))
        ->toBeArray()
        ->toMatchArray([
            'user' => 'crwlr',
            'password' => 'software',
        ]);

    expect(Validator::userInfoComponents('u§er:pássword'))
        ->toBeArray()
        ->toMatchArray([
            'user' => 'u%C2%A7er',
            'password' => 'p%C3%A1ssword',
        ]);

    expect(Validator::userInfoComponents(':password'))
        ->toBeNull();
});

test('ValidateUser', function (string $input, string $expected) {
    expect(Validator::user($input))
        ->toBe($expected);
})->with([
    ['user', 'user'],
    ['user-123', 'user-123'],
    ['user_123', 'user_123'],
    ['user%123', 'user%123'],
    ['u$3r_n4m3!', 'u$3r_n4m3!'],
    ['u$3r\'$_n4m3', 'u$3r\'$_n4m3'],
    ['u$3r*n4m3', 'u$3r*n4m3'],
    ['u$3r,n4m3', 'u$3r,n4m3'],
    ['=u$3r=', '=u$3r='],
    ['u§3rname', 'u%C2%A73rname'],
    ['user:name', 'user%3Aname'],
    ['Üsernäme', '%C3%9Csern%C3%A4me'],
    ['user°name', 'user%C2%B0name'],
    ['<username>', '%3Cusername%3E'],
    ['usern@me', 'usern%40me'],
    ['us€rname', 'us%E2%82%ACrname'],
]);

test('ValidatePassword', function (string $password, string $expected) {
    expect(Validator::password($password))->toBe($expected);
    expect(Validator::pass($password))->toBe($expected);
})->with([
    ['pASS123', 'pASS123'],
    ['P4ss.123', 'P4ss.123'],
    ['p4ss~123', 'p4ss~123'],
    ['p4ss-123!', 'p4ss-123!'],
    ['p4$$&w0rD', 'p4$$&w0rD'],
    ['(p4$$-w0rD)', '(p4$$-w0rD)'],
    ['p4$$+W0rD', 'p4$$+W0rD'],
    ['P4ss;w0rd', 'P4ss;w0rd'],
    ['"password"', '%22password%22'],
    ['pass`word', 'pass%60word'],
    ['pass^word', 'pass%5Eword'],
    ['pass🤓moji', 'pass%F0%9F%A4%93moji'],
    ['pass\word', 'pass%5Cword'],
    ['paßword', 'pa%C3%9Fword'],
]);

test('ValidateHost', function (string $host, string $expected) {
    expect(Validator::host($host))
        ->toBe($expected);
})->with([
    ['example.com', 'example.com'],
    ['www.example.com', 'www.example.com'],
    ['www.example.com.', 'www.example.com.'],
    ['subdomain.example.com', 'subdomain.example.com'],
    ['www.some-domain.io', 'www.some-domain.io'],
    ['123456.co.uk', '123456.co.uk'],
    ['WWW.EXAMPLE.COM', 'www.example.com'],
    ['www-something.blog', 'www-something.blog'],
    ['h4ck0r.software', 'h4ck0r.software'],
    ['g33ks.org', 'g33ks.org'],
    ['example.онлайн', 'example.xn--80asehdb'],
    ['example.xn--80asehdb', 'example.xn--80asehdb'],
    ['www.са.com', 'www.xn--80a7a.com'], // Fake "a" in ca.com => idn domain
    ['12.34.56.78', '12.34.56.78'],
    ['localhost', 'localhost'],
    ['dev.local', 'dev.local'],
]);

test('invalid host', function (string $host) {
    expect(Validator::host($host))
        ->toBeNull();
})->with([
    'slash/example.com',
    'exclamation!mark.co',
    'question?mark.blog',
    'under_score.org',
    'www.(parenthesis).net',
    'idk.amper&sand.uk',
    'equals=.ch',
    'apostrophe\'.at',
    'one+one.mobile',
    'hash#tag.social',
    'co:lon.com',
    'semi;colon.net',
    '<html>.codes',
    'www..com',
]);

test('ValidateDomainSuffix', function (string $suffix, ?string $expected) {
   expect(Validator::domainSuffix($suffix))
       ->toBe($expected);
})->with([
    ['com', 'com'],
    ['org', 'org'],
    ['net', 'net'],
    ['blog', 'blog'],
    ['codes', 'codes'],
    ['wtf', 'wtf'],
    ['sexy', 'sexy'],
    ['tennis', 'tennis'],
    ['versicherung', 'versicherung'],
    ['点看', 'xn--3pxu8k'],
    ['онлайн', 'xn--80asehdb'],
    ['大拿', 'xn--pssy2u'],
    ['co.uk', 'co.uk'],
    ['co.at', 'co.at'],
    ['or.at', 'or.at'],
    ['anything.bd', 'anything.bd'],
    ['süffix', null],
    ['idk', null],
]);

test('ValidateDomain', function (string $domain, ?string $expected) {
    expect(Validator::domain($domain))
        ->toBe($expected);
})->with([
    ['google.com', 'google.com'],
    ['example.xn--80asehdb', 'example.xn--80asehdb'],
    ['example.онлайн', 'example.xn--80asehdb'],
    ['www.google.com', null],
    ['yolo', null],
    ['subdomain.example.онлайн', null],
]);

test('ValidateDomainLabel', function (string $domainLabel, ?string $expected) {
    expect(Validator::domainLabel($domainLabel))
    ->toBe($expected);
})->with([
    ['yolo', 'yolo'],
    ['männersalon', 'xn--mnnersalon-q5a'],
    ['yo!lo', null],
    ['', null],
]);

test('ValidateSubdomain', function (string $subdomain, ?string $expected) {
    expect(Validator::subdomain($subdomain))
        ->toBe($expected);
})->with([
    ['www', 'www'],
    ['sub.domain', 'sub.domain'],
    ['SUB.DO.MAIN', 'sub.do.main'],
    ['sub_domain', null],
]);

test('ValidatePort', function (int $port, ?int $expected) {
    expect(Validator::port($port))
        ->toBe($expected);
})->with([
    [0, 0],
    [8080, 8080],
    [65535, 65535],
    [-1, null],
    [65536, null],
]);

test('ValidatePath', function (string $path, string $expected) {
    expect(Validator::path($path))
        ->toBe($expected);
})->with([
    ['/FoO/bAr', '/FoO/bAr'],
    ['/foo-123/bar_456', '/foo-123/bar_456'],
    ['/~foo/!bar$/&baz\'', '/~foo/!bar$/&baz\''],
    ['/(foo)/*bar+', '/(foo)/*bar+'],
    ['/foo,bar;baz:', '/foo,bar;baz:'],
    ['/foo=bar@baz', '/foo=bar@baz'],
    ['/"foo"', '/%22foo%22'],
    ['/foo\\bar', '/foo%5Cbar'],
    ['/bößer/pfad', '/b%C3%B6%C3%9Fer/pfad'],
    ['/<html>', '/%3Chtml%3E'],
    ['/foo%bar', '/foo%bar'], // Percent character not encoded (to %25) because %ba could be legitimate percent encoded character.
    ['/foo%gar', '/foo%25gar'], // Percent character encoded because %ga isn't a valid percent encoded character.
]);

test('ValidateQuery', function (string $query, string $expected) {
    expect(Validator::query($query))
        ->toBe($expected);
})->with([
    ['foo=bar', 'foo=bar'],
    ['?foo=bar', 'foo=bar'],
    ['foo1=bar&foo2=baz', 'foo1=bar&foo2=baz'],
    ['.foo-=_bar~', '.foo-=_bar~'],
    ['%foo!=$bar\'', '%25foo!=$bar\''],
    ['(foo)=*bar+', '(foo)=*bar+'],
    ['f,o;o==bar:', 'f,o;o==bar:'],
    ['?@foo=/bar?', '@foo=/bar%3F'],
    ['"foo"=bar', '%22foo%22=bar'],
    ['foo#=bar', 'foo%23=bar'],
    ['föo=bar', 'f%C3%B6o=bar'],
    ['boeßer=query', 'boe%C3%9Fer=query'],
    ['foo`=bar', 'foo%60=bar'],
    ['foo%25bar=baz', 'foo%25bar=baz'],
]);

test('ValidateFragment', function (string $fragment, string $expected) {
    expect(Validator::fragment($fragment))
        ->toBe($expected);
})->with([
    ['fragment', 'fragment'],
    ['#fragment', 'fragment'],
    ['fragment1234567890', 'fragment1234567890'],
    ['-.fragment_~', '-.fragment_~'],
    ['%!fragment$&', '%25!fragment$&'],
    ['(\'fragment*)', '(\'fragment*)'],
    ['#+,fragment;:', '+,fragment;:'],
    ['@=fragment/?', '@=fragment/?'],
    ['#"fragment"', '%22fragment%22'],
    ['#fragment#', 'fragment%23'],
    ['##fragment', '%23fragment'],
    ['frägment', 'fr%C3%A4gment'],
    ['boeßesfragment', 'boe%C3%9Fesfragment'],
    ['fragment`', 'fragment%60'],
    ['fragm%E2%82%ACnt', 'fragm%E2%82%ACnt'],
]);
