<?php

use Kreait\Firebase\Contract\Firestore;
use Mockery;

trait MocksFirestore
{
    protected $firestoreMock;
    protected $databaseMock;
    
    protected function mockFirestore()
    {
        $this->firestoreMock = Mockery::mock(Firestore::class);
        $this->databaseMock = Mockery::mock('database');
        $this->firestoreMock->shouldReceive('database')->andReturn($this->databaseMock);
        
        // If using Laravel
        $this->app->instance(Firestore::class, $this->firestoreMock);
        
        return $this->firestoreMock;
    }
    
    protected function mockCollection($name)
    {
        $collectionMock = Mockery::mock('collection');
        $this->databaseMock->shouldReceive('collection')->with($name)->andReturn($collectionMock);
        return $collectionMock;
    }
    
    protected function mockDocument($collectionMock, $id, $data = [], $exists = true)
    {
        $documentMock = Mockery::mock('document');
        $snapshotMock = Mockery::mock('snapshot');
        
        $collectionMock->shouldReceive('document')->with($id)->andReturn($documentMock);
        $documentMock->shouldReceive('snapshot')->andReturn($snapshotMock);
        $snapshotMock->shouldReceive('exists')->andReturn($exists);
        $snapshotMock->shouldReceive('data')->andReturn($data);
        $snapshotMock->shouldReceive('id')->andReturn($id);
        
        return $documentMock;
    }
}