<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Firebase;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Firestore;


class FirebaseController extends Controller
{
    private $auth;
    private $firestore;


    public function __construct(Auth $auth, Firestore $firestore) 
    {
        $this->auth = $auth;
        $this->firestore = $firestore;
    }

    public function createToken()
    {
        if (session()->has('firebase_token')) {
            $tokenData = session('firebase_token');
            
            if (time() - $tokenData['created_at'] < 3600) {
                return [
                    'token' => $tokenData['token'],
                    'claims' => $tokenData['claims'],
                    'from_session' => true
                ];
            }
        }

        try {
            $user = auth()->user();
            $claims = [
                'uid' => $user->id,
                'email' => $user->email, 
                'role' => $user->role
            ];
              
            $token = $this->auth->createCustomToken($user->id, $claims)->toString();
            session([
                'firebase_token' => [
                    'token' => $token,
                    'claims' => $claims,
                    'created_at' => time()
                ]
            ]);

            return [
                'token' => $token,
                'claims' => $claims,
                'from_session' => false
            ]; 
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function toggleBlock(Request $request, $id): JsonResponse
    {
        $authenticatedUser = $request->attributes->get('user');
        $user = User::where('id', $id)
            ->select('id')
            ->first();
        
        if ($authenticatedUser->id == $user->id) {

            return response()->json([
                'message' => "Can't block yourself",
                'is_blocked' => "False"
            ], 404);
        }
        
        if ($authenticatedUser->hasBlocked($user)) {
            $authenticatedUser->blocks()->detach($user);
            $message = 'User unblocked successfully';
            $isBlocked = false;

            $roomCollectionRef = $this->firestore->database()->collection('room');

            $query1 = $roomCollectionRef->where('user1', '==', (string)$user->id)
                ->where('user2', '==', (string)$authenticatedUser->id)
                ->where('blocked_by', '==', (string)$authenticatedUser->id);

            $query2 = $roomCollectionRef->where('user2', '==', (string)$user->id)
                ->where('user1', '==', (string)$authenticatedUser->id)
                ->where('blocked_by', '==', (string)$authenticatedUser->id);

            $snapshot1 = $query1->documents();
            $snapshot2 = $query2->documents();
           
            foreach ($snapshot1 as $document) {
                $roomRef = $roomCollectionRef->document($document->id());
                $roomRef->update([
                    ['path' => 'blocked_by', 'value' => null]
                ]);            
            }

            foreach ($snapshot2 as $document) {
                $roomRef = $roomCollectionRef->document($document->id());
                $roomRef->update([
                    ['path' => 'blocked_by', 'value' => null]
                ]);
            }
        } else {
            $authenticatedUser->blocks()->attach($user);
            $message = 'User blocked successfully';
            $isBlocked = true;

            if (!$user->hasBlocked($authenticatedUser)) { 
                $roomCollectionRef = $this->firestore->database()->collection('room');
                
                $query1 = $roomCollectionRef->where('user1', '==', (string)$user->id)
                    ->where('user2', '==', (string)$authenticatedUser->id);
                
                $query2 = $roomCollectionRef->where('user2', '==', (string)$user->id)
                    ->where('user1', '==', (string)$authenticatedUser->id);
                
                $snapshot1 = $query1->documents();
                $snapshot2 = $query2->documents();

                foreach ($snapshot1 as $document) {
                    $roomRef = $roomCollectionRef->document($document->id());
                    $roomRef->update([
                        ['path' => 'blocked_by', 'value' => $authenticatedUser->id]
                    ]);                }
                
                foreach ($snapshot2 as $document) {
                    $roomRef = $roomCollectionRef->document($document->id());
                    $roomRef->update([
                        ['path' => 'blocked_by', 'value' => $authenticatedUser->id]
                    ]);                
                }
            }
        }

        return response()->json([
            'message' => $message,
            'is_blocked' => $isBlocked
        ]);
    }
}