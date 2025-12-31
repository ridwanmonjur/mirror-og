<?php

namespace Tests\Unit\Other;

use Tests\TestCase;
use App\Models\Award;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AwardModelTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $award = Award::create([
            'title' => 'Best Player',
            'image' => 'trophy.png',
            'description' => 'Awarded to the best player',
        ]);

        $this->assertEquals('Best Player', $award->title);
        $this->assertEquals('trophy.png', $award->image);
        $this->assertEquals('Awarded to the best player', $award->description);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $award = new Award();

        $this->assertEquals('awards', $award->getTable());
    }

    /** @test */
    public function it_can_create_multiple_awards()
    {
        Award::create([
            'title' => 'Gold Medal',
            'image' => 'gold.png',
            'description' => 'First place',
        ]);

        Award::create([
            'title' => 'Silver Medal',
            'image' => 'silver.png',
            'description' => 'Second place',
        ]);

        $this->assertEquals(2, Award::count());
    }
}
