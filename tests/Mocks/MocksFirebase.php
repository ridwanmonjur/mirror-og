<?php

namespace Tests\Mocks;

use Kreait\Firebase\Contract\Firestore;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Mockery;

trait MocksFirebase
{
    use MocksFirestore; // Include existing Firestore mocking

    protected $firebaseAuthMock;

    /**
     * Mock Firebase Authentication
     */
    protected function mockFirebaseAuth()
    {
        $this->firebaseAuthMock = Mockery::mock(FirebaseAuth::class);
        $this->app->instance(FirebaseAuth::class, $this->firebaseAuthMock);

        return $this->firebaseAuthMock;
    }

    /**
     * Mock Firebase user creation
     */
    protected function mockFirebaseUserCreation($uid, $email, $displayName = null)
    {
        $userRecord = Mockery::mock('UserRecord');
        $userRecord->uid = $uid;
        $userRecord->email = $email;
        $userRecord->displayName = $displayName ?? $email;

        $this->firebaseAuthMock
            ->shouldReceive('createUser')
            ->andReturn($userRecord);

        return $userRecord;
    }

    /**
     * Mock Firestore document set operation
     */
    protected function mockFirestoreDocumentSet($collection, $documentId, $data)
    {
        $collectionMock = $this->mockCollection($collection);
        $documentMock = Mockery::mock('document');

        $collectionMock
            ->shouldReceive('document')
            ->with($documentId)
            ->andReturn($documentMock);

        $documentMock
            ->shouldReceive('set')
            ->with($data)
            ->andReturn(true);

        return $documentMock;
    }

    /**
     * Mock Firestore document update operation
     */
    protected function mockFirestoreDocumentUpdate($collection, $documentId, $data)
    {
        $collectionMock = $this->mockCollection($collection);
        $documentMock = Mockery::mock('document');

        $collectionMock
            ->shouldReceive('document')
            ->with($documentId)
            ->andReturn($documentMock);

        $documentMock
            ->shouldReceive('update')
            ->with($data)
            ->andReturn(true);

        return $documentMock;
    }

    /**
     * Mock Firestore query operation
     */
    protected function mockFirestoreQuery($collection, $field, $operator, $value, $results = [])
    {
        $collectionMock = $this->mockCollection($collection);
        $queryMock = Mockery::mock('query');
        $snapshotMock = Mockery::mock('snapshot');

        $collectionMock
            ->shouldReceive('where')
            ->with($field, $operator, $value)
            ->andReturn($queryMock);

        $queryMock
            ->shouldReceive('documents')
            ->andReturn($snapshotMock);

        // Mock result rows
        $snapshotMock
            ->shouldReceive('rows')
            ->andReturn(collect($results));

        $snapshotMock
            ->shouldReceive('isEmpty')
            ->andReturn(empty($results));

        return $snapshotMock;
    }

    /**
     * Mock Firestore document delete
     */
    protected function mockFirestoreDocumentDelete($collection, $documentId)
    {
        $collectionMock = $this->mockCollection($collection);
        $documentMock = Mockery::mock('document');

        $collectionMock
            ->shouldReceive('document')
            ->with($documentId)
            ->andReturn($documentMock);

        $documentMock
            ->shouldReceive('delete')
            ->andReturn(true);

        return $documentMock;
    }

    /**
     * Mock batch write operation
     */
    protected function mockFirestoreBatchWrite($operations = [])
    {
        $batchMock = Mockery::mock('batch');

        $this->databaseMock
            ->shouldReceive('batch')
            ->andReturn($batchMock);

        foreach ($operations as $operation) {
            $batchMock->shouldReceive($operation['type'])
                ->andReturnSelf();
        }

        $batchMock->shouldReceive('commit')
            ->andReturn(true);

        return $batchMock;
    }

    /**
     * Cleanup Firebase mocks
     */
    protected function tearDownFirebaseMocks()
    {
        if (class_exists(Mockery::class)) {
            Mockery::close();
        }
    }
}
