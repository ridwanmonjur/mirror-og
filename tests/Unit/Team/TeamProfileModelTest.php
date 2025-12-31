<?php

namespace Tests\Unit\Team;

use Tests\TestCase;
use Tests\Traits\CreatesTestTeams;
use App\Models\{TeamProfile, Team, EventCategory};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TeamProfileModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestTeams;

    /** @test */
    public function it_belongs_to_team()
    {
        $team = $this->createTeam();

        $this->assertInstanceOf(Team::class, $team->profile->team);
        $this->assertEquals($team->id, $team->profile->team->id);
    }

    /** @test */
    public function it_belongs_to_default_category()
    {
        $team = $this->createTeam();
        $category = EventCategory::factory()->create();

        $team->profile->update(['default_category_id' => $category->id]);

        $this->assertInstanceOf(EventCategory::class, $team->profile->fresh()->defaultCategory);
        $this->assertEquals($category->id, $team->profile->fresh()->defaultCategory->id);
    }

    /** @test */
    public function it_has_no_timestamps()
    {
        $profile = new TeamProfile();
        $this->assertFalse($profile->timestamps);
    }

    /** @test */
    public function it_stores_style_attributes()
    {
        $team = $this->createTeam();
        $profile = $team->profile;

        $profile->update([
            'frameColor' => '#FF0000',
            'backgroundColor' => '#00FF00',
            'backgroundGradient' => 'linear-gradient(to right, #FF0000, #00FF00)',
            'fontColor' => '#0000FF',
        ]);

        $fresh = $profile->fresh();
        $this->assertEquals('#FF0000', $fresh->frameColor);
        $this->assertEquals('#00FF00', $fresh->backgroundColor);
        $this->assertEquals('linear-gradient(to right, #FF0000, #00FF00)', $fresh->backgroundGradient);
        $this->assertEquals('#0000FF', $fresh->fontColor);
    }

    /** @test */
    public function it_stores_follower_count()
    {
        $team = $this->createTeam();
        $profile = $team->profile;

        $profile->update(['follower_count' => 150]);

        $this->assertEquals(150, $profile->fresh()->follower_count);
    }

    /** @test */
    public function it_sets_all_categories()
    {
        $team = $this->createTeam();
        $profile = $team->profile;

        $categoryIds = [1, 2, 3];
        $profile->setAllCategories($categoryIds);

        $this->assertEquals($categoryIds, $profile->getAllCategories());
    }

    /** @test */
    public function it_gets_empty_categories_array()
    {
        $team = $this->createTeam();
        $profile = $team->profile;

        $profile->all_categories = null;

        $this->assertEmpty($profile->getAllCategories());
    }

    /** @test */
    public function it_adds_category()
    {
        $team = $this->createTeam();
        $profile = $team->profile;

        $profile->setAllCategories([1, 2]);
        $profile->addOtherCategory(3);

        $categories = $profile->getAllCategories();
        $this->assertCount(3, $categories);
        $this->assertContains('3', $categories);
    }

    /** @test */
    public function it_does_not_add_duplicate_category()
    {
        $team = $this->createTeam();
        $profile = $team->profile;

        $profile->setAllCategories([1, 2, 3]);
        $profile->addOtherCategory(2);

        $categories = $profile->getAllCategories();
        $this->assertCount(3, $categories);
    }

    /** @test */
    public function it_removes_category()
    {
        $team = $this->createTeam();
        $profile = $team->profile;

        $profile->setAllCategories([1, 2, 3]);
        $profile->removeOtherCategory(2);

        $categories = $profile->getAllCategories();
        $this->assertCount(2, $categories);
        $this->assertNotContains('2', $categories);
    }

    /** @test */
    public function it_checks_if_has_category()
    {
        $team = $this->createTeam();
        $profile = $team->profile;

        $profile->setAllCategories([1, 2, 3]);

        $this->assertTrue($profile->hasOtherCategory(2));
        $this->assertFalse($profile->hasOtherCategory(5));
    }

    /** @test */
    public function it_generates_background_styles()
    {
        $team = $this->createTeam();
        $profile = $team->profile;

        $profile->backgroundColor = '#FF0000';
        $styles = $profile->generateStyles();

        $this->assertArrayHasKey('backgroundStyles', $styles);
        $this->assertStringContainsString('#FF0000', $styles['backgroundStyles']);
    }

    /** @test */
    public function it_generates_font_styles()
    {
        $team = $this->createTeam();
        $profile = $team->profile;

        $profile->fontColor = '#0000FF';
        $styles = $profile->generateStyles();

        $this->assertArrayHasKey('fontStyles', $styles);
        $this->assertStringContainsString('#0000FF', $styles['fontStyles']);
    }

    /** @test */
    public function it_generates_frame_styles()
    {
        $team = $this->createTeam();
        $profile = $team->profile;

        $profile->frameColor = '#00FF00';
        $styles = $profile->generateStyles();

        $this->assertArrayHasKey('frameStyles', $styles);
        $this->assertStringContainsString('#00FF00', $styles['frameStyles']);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $team = $this->createTeam();
        $profile = $team->profile;

        $profile->update([
            'frameColor' => '#FFFFFF',
            'backgroundColor' => '#000000',
            'fontColor' => '#FF00FF',
            'follower_count' => 250,
        ]);

        $fresh = $profile->fresh();
        $this->assertEquals('#FFFFFF', $fresh->frameColor);
        $this->assertEquals('#000000', $fresh->backgroundColor);
        $this->assertEquals('#FF00FF', $fresh->fontColor);
        $this->assertEquals(250, $fresh->follower_count);
    }
}
