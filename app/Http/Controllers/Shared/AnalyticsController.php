<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Services\GoogleAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    private $analyticsService;

    public function __construct(GoogleAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get all event views with counts
     * GET /api/analytics/events
     */
    public function getAllEvents(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'nullable|date|before_or_equal:today',
                'end_date' => 'nullable|date|before_or_equal:today|after_or_equal:start_date',
            ]);

            $events = $this->analyticsService->getAllEventViews(
                $validated['start_date'] ?? null,
                $validated['end_date'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $events,
                'total' => count($events),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch events', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch events',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get events grouped by dimension (games, tiers, esports, etc.)
     * GET /api/analytics/events/by/{dimension}
     */
    public function getEventsByDimension(Request $request, string $dimension): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'nullable|date|before_or_equal:today',
                'end_date' => 'nullable|date|before_or_equal:today|after_or_equal:start_date',
            ]);

            $allowedDimensions = ['events', 'games', 'tiers', 'esports', 'locations', 'types'];

            if (! in_array($dimension, $allowedDimensions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid dimension',
                    'allowed_dimensions' => $allowedDimensions,
                ], 400);
            }

            $data = $this->analyticsService->getEventsByDimension(
                $dimension,
                $validated['start_date'] ?? null,
                $validated['end_date'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch events by dimension', [
                'dimension' => $dimension,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch events by dimension',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get specific event details
     * GET /api/analytics/events/{eventId}
     */
    public function getEventDetails(Request $request, string $eventId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'nullable|date|before_or_equal:today',
                'end_date' => 'nullable|date|before_or_equal:today|after_or_equal:start_date',
            ]);

            $details = $this->analyticsService->getEventDetails(
                $eventId,
                $validated['start_date'] ?? null,
                $validated['end_date'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $details,
                'total' => array_sum(array_column($details, 'count')),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch event details', [
                'event_id' => $eventId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch event details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get summary statistics
     * GET /api/analytics/summary
     */
    public function getSummary(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'nullable|date|before_or_equal:today',
                'end_date' => 'nullable|date|before_or_equal:today|after_or_equal:start_date',
            ]);

            $summary = $this->analyticsService->getSummaryStats(
                $validated['start_date'] ?? null,
                $validated['end_date'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $summary,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch summary', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch summary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get games with their event counts
     * GET /api/analytics/games
     */
    public function getGames(Request $request): JsonResponse
    {
        return $this->getEventsByDimension($request, 'games');
    }

    /**
     * Get tiers with their event counts
     * GET /api/analytics/tiers
     */
    public function getTiers(Request $request): JsonResponse
    {
        return $this->getEventsByDimension($request, 'tiers');
    }

    /**
     * Get esports titles with their event counts
     * GET /api/analytics/esports
     */
    public function getEsports(Request $request): JsonResponse
    {
        return $this->getEventsByDimension($request, 'esports');
    }

    /**
     * Get locations with their event counts
     * GET /api/analytics/locations
     */
    public function getLocations(Request $request): JsonResponse
    {
        return $this->getEventsByDimension($request, 'locations');
    }

    /**
     * Get event types with their counts
     * GET /api/analytics/types
     */
    public function getTypes(Request $request): JsonResponse
    {
        return $this->getEventsByDimension($request, 'types');
    }

    /**
     * Test Google Analytics connection
     * GET /api/analytics/test
     */
    public function testConnection(): JsonResponse
    {
        try {
            $result = $this->analyticsService->testConnection();

            return response()->json([
                'success' => $result['success'],
                'data' => $result,
            ], $result['success'] ? 200 : 500);

        } catch (\Exception $e) {
            Log::error('Connection test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection test failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllTiersHit(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'nullable|date|before_or_equal:today',
                'end_date' => 'nullable|date|before_or_equal:today|after_or_equal:start_date',
            ]);

            $tiersHit = $this->analyticsService->getAllTiersHit(
                $validated['start_date'] ?? null,
                $validated['end_date'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $tiersHit,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch tiers hit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tiers hit',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get detailed tier analytics
     * GET /api/analytics/tiers/analytics
     */
    public function getTierAnalytics(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'nullable|date|before_or_equal:today',
                'end_date' => 'nullable|date|before_or_equal:today|after_or_equal:start_date',
            ]);

            $tierAnalytics = $this->analyticsService->getTierAnalytics(
                $validated['start_date'] ?? null,
                $validated['end_date'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $tierAnalytics,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch tier analytics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tier analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDashboard(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'nullable|date|before_or_equal:today',
                'end_date' => 'nullable|date|before_or_equal:today|after_or_equal:start_date',
            ]);

            $startDate = $validated['start_date'] ?? null;
            $endDate = $validated['end_date'] ?? null;

            $dashboard = [
                'summary' => $this->analyticsService->getSummaryStats($startDate, $endDate),
                'top_games' => $this->analyticsService->getEventsByDimension('games', $startDate, $endDate),
                'top_tiers' => $this->analyticsService->getEventsByDimension('tiers', $startDate, $endDate),
                'top_esports' => $this->analyticsService->getEventsByDimension('esports', $startDate, $endDate),
                'top_locations' => $this->analyticsService->getEventsByDimension('locations', $startDate, $endDate),
            ];

            return response()->json([
                'success' => true,
                'data' => $dashboard,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch dashboard data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
