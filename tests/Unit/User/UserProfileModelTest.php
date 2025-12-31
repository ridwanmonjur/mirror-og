<?php

namespace Tests\Unit\User;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{UserProfile, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserProfileModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $profile->user);
        $this->assertEquals($user->id, $profile->user->id);
    }

    /** @test */
    public function it_has_no_timestamps()
    {
        $profile = new UserProfile();
        $this->assertFalse($profile->timestamps);
    }

    /** @test */
    public function it_stores_style_attributes()
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'frameColor' => '#FF0000',
            'backgroundColor' => '#00FF00',
            'backgroundGradient' => 'linear-gradient(to right, #FF0000, #00FF00)',
            'fontColor' => '#0000FF',
        ]);

        $this->assertEquals('#FF0000', $profile->frameColor);
        $this->assertEquals('#00FF00', $profile->backgroundColor);
        $this->assertEquals('linear-gradient(to right, #FF0000, #00FF00)', $profile->backgroundGradient);
        $this->assertEquals('#0000FF', $profile->fontColor);
    }

    /** @test */
    public function it_generates_background_styles()
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'backgroundColor' => '#FF0000',
        ]);

        $styles = $profile->generateStyles();

        $this->assertArrayHasKey('backgroundStyles', $styles);
        $this->assertStringContainsString('#FF0000', $styles['backgroundStyles']);
    }

    /** @test */
    public function it_generates_font_styles()
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'fontColor' => '#0000FF',
        ]);

        $styles = $profile->generateStyles();

        $this->assertArrayHasKey('fontStyles', $styles);
        $this->assertStringContainsString('#0000FF', $styles['fontStyles']);
    }

    /** @test */
    public function it_generates_frame_styles()
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'frameColor' => '#00FF00',
        ]);

        $styles = $profile->generateStyles();

        $this->assertArrayHasKey('frameStyles', $styles);
        $this->assertStringContainsString('#00FF00', $styles['frameStyles']);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->create([
            'user_id' => $user->id,
            'frameColor' => '#FFFFFF',
            'backgroundColor' => '#000000',
            'fontColor' => '#FF00FF',
        ]);

        $this->assertEquals($user->id, $profile->user_id);
        $this->assertEquals('#FFFFFF', $profile->frameColor);
        $this->assertEquals('#000000', $profile->backgroundColor);
        $this->assertEquals('#FF00FF', $profile->fontColor);
    }
}
