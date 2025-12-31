<?php

namespace Tests\Unit\Other;

use Tests\TestCase;
use App\Models\CountryRegion;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;

class CountryRegionModelTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $country = CountryRegion::create([
            'name' => 'Malaysia',
            'emoji_flag' => '🇲🇾',
            'type' => 'country',
            'sort_order' => 1,
        ]);

        $this->assertEquals('Malaysia', $country->name);
        $this->assertEquals('🇲🇾', $country->emoji_flag);
        $this->assertEquals('country', $country->type);
        $this->assertEquals(1, $country->sort_order);
    }

    /** @test */
    public function it_does_not_use_timestamps()
    {
        $country = new CountryRegion();

        $this->assertNull($country->timestamps);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $country = new CountryRegion();

        $this->assertEquals('countries_and_regions', $country->getTable());
    }

    /** @test */
    public function it_has_cache_constants()
    {
        $this->assertEquals('countries', CountryRegion::CACHE_KEY);
        $this->assertEquals(36000, CountryRegion::CACHE_DURATION);
    }

    /** @test */
    public function it_can_get_all_cached()
    {
        CountryRegion::create([
            'name' => 'Malaysia',
            'emoji_flag' => '🇲🇾',
            'type' => 'country',
            'sort_order' => 1,
        ]);

        CountryRegion::create([
            'name' => 'Singapore',
            'emoji_flag' => '🇸🇬',
            'type' => 'country',
            'sort_order' => 2,
        ]);

        $countries = CountryRegion::getAllCached();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $countries);
        $this->assertCount(2, $countries);
    }

    /** @test */
    public function it_caches_results()
    {
        CountryRegion::create([
            'name' => 'Malaysia',
            'emoji_flag' => '🇲🇾',
            'type' => 'country',
            'sort_order' => 1,
        ]);

        // First call
        $countries1 = CountryRegion::getAllCached();

        // Verify cache exists
        $this->assertTrue(Cache::has(CountryRegion::CACHE_KEY));

        // Second call should use cache
        $countries2 = CountryRegion::getAllCached();

        $this->assertEquals($countries1->count(), $countries2->count());
    }

    /** @test */
    public function it_can_clear_cache()
    {
        CountryRegion::create([
            'name' => 'Malaysia',
            'emoji_flag' => '🇲🇾',
            'type' => 'country',
            'sort_order' => 1,
        ]);

        CountryRegion::getAllCached();

        $this->assertTrue(Cache::has(CountryRegion::CACHE_KEY));

        CountryRegion::clearCache();

        $this->assertFalse(Cache::has(CountryRegion::CACHE_KEY));
    }

    /** @test */
    public function it_orders_by_sort_order()
    {
        CountryRegion::create([
            'name' => 'Singapore',
            'emoji_flag' => '🇸🇬',
            'type' => 'country',
            'sort_order' => 2,
        ]);

        CountryRegion::create([
            'name' => 'Malaysia',
            'emoji_flag' => '🇲🇾',
            'type' => 'country',
            'sort_order' => 1,
        ]);

        $countries = CountryRegion::getAllCached();

        $this->assertEquals('Malaysia', $countries->first()->name);
        $this->assertEquals('Singapore', $countries->last()->name);
    }
}
