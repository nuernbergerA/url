<?php

use Crwlr\Url\Domain;

/**
 * The domain class is really simple and assumes input validation happens somewhere else, so this test
 * is rather short ;p
 */
test('valid', function () {
    expect(new Domain('example.com'))
        ->toBeInstanceOf(Domain::class)
        ->label()->toBe('example')
        ->suffix()->toBe('com')
        ->__toString()->toBe('example.com');
});

test('invalid', function () {
    expect(new Domain('notadomain'))
        ->toBeInstanceOf(Domain::class)
        ->label()->toBeNull()
        ->suffix()->toBeNull()
        ->__toString()->toBeEmpty();
});

it('can tell if its a Idn', function (string $domain, bool $expected) {
    expect((new Domain($domain))->isIdn())
        ->toBe($expected);
})->with([
    'example.com' => ['example.com', false],
    'ex-ample.com' => ['ex-ample.com', false],
    'männersalon.at' => ['xn--mnnersalon-q5a.at', true],
    'müller.de' => ['xn--mller-kva.de', true],
]);
