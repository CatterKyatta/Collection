<?php

declare(strict_types=1);

namespace App\Tests\Api\Photo;

use Api\Tests\ApiTestCase;
use App\Entity\Photo;
use Symfony\Component\HttpFoundation\Response;

class PhotoOtherUserTest extends ApiTestCase
{
    public function testCantGetAnotherUserPhoto(): void
    {
        $photo = $this->em->getRepository(Photo::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($photo);

        $this->createClientWithCredentials()->request('GET', $iri);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPutAnotherUserPhoto(): void
    {
        $photo = $this->em->getRepository(Photo::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($photo);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'title' => 'updated title with PUT',
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantGetAnotherUserPhotoAlbum(): void
    {
        $photo = $this->em->getRepository(Photo::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($photo);

        $this->createClientWithCredentials()->request('GET', $iri.'/album');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantPatchAnotherUserPhoto(): void
    {
        $photo = $this->em->getRepository(Photo::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($photo);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'title' => 'updated title with PATCH',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCantDeleteAnotherUserPhoto(): void
    {
        $photo = $this->em->getRepository(Photo::class)->findBy(['owner' => $this->otherUser], [], 1)[0];
        $iri = $this->iriConverter->getIriFromResource($photo);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
