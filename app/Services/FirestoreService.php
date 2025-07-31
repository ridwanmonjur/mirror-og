<?php

namespace App\Services;

use Kreait\Firebase\Contract\Firestore;
use Google\Cloud\Firestore\FieldValue;
use Illuminate\Support\Collection;

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
     * Create or overwrite multiple reports individually (fallback for batch issues)
     *
     * @param string|int $eventId Event ID
     * @param int $count Number of reports to create
     * @param array $customValuesArray Array of custom values for each report
     * @param array $specificIds Array of specific IDs to use
     * @return array Response with status and report references
     */
    public function createIndividualReports(
        string|int $eventId,
        int $count, 
        array $customValuesArray = [], 
        array $specificIds = []
    ) {
        $results = [];
        
        try {
            $firestoreDB = $this->firestore->database();
            
            for ($i = 0; $i < $count; $i++) {
                $reportId = $specificIds[$i];
                $customValues = $customValuesArray[$i] ?? [];
                
                $defaultReport = [
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
                
                // Clean the custom values to prevent circular references
                $cleanCustomValues = json_decode(json_encode($customValues), true);
                $reportData = array_merge($defaultReport, $cleanCustomValues);
                
                $docRef = $firestoreDB->collection('event')
                    ->document((string)$eventId)
                    ->collection('brackets')
                    ->document($reportId);
                    
                $docRef->set($reportData, ['merge' => false]);
                
                $results[] = [
                    'statusReport' => 'success',
                    'reportId' => $reportId,
                    'messageReport' => 'Report created or overwritten successfully'
                ];
            }
            
            return [
                'statusReport' => 'success',
                'messageReport' => 'Individual operation completed - all reports created or overwritten',
                'resultsReport' => $results
            ];
        } catch (\Exception $e) {
            error_log("FirestoreService individual write error: " . $e->getMessage());
            error_log("Error trace: " . $e->getTraceAsString());
            
            return [
                'statusReport' => 'error',
                'messageReport' => $e->getMessage(),
                'resultsReport' => $results
            ];
        }
    }

    /**
     * Create or overwrite multiple reports with specified IDs and customizable values
     *
     * @param string $baseId Base ID prefix for reports
     * @param int $count Number of reports to create
     * @param array $customValuesArray Array of custom values for each report
     * @param array $specificIds Optional array of specific IDs to use instead of sequential ones
     * @return array Response with status and report references
     */
    public function createBatchReports(
        string| int $eventId,
        int $count, 
        array $customValuesArray = [], 
        array $specificIds = []
    ) {
        $results = [];
        
        try {
            $firestoreDB = $this->firestore->database();
            
            $batch = $firestoreDB->batch();
            $docRefs = [];
            
            for ($i = 0; $i < $count; $i++) {
                $reportId = $specificIds[$i] ;
                $customValues = $customValuesArray[$i] ?? [];
                
                $defaultReport = [
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
                
                // Clean the custom values to prevent circular references
                $cleanCustomValues = json_decode(json_encode($customValues), true);
                $reportData = array_merge($defaultReport, $cleanCustomValues);
                
                $docRef = $firestoreDB->collection('event')
                    ->document((string)$eventId)
                    ->collection('brackets')
                    ->document($reportId);                
                $batch->set($docRef, $reportData, ['merge' => false]);
                
                $docRefs[$reportId] = $docRef;
                $results[] = [
                    'statusReport' => 'pending',
                    'reportId' => $reportId,
                ];
            }
            
            // Add debug logging before commit
            error_log("FirestoreService: About to commit batch with " . count($docRefs) . " documents");
            
            // Commit the batch
            $batch->commit();
            
            // Update results to success after commit
            foreach ($results as &$result) {
                $result['statusReport'] = 'success';
                $result['messageReport'] = 'Report created or overwritten successfully';
            }
            
            return [
                'statusReport' => 'success',
                'messageReport' => 'Batch operation completed - all reports created or overwritten',
                'resultsReport' => $results
            ];
        } catch (\Exception $e) {
            error_log("FirestoreService batch commit error: " . $e->getMessage());
            error_log("Error trace: " . $e->getTraceAsString());
            
            // If batch fails due to recursion or other issues, try individual writes
            if (strpos($e->getMessage(), 'recursion') !== false || 
                strpos($e->getMessage(), 'call stack') !== false) {
                error_log("FirestoreService: Falling back to individual writes due to recursion error");
                return $this->createIndividualReports($eventId, $count, $customValuesArray, $specificIds);
            }
            
            return [
                'statusReport' => 'error',
                'messageReport' => $e->getMessage(),
                'resultsReport' => $results
            ];
        }
    }

    /**
     * Create or overwrite multiple dispute documents with specified IDs
     *
     * @param string|int $eventId Event ID
     * @param int $count Number of disputes to create
     * @param array $customValuesArray Array of custom values for each dispute
     * @param array $specificIds Array of specific IDs to use for disputes
     * @return array Response with status and dispute references
     */
    public function createBatchDisputes(
        string|int $eventId,
        int $count, 
        array $customValuesArray = [], 
        array $specificIds = []
    ) {
        $results = [];
        
        try {
            $firestoreDB = $this->firestore->database();
            
            $batch = $firestoreDB->batch();
            $docRefs = [];
            
            for ($i = 0; $i < $count; $i++) {
                $disputeId = $specificIds[$i];
                $customValues = $customValuesArray[$i] ?? [];
                
                $defaultDispute = [
                    'created_at' => FieldValue::serverTimestamp(),
                    'dispute_description' => null,
                    'dispute_image_videos' => [],
                    'dispute_reason' => null,
                    'dispute_teamId' => null,
                    'dispute_teamNumber' => null,
                    'dispute_userId' => null,
                    'event_id' => (string)$eventId,
                    'match_number' => null,
                    'report_id' => null,
                    'resolution_resolved_by' => null,
                    'resolution_winner' => null,
                    'response_explanation' => null,
                    'response_teamId' => null,
                    'response_teamNumber' => null,
                    'response_userId' => null,
                    'updated_at' => FieldValue::serverTimestamp()
                ];
                
                $disputeData = array_merge($defaultDispute, $customValues);
                
                $docRef = $firestoreDB->collection('event')
                    ->document((string)$eventId)
                    ->collection('disputes')
                    ->document($disputeId);
                    
                $batch->set($docRef, $disputeData, ['merge' => false]); // Ensure complete overwrite
                
                $docRefs[$disputeId] = $docRef;
                $results[] = [
                    'statusDispute' => 'pending',
                    'disputeId' => $disputeId,
                ];
            }
            
            // Commit the batch
            $batch->commit();
            
            // Update results to success after commit
            foreach ($results as &$result) {
                $result['statusDispute'] = 'success';
                $result['messageDispute'] = 'Dispute created or overwritten successfully';
            }
            
            return [
                'statusDispute' => 'success',
                'messageDispute' => 'Batch operation completed - all disputes created or overwritten',
                'resultsDispute' => $results
            ];
        } catch (\Exception $e) {
            return [
                'statusDispute' => 'error',
                'messageDispute' => $e->getMessage(),
                'resultsDispute' => $results
            ];
        }
    }

    public function generateDisputes(string $eventId): array {
        $docRef = $this->firestore->database()->collection('event')->document($eventId)->collection('disputes');
        $documents = $docRef->documents();
        $disputes = [];
        
        foreach ($documents as $document) {
            if ($document->exists()) {
                $disputes[] = [...$document->data(), 'id' => $document->id()];
            }
        }

        return $disputes;
    }

    public function generateBrackets(Collection $matches, string| int $eventId): array {
        $allBracketDocs = $this->firestore->database()
            ->collection('event')
            ->document($eventId)
            ->collection('brackets')
            ->documents();

        $bracketDataByPath = [];
        foreach ($allBracketDocs as $doc) {
            $bracketDataByPath[$doc->id()] = $doc->data();
        }

        $brackets = [];
        foreach ($matches as $bracket) {
            
            $additionalData = [];
                $matchStatusPath = $bracket->team1_position . '.' . $bracket->team2_position;
                if (isset($bracketDataByPath[$matchStatusPath])) {
                    $additionalData = $bracketDataByPath[$matchStatusPath];
                } else {
                    $additionalData = [
                        "organizerWinners" => [null, null, null],
                        "disputeResolved" => [null, null, null],
                        "disqualified" => false,
                        "team2Winners" => [null, null, null],
                        "team1Winners" => [null, null, null],
                        "score" => [0,0],
                        "defaultWinners" => [null, null, null],
                        "position" => "U9",
                        "matchStatus" => [null, null, null],
                        "randomWinners" => [null, null, null],
                        "realWinners" => [null, null, null]
                    ];
                }
            
            $brackets[] = [
                ... (array) $bracket,
                ...$additionalData,
            ];
        }
        return $brackets;
    }
}