<?php

declare(strict_types=1);

namespace App\Tests\Api\Inventory;

use Api\Tests\ApiTestCase;
use App\Entity\Inventory;
use Symfony\Component\HttpFoundation\Response;

class InventoryCurrentUserTest extends ApiTestCase
{
    public function testGetInventories(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/inventories');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(5, $data['hydra:totalItems']);
        $this->assertCount(5, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Inventory::class);
    }

    public function testGetInventory(): void
    {
        $inventory = $this->em->getRepository(Inventory::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($inventory);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
        ]);
    }

    public function testPostInventory(): void
    {
        $this->createClientWithCredentials()->request('POST', '/api/inventories', [
            'json' => [
                'name' => 'New inventory',
                'content' => '{}',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'name' => 'New inventory',
        ]);
    }

    public function testPutInventory(): void
    {
        $inventory = $this->em->getRepository(Inventory::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($inventory);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'name' => 'updated name with PUT',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'updated name with PUT',
        ]);
    }

    public function testPatchInventory(): void
    {
        $inventory = $this->em->getRepository(Inventory::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($inventory);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'updated name with PATCH',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'updated name with PATCH',
        ]);
    }

    public function testDeleteInventory(): void
    {
        $inventory = $this->em->getRepository(Inventory::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($inventory);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
