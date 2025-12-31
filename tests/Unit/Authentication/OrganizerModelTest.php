<?php

namespace Tests\Unit\Authentication;

use Tests\TestCase;
use Tests\Traits\CreatesTestUsers;
use App\Models\{Organizer, User};
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrganizerModelTest extends TestCase
{
    use DatabaseTransactions, CreatesTestUsers;

    /** @test */
    public function it_belongs_to_user()
    {
        $user = $this->createOrganizer();

        $this->assertInstanceOf(User::class, $user->organizer->user);
        $this->assertEquals($user->id, $user->organizer->user->id);
    }

    /** @test */
    public function it_stores_company_name()
    {
        $user = $this->createOrganizer();
        $organizer = $user->organizer;
        $organizer->update(['companyName' => 'Esports Inc']);

        $this->assertEquals('Esports Inc', $organizer->fresh()->companyName);
    }

    /** @test */
    public function it_stores_company_description()
    {
        $user = $this->createOrganizer();
        $organizer = $user->organizer;
        $organizer->update(['companyDescription' => 'Leading esports company']);

        $this->assertEquals('Leading esports company', $organizer->fresh()->companyDescription);
    }

    /** @test */
    public function it_stores_industry()
    {
        $user = $this->createOrganizer();
        $organizer = $user->organizer;
        $organizer->update(['industry' => 'Gaming']);

        $this->assertEquals('Gaming', $organizer->fresh()->industry);
    }

    /** @test */
    public function it_stores_type()
    {
        $user = $this->createOrganizer();
        $organizer = $user->organizer;
        $organizer->update(['type' => 'Corporation']);

        $this->assertEquals('Corporation', $organizer->fresh()->type);
    }

    /** @test */
    public function it_stores_social_media_links()
    {
        $user = $this->createOrganizer();
        $organizer = $user->organizer;

        $organizer->update([
            'website_link' => 'https://esports.com',
            'instagram_link' => 'https://instagram.com/esports',
            'facebook_link' => 'https://facebook.com/esports',
            'twitter_link' => 'https://twitter.com/esports',
        ]);

        $fresh = $organizer->fresh();
        $this->assertEquals('https://esports.com', $fresh->website_link);
        $this->assertEquals('https://instagram.com/esports', $fresh->instagram_link);
        $this->assertEquals('https://facebook.com/esports', $fresh->facebook_link);
        $this->assertEquals('https://twitter.com/esports', $fresh->twitter_link);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = $this->createOrganizer();
        $organizer = $user->organizer;

        $organizer->update([
            'companyName' => 'Gaming Corp',
            'companyDescription' => 'Professional esports organization',
            'industry' => 'Entertainment',
            'type' => 'LLC',
            'website_link' => 'https://gamingcorp.com',
        ]);

        $fresh = $organizer->fresh();
        $this->assertEquals('Gaming Corp', $fresh->companyName);
        $this->assertEquals('Professional esports organization', $fresh->companyDescription);
        $this->assertEquals('Entertainment', $fresh->industry);
        $this->assertEquals('LLC', $fresh->type);
        $this->assertEquals('https://gamingcorp.com', $fresh->website_link);
    }

    /** @test */
    public function it_can_have_null_social_links()
    {
        $user = $this->createOrganizer();
        $organizer = $user->organizer;

        $organizer->update([
            'website_link' => null,
            'instagram_link' => null,
            'facebook_link' => null,
            'twitter_link' => null,
        ]);

        $fresh = $organizer->fresh();
        $this->assertNull($fresh->website_link);
        $this->assertNull($fresh->instagram_link);
        $this->assertNull($fresh->facebook_link);
        $this->assertNull($fresh->twitter_link);
    }
}
