<?php
namespace Database\Factories;

use App\Models\EventDetail;
use App\Models\Brackets;
use App\Models\Team;
use App\Services\EventMatchService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\App;

class BracketsFactory extends Factory
{
    protected $model = Brackets::class;

     /**
     * The event match service instance.
     *
     * @var EventMatchService
     */
    protected $eventMatchService;

    public function __construct()
    {
        parent::__construct();
        
        // Resolve the service from the container
        $this->eventMatchService = App::make(EventMatchService::class);
    }


    public function definition(): array
    {
        return [
            'order' => $this->faker->numberBetween(0, 5),
            'team1_id' => Team::factory(),
            'team2_id' => Team::factory(),
            'event_details_id' => EventDetail::factory(),
            'team1_position' => $this->faker->regexify('[A-Z][0-9]?'),
            'team2_position' => $this->faker->regexify('[A-Z][0-9]?'),
            'stage_name' => $this->faker->randomElement(['F', 'W', 'U', 'L']),
            'inner_stage_name' => $this->faker->randomElement(['e1', 'e2', 'p1', 'p2']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function seed() {
        $eventDetailFactory = new EventDetailFactory();
        $eventDetailFactory->deleteRelatedTables();

        $joinEventFactory = new JoinEventFactory();
        $result = $joinEventFactory->seed();
        $events = collect($result['events']);
        $teams = $result['teams'];

        foreach ($events as $detail) {
            $detail->createStructuredDeadlines();
            $this->eventMatchService->createBrackets($detail);
            $this->updateBracketTeams(
                $detail->id, 
                'U', 
                ['e1', 'e3', 'e5', 'p1'],
                $teams
            );
            
            // 2. Update L, e2, e4 brackets
            $this->updateBracketTeams(
                $detail->id, 
                'L', 
                ['e2', 'e4', 'p1'],
                $teams
            );
        }
        
        $eventIds = $events->pluck('id')->toArray();

        return [
            'eventIds' => $eventIds,
            'result' => $result
        ];
    }

     /**
     * Update bracket team IDs for specific criteria
     *
     * @param int $eventId Event ID
     * @param string $stageName Stage name (U, L, F, W)
     * @param array $innerStageNames Array of inner stage names to update
     * @param array $teams Array of team models to use for assignment
     */
    private function updateBracketTeams(
        int $eventId, 
        string $stageName, 
        array $innerStageNames,
        array $teams
    ): void {
        $brackets = Brackets::where('event_details_id', $eventId)
            ->where('stage_name', $stageName)
            ->whereIn('inner_stage_name', $innerStageNames)
            ->get();
        
        $teamCount = count($teams);
        $teamIndex = 0;
        
        foreach ($brackets as $bracket) {
            $bracket->team1_id = $teams[$teamIndex % $teamCount]->id;
            $teamIndex++;
            
            $bracket->team2_id = $teams[$teamIndex % $teamCount]->id;
            $teamIndex++;
            
            $bracket->save();
        }
    }
}
