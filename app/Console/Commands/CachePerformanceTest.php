<?php

namespace App\Console\Commands;

use App\Models\EventDetail;
use App\Services\EventMatchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CachePerformanceTest extends Command
{
    protected $signature = 'cache:performance-test 
                            {--range=1-20 : Event ID range to test (e.g., 1-20)}
                            {--save : Save results to JSON file}';

    protected $description = 'Test produceBrackets cache performance across multiple events';

    public function handle()
    {
        $this->info('=== Cache Performance Test ===');
        $this->newLine();

        // Clear all cache at the beginning
        $this->info('🧹 Clearing all cache...');
        Cache::flush();
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        $this->info('✅ Cache cleared successfully');
        $this->newLine();

        // Parse range
        $range = $this->option('range');
        [$start, $end] = explode('-', $range);
        
        // Find events in range
        $events = EventDetail::with(['type', 'tier'])
            ->whereBetween('id', [(int)$start, (int)$end])
            ->get();

        if ($events->isEmpty()) {
            $this->error("No events found with IDs {$range} that have type and tier.");
            return 1;
        }

        $this->info("Found {$events->count()} events to test");
        $this->info("Event IDs: " . $events->pluck('id')->implode(', '));
        $this->newLine();

        $totalResults = [
            'without_cache_times' => [],
            'first_hit_times' => [],
            'second_hit_times' => [],
            'third_hit_times' => [],
            'memory_usage' => [],
            'events_tested' => []
        ];

        foreach ($events as $event) {
            $eventType = $event->type?->eventType ?? 'N/A';
            $teamSlots = $event->tier?->tierTeamSlot ?? 'N/A';
            $this->info("🧪 Testing Event {$event->id} ({$eventType}, {$teamSlots} teams)");
            $service = new EventMatchService();
            
            try {
                // Clear cache for this specific event to ensure clean test
                Cache::forget("brackets_event_{$event->id}_page_1");
                
                // Test 1: Without cache (clean slate)
                $this->line("  ⏱️  Without cache (clean)...");
                $startTime = microtime(true);
                $startMemory = memory_get_usage(true);
                
                $result1 = $service->generateBrackets($event, false, null, 1);
                
                $endTime = microtime(true);
                $endMemory = memory_get_usage(true);
                
                $timeWithoutCache = ($endTime - $startTime) * 1000;
                $memory1 = $endMemory - $startMemory;
                
                // Test 2: First hit (cache miss - initial load) 
                $this->line("  ⏱️  1st hit (cache miss)...");
                Cache::forget("brackets_event_{$event->id}_page_1");
                $startTime = microtime(true);
                
                $result2 = $service->generateBrackets($event, false, null, 1);
                
                $endTime = microtime(true);
                
                $time1 = ($endTime - $startTime) * 1000;
                
                // Test 3: Second hit (first cache hit)
                $this->line("  ⚡ 2nd hit (1st cache hit)...");
                $startTime = microtime(true);
                $result3 = $service->generateBrackets($event, false, null, 1);
                $endTime = microtime(true);
                
                $time2 = ($endTime - $startTime) * 1000;
                
                // Test 4: Third hit (second cache hit)
                $this->line("  🚀 3rd hit (2nd cache hit)...");
                $startTime = microtime(true);
                $result4 = $service->generateBrackets($event, false, null, 1);
                $endTime = microtime(true);
                
                $time3 = ($endTime - $startTime) * 1000;
                
                // Calculate improvements
                $improvementWithoutToFirst = (($timeWithoutCache - $time1) / $timeWithoutCache) * 100;
                $improvement1to2 = (($time1 - $time2) / $time1) * 100;
                $improvement2to3 = (($time2 - $time3) / $time2) * 100;
                $improvementOverall = (($timeWithoutCache - $time3) / $timeWithoutCache) * 100;
                
                // Calculate cumulative improvements from baseline
                $cumulativeToSecond = (($timeWithoutCache - $time2) / $timeWithoutCache) * 100;
                $cumulativeToThird = (($timeWithoutCache - $time3) / $timeWithoutCache) * 100;
                
                // Display results for this event
                $this->table(
                    ['Test', 'Time (ms)', 'vs Previous', 'vs Baseline'],
                    [
                        ['Without Cache', number_format($timeWithoutCache, 2), '-', '-'],
                        ['1st Hit (miss)', number_format($time1, 2), number_format($improvementWithoutToFirst, 1) . '%', number_format($improvementWithoutToFirst, 1) . '%'],
                        ['2nd Hit (cache)', number_format($time2, 2), number_format($improvement1to2, 1) . '%', number_format($cumulativeToSecond, 1) . '%'],
                        ['3rd Hit (cache)', number_format($time3, 2), number_format($improvement2to3, 1) . '%', number_format($cumulativeToThird, 1) . '%']
                    ]
                );
                
                if ($improvementOverall > 50) {
                    $this->info("  ✅ Event {$event->id}: Overall improvement " . number_format($improvementOverall, 1) . "% EXCELLENT");
                } elseif ($improvementOverall > 25) {
                    $this->line("  ✅ Event {$event->id}: Overall improvement " . number_format($improvementOverall, 1) . "% GOOD");
                } elseif ($improvementOverall > 0) {
                    $this->line("  ⚠️  Event {$event->id}: Overall improvement " . number_format($improvementOverall, 1) . "% MODERATE");
                } else {
                    $this->error("  ❌ Event {$event->id}: Overall improvement " . number_format($improvementOverall, 1) . "% SLOWER");
                }
                
                // Store results
                $totalResults['without_cache_times'][] = $timeWithoutCache;
                $totalResults['first_hit_times'][] = $time1;
                $totalResults['second_hit_times'][] = $time2;
                $totalResults['third_hit_times'][] = $time3;
                $totalResults['memory_usage'][] = $memory1;
                $totalResults['events_tested'][] = $event->id;
                
            } catch (\Exception $e) {
                // Skip failed events and log error
                $this->error("  ❌ Event {$event->id} failed: " . $e->getMessage());
                continue;
            }
            
            $this->newLine();
        }

        $this->newLine(2);

        // Overall Analysis
        $this->info('=== Performance Analysis ===');

        if (count($totalResults['first_hit_times']) > 0) {
            $avgWithoutCache = array_sum($totalResults['without_cache_times']) / count($totalResults['without_cache_times']);
            $avgFirstHit = array_sum($totalResults['first_hit_times']) / count($totalResults['first_hit_times']);
            $avgSecondHit = array_sum($totalResults['second_hit_times']) / count($totalResults['second_hit_times']);
            $avgThirdHit = array_sum($totalResults['third_hit_times']) / count($totalResults['third_hit_times']);
            $avgMemory = array_sum($totalResults['memory_usage']) / count($totalResults['memory_usage']);
            
            $improvementWithoutTo1st = (($avgWithoutCache - $avgFirstHit) / $avgWithoutCache) * 100;
            $improvement1to2 = (($avgFirstHit - $avgSecondHit) / $avgFirstHit) * 100;
            $improvement2to3 = (($avgSecondHit - $avgThirdHit) / $avgSecondHit) * 100;
            $overallImprovement = (($avgWithoutCache - $avgThirdHit) / $avgWithoutCache) * 100;
            
            // Cumulative improvements from baseline
            $cumulativeTo2nd = (($avgWithoutCache - $avgSecondHit) / $avgWithoutCache) * 100;
            $cumulativeTo3rd = (($avgWithoutCache - $avgThirdHit) / $avgWithoutCache) * 100;
            
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Events tested', count($totalResults['without_cache_times'])],
                    ['Avg time without cache', number_format($avgWithoutCache, 2) . ' ms'],
                    ['Avg time 1st hit (miss)', number_format($avgFirstHit, 2) . ' ms'],
                    ['Avg time 2nd hit (cache)', number_format($avgSecondHit, 2) . ' ms'],
                    ['Avg time 3rd hit (cache)', number_format($avgThirdHit, 2) . ' ms'],
                    ['Without → 1st improvement', number_format($improvementWithoutTo1st, 1) . '%'],
                    ['1st → 2nd improvement', number_format($improvement1to2, 1) . '%'],
                    ['2nd → 3rd improvement', number_format($improvement2to3, 1) . '%'],
                    ['Cumulative to 2nd hit', number_format($cumulativeTo2nd, 1) . '%'],
                    ['Cumulative to 3rd hit', number_format($cumulativeTo3rd, 1) . '%'],
                    ['Overall improvement', number_format($overallImprovement, 1) . '%'],
                    ['Avg memory usage', number_format($avgMemory / 1024, 2) . ' KB'],
                ]
            );

            // Performance breakdown
            $improvements = [];
            for ($i = 0; $i < count($totalResults['without_cache_times']); $i++) {
                $improvement = (($totalResults['without_cache_times'][$i] - $totalResults['third_hit_times'][$i]) / $totalResults['without_cache_times'][$i]) * 100;
                $improvements[] = $improvement;
            }
            
            $this->newLine();
            $this->info('=== Performance Distribution ===');
            $this->line("Best improvement: " . number_format(max($improvements), 1) . "%");
            $this->line("Worst improvement: " . number_format(min($improvements), 1) . "%");
            $this->line("Median improvement: " . number_format($improvements[floor(count($improvements)/2)], 1) . "%");
            
            // Cache effectiveness rating
            $this->newLine();
            if ($overallImprovement > 50) {
                $this->info("✅ EXCELLENT: Caching is highly effective across all events!");
            } elseif ($overallImprovement > 25) {
                $this->info("✅ GOOD: Caching provides solid performance improvement");
            } elseif ($overallImprovement > 10) {
                $this->warn("⚠️  MODERATE: Caching helps but gains are modest");
            } else {
                $this->error("❌ LIMITED: Caching may not be worthwhile");
            }
            
            // Event type breakdown
            $this->newLine();
            $this->info('=== Event Type Breakdown ===');
            
            $detailsTable = [];
            foreach ($events as $index => $event) {
                if (isset($totalResults['without_cache_times'][$index])) {
                    $improvement = $improvements[$index];
                    
                    // Calculate stage improvements for this event (all against baseline without cache)
                    $withoutTo1st = (($totalResults['without_cache_times'][$index] - $totalResults['first_hit_times'][$index]) / $totalResults['without_cache_times'][$index]) * 100;
                    $withoutTo2nd = (($totalResults['without_cache_times'][$index] - $totalResults['second_hit_times'][$index]) / $totalResults['without_cache_times'][$index]) * 100;
                    $withoutTo3rd = (($totalResults['without_cache_times'][$index] - $totalResults['third_hit_times'][$index]) / $totalResults['without_cache_times'][$index]) * 100;
                    
                    $detailsTable[] = [
                        $event->id,
                        $event->type?->eventType ?? 'N/A',
                        $event->tier?->tierTeamSlot ?? 'N/A',
                        number_format($totalResults['without_cache_times'][$index], 2) . ' ms',
                        number_format($totalResults['first_hit_times'][$index], 2) . ' ms (' . number_format($withoutTo1st, 1) . '%)',
                        number_format($totalResults['second_hit_times'][$index], 2) . ' ms (' . number_format($withoutTo2nd, 1) . '%)',
                        number_format($totalResults['third_hit_times'][$index], 2) . ' ms (' . number_format($withoutTo3rd, 1) . '%)',
                        number_format($improvement, 1) . '%'
                    ];
                }
            }
            
            $this->table(
                ['Event ID', 'Type', 'Teams', 'Without Cache', '1st Hit (Improv)', '2nd Hit (Improv)', '3rd Hit (Improv)', 'Overall'],
                $detailsTable
            );

            // Save results if requested
            if ($this->option('save')) {
                $results = [
                    'test_date' => date('Y-m-d H:i:s'),
                    'events_tested' => count($totalResults['without_cache_times']),
                    'event_range' => $range,
                    'average_without_cache_ms' => $avgWithoutCache,
                    'average_first_hit_ms' => $avgFirstHit,
                    'average_second_hit_ms' => $avgSecondHit,
                    'average_third_hit_ms' => $avgThirdHit,
                    'improvement_1st_to_2nd_percent' => $improvement1to2,
                    'improvement_2nd_to_3rd_percent' => $improvement2to3,
                    'overall_improvement_percent' => $overallImprovement,
                    'average_memory_kb' => $avgMemory / 1024,
                    'best_improvement_percent' => max($improvements),
                    'worst_improvement_percent' => min($improvements),
                    'median_improvement_percent' => $improvements[floor(count($improvements)/2)],
                    'detailed_results' => []
                ];
                
                // Add detailed per-event results
                foreach ($events as $index => $event) {
                    if (isset($totalResults['without_cache_times'][$index])) {
                        $results['detailed_results'][] = [
                            'event_id' => $event->id,
                            'event_type' => $event->type?->eventType ?? 'N/A',
                            'team_slots' => $event->tier?->tierTeamSlot ?? 'N/A',
                            'without_cache_ms' => $totalResults['without_cache_times'][$index],
                            'first_hit_ms' => $totalResults['first_hit_times'][$index],
                            'second_hit_ms' => $totalResults['second_hit_times'][$index],
                            'third_hit_ms' => $totalResults['third_hit_times'][$index],
                            'overall_improvement_percent' => $improvements[$index],
                            'memory_kb' => $totalResults['memory_usage'][$index] / 1024
                        ];
                    }
                }
                
                // Ensure tests directory exists
                $testsDir = base_path('tests/cache_performance');
                if (!is_dir($testsDir)) {
                    mkdir($testsDir, 0755, true);
                }
                
                $filename = 'cache_performance_results_' . str_replace('-', '_', $range) . '_' . date('Y-m-d_H-i-s') . '.json';
                $fullPath = $testsDir . '/' . $filename;
                file_put_contents($fullPath, json_encode($results, JSON_PRETTY_PRINT));
                $this->info("📊 Results saved to: tests/cache_performance/{$filename}");
            }

        } else {
            $this->error("❌ No events could be tested successfully");
            return 1;
        }

        $this->newLine();
        $this->info("Cache performance test complete!");
        return 0;
    }
}