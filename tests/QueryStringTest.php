<?php

use Crwlr\QueryString\Query;
use Crwlr\Url\Url;

it('returns an instance of query when PHP version 8 or above otherwise throws exception', function () {
    $url = Url::parse('https://www.example.com/path?foo=bar');

    if (PHP_VERSION_ID >= 80000 && class_exists(Query::class)) {
        expect($url->queryString())->toBeInstanceOf(Query::class);
    } else {
        $this->expectException(Exception::class);

        $url->queryString();
    }
});

test('the query method return value is in sync with query string instance', function () {
    $url = Url::parse('https://www.example.com/path?foo=bar');
    $url->queryString()->set('baz', 'quz');

    expect($url)
        ->query()->toBe('foo=bar&baz=quz')
        ->__toString->toBe('https://www.example.com/path?foo=bar&baz=quz');
})->skip(PHP_VERSION_ID < 80000 || ! class_exists(Query::class));

test('the query array method return value is in sync with query string instance', function () {
    $url = Url::parse('https://www.example.com/path?foo=bar');
    $url->queryString()->set('baz', 'quz');

    expect($url)->queryArray()->toBe(['foo' => 'bar', 'baz' => 'quz']);
    expect($url)->__toString->toBe('https://www.example.com/path?foo=bar&baz=quz');
})->skip(PHP_VERSION_ID < 80000 || ! class_exists(Query::class));

test('the query string can be accessed via magic getter', function () {
    $url = Url::parse('https://www.example.com/path?foo=bar');
    $url->queryString()->set('baz', 'quz');

    expect($url)
        ->queryArray()->toBe(['foo' => 'bar', 'baz' => 'quz'])
        ->__toString->toBe('https://www.example.com/path?foo=bar&baz=quz');
})->skip(PHP_VERSION_ID < 80000 || ! class_exists(Query::class));

it('still works to set the query via query method after query string was used', function () {
    $url = Url::parse('https://www.example.com/path?foo=bar');
    $url->queryString()->set('baz', 'quz');
    $url->query('yo=lo');

    expect($url)
        ->query()->toBe('yo=lo')
        ->queryArray()->toBe(['yo' => 'lo'])
        ->__toString->toBe('https://www.example.com/path?yo=lo');
})->skip(PHP_VERSION_ID < 80000 || ! class_exists(Query::class));

it('still works to set the query via query array method after query string was used', function () {
    $url = Url::parse('https://www.example.com/path?foo=bar');
    $url->queryString()->set('baz', 'quz');
    $url->queryArray(['boo' => 'yah']);

    expect($url)
        ->query()->toBe('boo=yah')
        ->queryArray()->toBe(['boo' => 'yah'])
        ->__toString->toBe('https://www.example.com/path?boo=yah');
})->skip(PHP_VERSION_ID < 80000 || ! class_exists(Query::class));
