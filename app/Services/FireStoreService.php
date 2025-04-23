<?php

namespace App\Services;

use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Factory;

class FirestoreService
{
    protected FirestoreClient $firestore;

    public function __construct()
    {
        $credentialsPath = env('FIREBASE_CREDENTIALS', storage_path('firebase/firebase_credentials.json'));

        $factory = new Factory();
        $this->firestore = $factory
            ->withServiceAccount($credentialsPath)
            ->createFirestore()
            ->database();
    }

    public function setMatchStatus(string $collection, string $documentId, string $status): void
    {
        $this->firestore->collection($collection)->document($documentId)->update([
            ['path' => 'status', 'value' => $status]
        ]);
    }

    public function getMatch(string $collection, string $documentId): array
    {
        return $this->firestore->collection($collection)->document($documentId)->snapshot()->data();
    }

    // Add other methods as needed
}
