<?php

declare(strict_types=1);

namespace App\Tests;

/**
 * Class AdminAccessFunctionnalTest
 *
 * @package App\Tests
 */
class UserSmokeFunctionnalTest extends LoggedWebTestCase
{
    /**
     * @dataProvider isSuccessfulUrlProvider
     */
    public function testPageIsSuccessful(string $url)
    {
        $this->login('cthulhu@koillection.com');
        $this->client->request('GET', $this->replaceUrlParameters($url));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider isNotFoundUrlProvider
     */
    public function testPageIsNotFound(string $url)
    {
        $this->login('cthulhu@koillection.com');
        $this->client->request('GET', $this->replaceUrlParameters($url));
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function isSuccessfulUrlProvider()
    {
        yield ["/collections"];
        yield ["/collections/add"];
        yield ["/tags"];
        yield ["/wishlists"];
        yield ["/albums"];
        yield ["/albums/add"];
        yield ["/statistics"];
        yield ["/loans"];
        yield ["/templates"];
        yield ["/templates/add"];
        yield ["/settings"];
        yield ["/profile"];
        yield ["/history"];
        yield ["/preview"];
        yield ["/user/{{username}}"];
        yield ["/tools"];
        yield ["/inventories/add"];
        yield ["/inventories/{{inventory}}"];
    }

    public function isNotFoundUrlProvider()
    {
        yield ["/admin"];
        yield ["/admin/users"];
        yield ["/admin/_trans"];
    }
}
