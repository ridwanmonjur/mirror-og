<?php

namespace Tests\Unit\Event;

use Tests\TestCase;
use App\Models\{BracketDeadlineSetup, EventTier, EventType};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BracketDeadlineSetupModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_event_tier()
    {
        $tier = EventTier::factory()->create();
        $type = EventType::factory()->create();

        $setup = BracketDeadlineSetup::create([
            'tier_id' => $tier->id,
            'type_id' => $type->id,
            'deadline_config' => ['key' => 'value'],
        ]);

        $this->assertInstanceOf(EventTier::class, $setup->eventTier);
        $this->assertEquals($tier->id, $setup->eventTier->id);
    }

    /** @test */
    public function it_belongs_to_event_type()
    {
        $tier = EventTier::factory()->create();
        $type = EventType::factory()->create();

        $setup = BracketDeadlineSetup::create([
            'tier_id' => $tier->id,
            'type_id' => $type->id,
            'deadline_config' => ['key' => 'value'],
        ]);

        $this->assertInstanceOf(EventType::class, $setup->eventType);
        $this->assertEquals($type->id, $setup->eventType->id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $tier = EventTier::factory()->create();
        $type = EventType::factory()->create();

        $config = [
            'stage_1' => ['duration' => 60],
            'stage_2' => ['duration' => 120],
        ];

        $setup = BracketDeadlineSetup::create([
            'tier_id' => $tier->id,
            'type_id' => $type->id,
            'deadline_config' => $config,
        ]);

        $this->assertEquals($tier->id, $setup->tier_id);
        $this->assertEquals($type->id, $setup->type_id);
        $this->assertEquals($config, $setup->deadline_config);
    }

    /** @test */
    public function it_does_not_use_timestamps()
    {
        $setup = new BracketDeadlineSetup();

        $this->assertFalse($setup->timestamps);
    }

    /** @test */
    public function it_casts_deadline_config_to_array()
    {
        $tier = EventTier::factory()->create();
        $type = EventType::factory()->create();

        $config = ['stage_1' => ['duration' => 60]];

        $setup = BracketDeadlineSetup::create([
            'tier_id' => $tier->id,
            'type_id' => $type->id,
            'deadline_config' => $config,
        ]);

        $this->assertIsArray($setup->deadline_config);
        $this->assertEquals($config, $setup->deadline_config);
    }

    /** @test */
    public function it_stores_complex_deadline_configuration()
    {
        $tier = EventTier::factory()->create();
        $type = EventType::factory()->create();

        $config = [
            'round_1' => [
                'duration' => 60,
                'auto_advance' => true,
                'notification_before' => 10,
            ],
            'round_2' => [
                'duration' => 120,
                'auto_advance' => false,
            ],
        ];

        $setup = BracketDeadlineSetup::create([
            'tier_id' => $tier->id,
            'type_id' => $type->id,
            'deadline_config' => $config,
        ]);

        $fresh = $setup->fresh();

        $this->assertEquals($config, $fresh->deadline_config);
        $this->assertTrue($fresh->deadline_config['round_1']['auto_advance']);
        $this->assertFalse($fresh->deadline_config['round_2']['auto_advance']);
    }
}
