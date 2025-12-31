<?php

namespace Tests\Unit\Event;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{EventCategory, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;

class EventCategoryModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $organizer = $this->createOrganizer();

        $category = EventCategory::factory()->create([
            'user_id' => $organizer->id,
        ]);

        $this->assertInstanceOf(User::class, $category->user);
        $this->assertEquals($organizer->id, $category->user->id);
    }

    /** @test */
    public function it_stores_game_title()
    {
        $category = EventCategory::factory()->create([
            'gameTitle' => 'Counter-Strike 2',
        ]);

        $this->assertEquals('Counter-Strike 2', $category->gameTitle);
    }

    /** @test */
    public function it_stores_game_icon()
    {
        $category = EventCategory::factory()->create([
            'gameIcon' => 'cs2-icon.png',
        ]);

        $this->assertEquals('cs2-icon.png', $category->gameIcon);
    }

    /** @test */
    public function it_stores_game_url()
    {
        $category = EventCategory::factory()->create([
            'gameUrl' => 'counter-strike-2',
        ]);

        $this->assertEquals('counter-strike-2', $category->gameUrl);
    }

    /** @test */
    public function it_stores_game_description()
    {
        $category = EventCategory::factory()->create([
            'gameDescription' => 'Popular FPS game',
        ]);

        $this->assertEquals('Popular FPS game', $category->gameDescription);
    }

    /** @test */
    public function it_casts_event_tags_to_array()
    {
        $tags = ['fps', 'competitive', 'team-based'];

        $category = EventCategory::factory()->create([
            'eventTags' => $tags,
        ]);

        $this->assertIsArray($category->eventTags);
        $this->assertEquals($tags, $category->eventTags);
    }

    /** @test */
    public function it_stores_player_per_team()
    {
        $category = EventCategory::factory()->create([
            'player_per_team' => 5,
        ]);

        $this->assertEquals(5, $category->player_per_team);
    }

    /** @test */
    public function it_stores_games_per_match()
    {
        $category = EventCategory::factory()->create([
            'games_per_match' => 3,
        ]);

        $this->assertEquals(3, $category->games_per_match);
    }

    /** @test */
    public function it_caches_all_categories()
    {
        EventCategory::factory()->count(3)->create();

        Cache::shouldReceive('remember')
            ->once()
            ->with(EventCategory::CACHE_KEY, EventCategory::CACHE_DURATION, \Mockery::type('Closure'))
            ->andReturn(EventCategory::all());

        $categories = EventCategory::getAllCached();

        $this->assertCount(3, EventCategory::all());
    }

    /** @test */
    public function it_clears_cache()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with(EventCategory::CACHE_KEY);

        EventCategory::clearCache();
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $organizer = $this->createOrganizer();

        $category = EventCategory::factory()->create([
            'gameTitle' => 'Valorant',
            'gameIcon' => 'valorant.png',
            'gameUrl' => 'valorant',
            'gameDescription' => 'Tactical shooter',
            'eventTags' => ['fps', 'tactical'],
            'user_id' => $organizer->id,
            'player_per_team' => 5,
            'games_per_match' => 2,
            'url' => 'https://playvalorant.com',
        ]);

        $this->assertEquals('Valorant', $category->gameTitle);
        $this->assertEquals('valorant.png', $category->gameIcon);
        $this->assertEquals('valorant', $category->gameUrl);
        $this->assertEquals('Tactical shooter', $category->gameDescription);
        $this->assertEquals(['fps', 'tactical'], $category->eventTags);
        $this->assertEquals($organizer->id, $category->user_id);
        $this->assertEquals(5, $category->player_per_team);
        $this->assertEquals(2, $category->games_per_match);
        $this->assertEquals('https://playvalorant.com', $category->url);
    }
}
