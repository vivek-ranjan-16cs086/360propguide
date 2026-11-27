<?php

namespace Tests\Feature;

use App\Models\Query;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminQueryLocationFilterTest extends TestCase
{
    private int $cityId;
    private int $sublocationId;
    private int $otherCityId;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.prefix' => '',
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('locations', function ($table) {
            $table->id();
            $table->string('city');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('projects', function ($table) {
            $table->id();
            $table->string('slug')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('sublocation_id')->nullable();
            $table->string('cities')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('queries', function ($table) {
            $table->id();
            $table->string('name')->nullable();
            $table->bigInteger('mobile')->nullable();
            $table->string('email')->nullable();
            $table->text('submittedVia')->nullable();
            $table->longText('message')->nullable();
            $table->longText('reply')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        $this->cityId = $this->createLocation('Pune');
        $this->sublocationId = $this->createLocation('Baner', $this->cityId);
        $this->otherCityId = $this->createLocation('Mumbai');

        DB::table('projects')->insert([
            [
                'slug' => 'pune-project',
                'location_id' => $this->cityId,
                'sublocation_id' => null,
                'cities' => 'Pune',
            ],
            [
                'slug' => 'baner-project',
                'location_id' => $this->cityId,
                'sublocation_id' => $this->sublocationId,
                'cities' => 'Pune',
            ],
            [
                'slug' => 'mumbai-project',
                'location_id' => $this->otherCityId,
                'sublocation_id' => null,
                'cities' => 'Mumbai',
            ],
        ]);

        $this->createLead('Pune lead', 'https://example.test/projects/pune-project');
        $this->createLead('Baner lead', 'https://example.test/projects/baner-project');
        $this->createLead('Mumbai lead', 'https://example.test/projects/mumbai-project');
    }

    public function test_city_filter_includes_leads_for_its_sublocations(): void
    {
        $response = $this->withoutMiddleware()->getJson(route('queries.ajax-list', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'order' => [['column' => 0, 'dir' => 'asc']],
            'columns' => [['data' => 'id']],
            'search' => ['value' => ''],
            'location_id' => $this->cityId,
        ]));

        $response->assertOk();
        $this->assertSame(
            ['Baner lead', 'Pune lead'],
            collect($response->json('aaData'))->pluck('name')->sort()->values()->all()
        );
        $this->assertSame(2, $response->json('iTotalDisplayRecords'));
    }

    public function test_sublocation_filter_excludes_other_locations(): void
    {
        $response = $this->withoutMiddleware()->getJson(route('queries.ajax-list', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'order' => [['column' => 0, 'dir' => 'asc']],
            'columns' => [['data' => 'id']],
            'search' => ['value' => ''],
            'location_id' => $this->sublocationId,
        ]));

        $response->assertOk();
        $this->assertSame(
            ['Baner lead'],
            collect($response->json('aaData'))->pluck('name')->all()
        );
        $this->assertSame(1, $response->json('iTotalDisplayRecords'));
    }

    public function test_location_without_projects_returns_no_leads(): void
    {
        $emptyLocationId = $this->createLocation('Goa');
        $response = $this->withoutMiddleware()->getJson(route('queries.ajax-list', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'order' => [['column' => 0, 'dir' => 'asc']],
            'columns' => [['data' => 'id']],
            'search' => ['value' => ''],
            'location_id' => $emptyLocationId,
        ]));

        $response->assertOk();
        $this->assertSame([], $response->json('aaData'));
        $this->assertSame(0, $response->json('iTotalDisplayRecords'));
    }

    private function createLocation(string $city, ?int $parentId = null): int
    {
        return DB::table('locations')->insertGetId([
            'city' => $city,
            'parent_id' => $parentId,
            'status' => true,
        ]);
    }

    private function createLead(string $name, string $submittedVia): void
    {
        Query::create([
            'name' => $name,
            'submittedVia' => $submittedVia,
        ]);
    }
}
