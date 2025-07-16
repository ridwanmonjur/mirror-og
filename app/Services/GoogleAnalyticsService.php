<?php

namespace App\Services;

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\RunRealtimeReportRequest;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\FilterExpressionList;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;

class GoogleAnalyticsService
{
    private $client;
    private $propertyId;

    public function __construct()
    {
        $this->client = new BetaAnalyticsDataClient([
            'credentials' => base_path(config('analytics.ga4.credentials_path'))
        ]);
        $this->propertyId = config('analytics.ga4.property_id');
    }

    /**
     * Get all event views with counts
     */
    public function getAllEventViews($startDate = null, $endDate = null)
    {
        try {
            $startDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = $endDate ?? Carbon::now()->format('Y-m-d');

            $request = new RunReportRequest();
            $request->setProperty('properties/' . $this->propertyId);
            
            // Set date ranges
            $dateRange = new DateRange();
            $dateRange->setStartDate($startDate);
            $dateRange->setEndDate($endDate);
            $request->setDateRanges([$dateRange]);
            
            // Set dimensions
            $dimensions = [
                // (new Dimension())->setName('customEvent:event_id'),
                (new Dimension())->setName('customEvent:event_name'),
                (new Dimension())->setName('customEvent:event_tier'),
                (new Dimension())->setName('customEvent:event_type'),
                (new Dimension())->setName('customEvent:esport_title'),
                (new Dimension())->setName('customEvent:location'),
                (new Dimension())->setName('customEvent:game_id'),
            ];
            $request->setDimensions($dimensions);
            
            // Set metrics
            $metrics = [
                (new Metric())->setName('eventCount'),
            ];
            $request->setMetrics($metrics);
            
            // // Set dimension filter
            // $filter = new Filter();
            // $filter->setFieldName('eventName');
            // $stringFilter = new Filter\StringFilter();
            // $stringFilter->setValue('event_view');
            // $stringFilter->setMatchType(Filter\StringFilter\MatchType::EXACT);
            // $filter->setStringFilter($stringFilter);
            
            // $filterExpression = new FilterExpression();
            // $filterExpression->setFilter($filter);
            // $request->setDimensionFilter($filterExpression);

            $response = $this->client->runReport($request);
            return $this->formatEventResponse($response);
            
        } catch (Exception $e) {
            throw new Exception('Failed to fetch event views: ' . $e->getMessage());
        }
    }

    /**
     * Get events grouped by specific dimension
     */
    public function getEventsByDimension($dimension, $startDate = null, $endDate = null)
    {
        try {
            $startDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
            $endDate = $endDate ?? Carbon::now()->format('Y-m-d');

            $dimensionMap = [
                'events' => 'customEvent:event_name',
                'games' => 'customEvent:game_id',
                'tiers' => 'customEvent:event_tier',
                'esports' => 'customEvent:esport_title',
                'locations' => 'customEvent:location',
                'types' => 'customEvent:event_type'
            ];

            if (!isset($dimensionMap[$dimension])) {
                throw new \InvalidArgumentException("Invalid dimension: {$dimension}");
            }

            $request = new RunReportRequest();
            $request->setProperty('properties/' . $this->propertyId);
            
            // Set date ranges
            $dateRange = new DateRange();
            $dateRange->setStartDate($startDate);
            $dateRange->setEndDate($endDate);
            $request->setDateRanges([$dateRange]);
            
            // Set dimensions
            $dimensions = [
                (new Dimension())->setName($dimensionMap[$dimension]),
            ];
            $request->setDimensions($dimensions);
            
            // Set metrics
            $metrics = [
                (new Metric())->setName('eventCount'),
            ];
            $request->setMetrics($metrics);
            
            // Set basic event filter
            $eventFilter = new Filter();
            $eventFilter->setFieldName('eventName');
            $stringFilter = new Filter\StringFilter();
            $stringFilter->setValue('event_view');
            $stringFilter->setMatchType(Filter\StringFilter\MatchType::EXACT);
            $eventFilter->setStringFilter($stringFilter);
            
            $eventFilterExpression = new FilterExpression();
            $eventFilterExpression->setFilter($eventFilter);
            
            $request->setDimensionFilter($eventFilterExpression);
            
            // Set ordering
            $orderBy = new OrderBy();
            $metricOrderBy = new MetricOrderBy();
            $metricOrderBy->setMetricName('eventCount');
            $orderBy->setMetric($metricOrderBy);
            $orderBy->setDesc(true);
            $request->setOrderBys([$orderBy]);

            $response = $this->client->runReport($request);
            
            return $this->formatDimensionResponse($response, $dimension);
            
        } catch (Exception $e) {
            Log::error('Failed to fetch events by dimension in GoogleAnalyticsService', [
                'dimension' => $dimension,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw new Exception('Failed to fetch events by dimension: ' . $e->getMessage());
        }
    }

    /**
     * Get specific event details
     */
    public function getEventDetails($eventId, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $endDate = $endDate ?? Carbon::now()->format('Y-m-d');

        $request = new RunReportRequest([
            'property' => 'properties/' . $this->propertyId,
            // 'dateRanges' => [
            //     new DateRange([
            //         'start_date' => $startDate,
            //         'end_date' => $endDate,
            //     ]),
            // ],
            'dimensions' => [
                new Dimension(['name' => 'customEvent:event_id']),
                new Dimension(['name' => 'customEvent:event_name']),
                new Dimension(['name' => 'customEvent:event_tier']),
                new Dimension(['name' => 'customEvent:event_type']),
                new Dimension(['name' => 'customEvent:esport_title']),
                new Dimension(['name' => 'customEvent:location']),
                new Dimension(['name' => 'date']),
            ],
            'metrics' => [
                new Metric(['name' => 'eventCount']),
            ],
            'dimension_filter' => new FilterExpression([
                'and_group' => new FilterExpressionList([
                    'expressions' => [
                        new FilterExpression([
                            'filter' => new Filter([
                                'field_name' => 'eventName',
                                'string_filter' => new Filter\StringFilter([
                                    'value' => 'event_view',
                                    'match_type' => Filter\StringFilter\MatchType::EXACT,
                                ]),
                            ]),
                        ]),
                        new FilterExpression([
                            'filter' => new Filter([
                                'field_name' => 'customEvent:event_id',
                                'string_filter' => new Filter\StringFilter([
                                    'value' => $eventId,
                                    'match_type' => Filter\StringFilter\MatchType::EXACT,
                                ]),
                            ]),
                        ]),
                    ],
                ]),
            ]),
        ]);

        $dateRange = new DateRange();
        $dateRange->setStartDate($startDate);
        $dateRange->setEndDate($endDate);
        $request->setDateRanges([$dateRange]);

        $response = $this->client->runReport($request);
        return $this->formatEventDetailsResponse($response);
    }

    /**
     * Test connection to Google Analytics
     */
    public function testConnection()
    {
        try {
            $request = new RunReportRequest();
            $request->setProperty('properties/' . $this->propertyId);
            
            // Set date ranges - last 7 days
            $dateRange = new DateRange();
            $dateRange->setStartDate(Carbon::now()->subDays(7)->format('Y-m-d'));
            $dateRange->setEndDate(Carbon::now()->format('Y-m-d'));
            $request->setDateRanges([$dateRange]);
            
            // Set metrics
            $metrics = [
                (new Metric())->setName('activeUsers'),
            ];
            $request->setMetrics($metrics);
            
            // Set row limit
            $request->setLimit(1);

            $response = $this->client->runReport($request);
            
            return [
                'success' => true,
                'property_id' => $this->propertyId,
                // 'rows_returned' => count($response->getRows()),
                'connection_status' => 'Connected successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'connection_status' => 'Connection failed'
            ];
        }
    }
    public function getAllTiersHit($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $endDate = $endDate ?? Carbon::now()->format('Y-m-d');

        $request = new RunReportRequest([
            'property' => 'properties/' . $this->propertyId,
            // 'dateRanges' => [
            //     new DateRange([
            //         'start_date' => $startDate,
            //         'end_date' => $endDate,
            //     ]),
            // ],
            'dimensions' => [
                new Dimension(['name' => 'customEvent:event_tier']),
            ],
            'metrics' => [
                new Metric(['name' => 'eventCount']),
                new Metric(['name' => 'activeUsers']),
            ],
            'dimension_filter' => new FilterExpression([
                'and_group' => new FilterExpressionList([
                    'expressions' => [
                        new FilterExpression([
                            'filter' => new Filter([
                                'field_name' => 'eventName',
                                'string_filter' => new Filter\StringFilter([
                                    'value' => 'tier_view',
                                    'match_type' => Filter\StringFilter\MatchType::EXACT,
                                ]),
                            ]),
                        ]),
                        // Filter out empty/null tier values
                        new FilterExpression([
                            'filter' => new Filter([
                                'field_name' => 'customEvent:event_tier',
                                'string_filter' => new Filter\StringFilter([
                                    'value' => '',
                                    'match_type' => Filter\StringFilter\MatchType::EXACT,
                                ]),
                            ]),
                            'not_expression' => true,
                        ]),
                    ],
                ]),
            ]),
            'order_bys' => [
                new \Google\Analytics\Data\V1beta\OrderBy([
                    'metric' => new \Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy([
                        'metric_name' => 'eventCount'
                    ]),
                    'desc' => true,
                ]),
            ],
        ]);

        $dateRange = new DateRange();
        $dateRange->setStartDate($startDate);
        $dateRange->setEndDate($endDate);
        $request->setDateRanges([$dateRange]);

        $response = $this->client->runReport($request);
        
        return $this->formatTiersHitResponse($response);
    }

    /**
     * Get detailed tier analytics with additional metrics
     */
    public function getTierAnalytics($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $endDate = $endDate ?? Carbon::now()->format('Y-m-d');

        $request = new RunReportRequest([
            'property' => 'properties/' . $this->propertyId,
            // 'dateRanges' => [
            //     new DateRange([
            //         'start_date' => $startDate,
            //         'end_date' => $endDate,
            //     ]),
            // ],
            'dimensions' => [
                new Dimension(['name' => 'customEvent:event_tier']),
                new Dimension(['name' => 'customEvent:esport_title']),
                new Dimension(['name' => 'customEvent:location']),
            ],
            'metrics' => [
                new Metric(['name' => 'eventCount']),
                new Metric(['name' => 'activeUsers']),
            ],
            'dimension_filter' => new FilterExpression([
                'and_group' => new FilterExpressionList([
                    'expressions' => [
                        new FilterExpression([
                            'filter' => new Filter([
                                'field_name' => 'eventName',
                                'string_filter' => new Filter\StringFilter([
                                    'value' => 'tier_view',
                                    'match_type' => Filter\StringFilter\MatchType::EXACT,
                                ]),
                            ]),
                        ]),
                        new FilterExpression([
                            'filter' => new Filter([
                                'field_name' => 'customEvent:event_tier',
                                'string_filter' => new Filter\StringFilter([
                                    'value' => '',
                                    'match_type' => Filter\StringFilter\MatchType::EXACT,
                                ]),
                            ]),
                            'not_expression' => true,
                        ]),
                    ],
                ]),
            ]),
            'order_bys' => [
                new \Google\Analytics\Data\V1beta\OrderBy([
                    'metric' => new \Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy([
                        'metric_name' => 'eventCount'
                    ]),
                    'desc' => true,
                ]),
            ],
        ]);

        $dateRange = new DateRange();
        $dateRange->setStartDate($startDate);
        $dateRange->setEndDate($endDate);
        $request->setDateRanges([$dateRange]);
        $response = $this->client->runReport($request);
        
        return $this->formatTierAnalyticsResponse($response);
    }
    public function getSummaryStats($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $endDate = $endDate ?? Carbon::now()->format('Y-m-d');

        $request = new RunReportRequest([
            'property' => 'properties/' . $this->propertyId,
            // 'dateRanges' => [
            //     new DateRange([
            //         'start_date' => $startDate,
            //         'end_date' => $endDate,
            //     ]),
            // ],
            'metrics' => [
                new Metric(['name' => 'eventCount']),
            ],
            'dimension_filter' => new FilterExpression([
                'filter' => new Filter([
                    'field_name' => 'eventName',
                    'string_filter' => new Filter\StringFilter([
                        'value' => 'event_view',
                        'match_type' => Filter\StringFilter\MatchType::EXACT,
                    ]),
                ]),
            ]),
        ]);

        $dateRange = new DateRange();
        $dateRange->setStartDate($startDate);
        $dateRange->setEndDate($endDate);
        $request->setDateRanges([$dateRange]);
        $response = $this->client->runReport($request);
        
        return [
            'total_events' => $response->getRows(),
            'date_range' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];
    }

    private function formatEventResponse($response)
    {
        $dimensionHeaders = $response->getDimensionHeaders();

        $metricHeaders = $response->getMetricHeaders();

        $events = [];
    
        foreach ($response->getRows() as $row) {
            $event = [];
    
            // Map dimension values to header names
            foreach ($dimensionHeaders as $index => $header) {
                $name = $header->getName(); // e.g., 'eventName', 'country'
                $event[$name] = $row->getDimensionValues()[$index]?->getValue() ?? null;
            }
    
            // Map metric values to header names
            foreach ($metricHeaders as $index => $header) {
                $name = $header->getName(); // e.g., 'eventCount', 'activeUsers'
                $event[$name] = (float) ($row->getMetricValues()[$index]?->getValue() ?? 0);
            }
    
            $events[] = $event;
        }

        return $events;
    }

    private function formatDimensionResponse($response, $dimension)
    {
        $results = [];
        
        foreach ($response->getRows() as $row) {
            $dimensions = $row->getDimensionValues();
            $metrics = $row->getMetricValues();
            
            $results[] = [
                'name' => $dimensions[0]->getValue(),
                'count' => (int) $metrics[0]->getValue(),
            ];
        }
        
        return [
            'dimension' => $dimension,
            'results' => $results,
            'total' => array_sum(array_column($results, 'count')),
        ];
    }

    private function formatEventDetailsResponse($response)
    {
        $details = [];
        
        foreach ($response->getRows() as $row) {
            $dimensions = $row->getDimensionValues();
            $metrics = $row->getMetricValues();
            
            $details[] = [
                'event_id' => $dimensions[0]->getValue(),
                'event_name' => $dimensions[1]->getValue(),
                'event_tier' => $dimensions[2]->getValue(),
                'event_type' => $dimensions[3]->getValue(),
                'esport_title' => $dimensions[4]->getValue(),
                'location' => $dimensions[5]->getValue(),
                'date' => $dimensions[6]->getValue(),
                'count' => (int) $metrics[0]->getValue(),
            ];
        }
        
        return $details;
    }

    private function formatTiersHitResponse($response)
    {
        $tiers = [];
        
        foreach ($response->getRows() as $row) {
            $dimensions = $row->getDimensionValues();
            $metrics = $row->getMetricValues();
            
            $tiers[] = [
                'tier_name' => $dimensions[0]->getValue(),
                'view_count' => (int) $metrics[0]->getValue(),
                'unique_users' => (int) $metrics[1]->getValue(),
            ];
        }
        
        return [
            'tiers' => $tiers,
            'total_tiers' => count($tiers),
            'total_views' => array_sum(array_column($tiers, 'view_count')),
            'total_unique_users' => array_sum(array_column($tiers, 'unique_users')),
        ];
    }

    private function formatTierAnalyticsResponse($response)
    {
        $analytics = [];
        $tierSummary = [];
        
        foreach ($response->getRows() as $row) {
            $dimensions = $row->getDimensionValues();
            $metrics = $row->getMetricValues();
            
            $tierName = $dimensions[0]->getValue();
            $esportTitle = $dimensions[1]->getValue();
            $location = $dimensions[2]->getValue();
            $viewCount = (int) $metrics[0]->getValue();
            $uniqueUsers = (int) $metrics[1]->getValue();
            
            $analytics[] = [
                'tier_name' => $tierName,
                'esport_title' => $esportTitle,
                'location' => $location,
                'view_count' => $viewCount,
                'unique_users' => $uniqueUsers,
            ];
            
            // Build tier summary
            if (!isset($tierSummary[$tierName])) {
                $tierSummary[$tierName] = [
                    'tier_name' => $tierName,
                    'total_views' => 0,
                    'total_unique_users' => 0,
                    'esports' => [],
                    'locations' => [],
                ];
            }
            
            $tierSummary[$tierName]['total_views'] += $viewCount;
            $tierSummary[$tierName]['total_unique_users'] += $uniqueUsers;
            
            if ($esportTitle && !in_array($esportTitle, $tierSummary[$tierName]['esports'])) {
                $tierSummary[$tierName]['esports'][] = $esportTitle;
            }
            
            if ($location && !in_array($location, $tierSummary[$tierName]['locations'])) {
                $tierSummary[$tierName]['locations'][] = $location;
            }
        }
        
        return [
            'detailed_analytics' => $analytics,
            'tier_summary' => array_values($tierSummary),
            'total_tiers' => count($tierSummary),
        ];
    }
}