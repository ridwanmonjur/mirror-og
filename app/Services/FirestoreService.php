<?php

namespace App\Services;

use Kreait\Firebase\Contract\Firestore;
use Google\Cloud\Firestore\FieldValue;

class FirestoreService
{
    /**
     * @var Firestore
     */
    protected $firestore;

    /**
     * Constructor to inject Firestore dependency
     *
     * @param Firestore $firestore
     */
    public function __construct(Firestore $firestore)
    {
        $this->firestore = $firestore;
    }

    
    /**
     * Create or overwrite multiple documents with specified IDs and customizable values
     *
     * @param string $baseId Base ID prefix for documents
     * @param int $count Number of documents to create
     * @param array $customValuesArray Array of custom values for each document
     * @param string $collectionName The collection name
     * @param array $specificIds Optional array of specific IDs to use instead of sequential ones
     * @return array Response with status and document references
     */
    public function createBatchDocuments(
        string| int $eventId,
        string $baseId, 
        int $count, 
        array $customValuesArray = [], 
        string $collectionName = 'matches',
        array $specificIds = []
    ) {
        $results = [];
        
        try {
            $firestoreDB = $this->firestore->database();
            
            $batch = $firestoreDB->batch();
            $docRefs = [];
            
            for ($i = 0; $i < $count; $i++) {
                $documentId = !empty($specificIds[$i]) ? $specificIds[$i] : $baseId . '_' . ($i + 1);
                $customValues = $customValuesArray[$i] ?? [];
                
                $defaultDocument = [
                    'completeMatchStatus' => 'UPCOMING',
                    'defaultWinners' => [null, null, null],
                    'disputeResolved' => [null, null, null],
                    'disqualified' => false,
                    'matchStatus' => ['UPCOMING', 'UPCOMING', 'UPCOMING'],
                    'organizerWinners' => [null, null, null],
                    'position' => null,
                    'randomWinners' => [null, null, null],
                    'realWinners' => [null, null, null],
                    'score' => [0, 0],
                    'team1Id' => null,
                    'team1Winners' => [null, null, null],
                    'team2Id' => null,
                    'team2Winners' => [null, null, null]
                ];
                
                $documentData = array_merge($defaultDocument, $customValues);
                
                $docRef = $firestoreDB->collection('event')
                    ->document((string)$eventId)
                    ->collection('brackets')
                    ->document($documentId);                
                $batch->set($docRef, $documentData, ['merge' => false]); // Ensure complete overwrite
                
                $docRefs[$documentId] = $docRef;
                $results[] = [
                    'status' => 'pending',
                    'documentId' => $documentId,
                    'documentPath' => $collectionName.'/'.$documentId
                ];
            }
            
            // Commit the batch
            $batch->commit();
            
            // Update results to success after commit
            foreach ($results as &$result) {
                $result['status'] = 'success';
                $result['message'] = 'Document created or overwritten successfully';
            }
            
            return [
                'status' => 'success',
                'message' => 'Batch operation completed - all documents created or overwritten',
                'results' => $results
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'results' => $results
            ];
        }
    }
}