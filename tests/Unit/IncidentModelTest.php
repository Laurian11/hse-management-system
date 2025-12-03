<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_incident_belongs_to_company(): void
    {
        $company = Company::factory()->create();
        $incident = Incident::factory()->create(['company_id' => $company->id]);

        $this->assertInstanceOf(Company::class, $incident->company);
        $this->assertEquals($company->id, $incident->company->id);
    }

    public function test_incident_belongs_to_reporter(): void
    {
        $user = User::factory()->create();
        $incident = Incident::factory()->create(['reported_by' => $user->id]);

        $this->assertInstanceOf(User::class, $incident->reporter);
        $this->assertEquals($user->id, $incident->reporter->id);
    }

    public function test_incident_scope_for_company(): void
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        Incident::factory()->count(3)->create(['company_id' => $company1->id]);
        Incident::factory()->count(2)->create(['company_id' => $company2->id]);

        $incidents = Incident::forCompany($company1->id)->get();

        $this->assertCount(3, $incidents);
        $this->assertTrue($incidents->every(fn($incident) => $incident->company_id === $company1->id));
    }

    public function test_incident_scope_by_status(): void
    {
        Incident::factory()->create(['status' => 'open']);
        Incident::factory()->create(['status' => 'closed']);
        Incident::factory()->create(['status' => 'investigating']);

        $openIncidents = Incident::open()->get();
        $closedIncidents = Incident::closed()->get();

        $this->assertGreaterThan(0, $openIncidents->count());
        $this->assertGreaterThan(0, $closedIncidents->count());
    }

    public function test_incident_can_be_closed(): void
    {
        $incident = Incident::factory()->create(['status' => 'open']);

        $incident->close('Resolved successfully');

        $this->assertEquals('closed', $incident->fresh()->status);
        $this->assertNotNull($incident->fresh()->closed_at);
        $this->assertEquals('Resolved successfully', $incident->fresh()->resolution_notes);
    }

    public function test_incident_can_be_reopened(): void
    {
        $incident = Incident::factory()->create([
            'status' => 'closed',
            'closed_at' => now(),
            'resolution_notes' => 'Test resolution',
        ]);

        $incident->reopen();

        $this->assertEquals('open', $incident->fresh()->status);
        $this->assertNull($incident->fresh()->closed_at);
        $this->assertNull($incident->fresh()->resolution_notes);
    }

    public function test_incident_severity_color(): void
    {
        $critical = Incident::factory()->create(['severity' => 'critical']);
        $high = Incident::factory()->create(['severity' => 'high']);
        $medium = Incident::factory()->create(['severity' => 'medium']);
        $low = Incident::factory()->create(['severity' => 'low']);

        $this->assertEquals('red', $critical->severity_color);
        $this->assertEquals('orange', $high->severity_color);
        $this->assertEquals('yellow', $medium->severity_color);
        $this->assertEquals('green', $low->severity_color);
    }
}

