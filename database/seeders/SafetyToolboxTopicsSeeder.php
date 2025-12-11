<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ToolboxTalkTopic;
use App\Models\ToolboxTalk;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;

class SafetyToolboxTopicsSeeder extends Seeder
{
    /**
     * Map CSV subcategories to database enum values
     */
    private function mapSubcategory($csvSubcategory): ?string
    {
        $mapping = [
            'fire_safety' => 'fire_safety',
            'first_aid' => 'first_aid',
            'slips_trips_falls' => 'hazard_recognition',
            'electrical' => 'electrical_safety',
            'environmental' => 'hazard_recognition',
            'tools' => 'equipment_safety',
            'hazcom' => 'chemical_safety',
            'ppe' => 'ppe',
            'heights' => 'fall_protection',
            'machinery' => 'equipment_safety',
            'confined_spaces' => 'hazard_recognition',
            'lot' => 'lockout_tagout',
            'hot_work' => 'equipment_safety',
            'general' => 'other',
            'vehicles' => 'equipment_safety',
            'emergency' => 'emergency_procedures',
            'hazmat' => 'chemical_safety',
            'radiation' => 'other',
            'biohazards' => 'other',
        ];

        return $mapping[$csvSubcategory] ?? 'other';
    }

    /**
     * Parse key points string into array
     */
    private function parseKeyPoints($keyPoints): array
    {
        if (empty($keyPoints)) {
            return [];
        }
        
        // Split by comma and clean up
        $points = array_map('trim', explode(',', $keyPoints));
        return array_filter($points);
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Safety Toolbox Topics...');

        // Get a user to assign as creator (prefer HSE officer or admin)
        $creator = User::whereHas('role', function($q) {
            $q->whereIn('name', ['hse_officer', 'admin', 'super_admin']);
        })->first() ?? User::first();

        if (!$creator) {
            $this->command->error('No user found to assign as creator. Please create a user first.');
            return;
        }

        // CSV data parsed into array
        $topicsData = [
            [
                'title' => 'Fire Safety Procedures',
                'description' => 'Basic fire safety and evacuation procedures',
                'category' => 'safety',
                'subcategory' => 'fire_safety',
                'difficulty_level' => 'basic',
                'duration' => 15,
                'key_points' => 'Fire prevention, Evacuation routes, Fire extinguisher use, Emergency exits, Fire alarm activation',
                'regulatory_references' => 'OSHA 1910.157',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'First Aid Basics',
                'description' => 'Basic first aid training for workplace injuries',
                'category' => 'health',
                'subcategory' => 'first_aid',
                'difficulty_level' => 'basic',
                'duration' => 20,
                'key_points' => 'CPR procedure, Wound care and bandaging, Emergency response protocols, AED use, Bleeding control',
                'regulatory_references' => 'OSHA 1910.151',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Slips, Trips and Falls',
                'description' => 'Prevention of common workplace accidents',
                'category' => 'safety',
                'subcategory' => 'slips_trips_falls',
                'difficulty_level' => 'basic',
                'duration' => 10,
                'key_points' => 'Proper footwear requirements, Clear walkways, Spill response procedures, Stair safety, Good housekeeping',
                'regulatory_references' => 'OSHA 1910.22',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Electrical Safety',
                'description' => 'Working safely with electrical equipment and systems',
                'category' => 'safety',
                'subcategory' => 'electrical',
                'difficulty_level' => 'intermediate',
                'duration' => 25,
                'key_points' => 'Lockout/Tagout procedures, Cord and equipment inspection, GFCI use, Overhead power line awareness, Arc flash protection',
                'regulatory_references' => 'OSHA 1910.303',
                'seasonal_relevance' => 'winter',
                'mandatory' => true,
            ],
            [
                'title' => 'Heat Stress Prevention',
                'description' => 'Preventing heat-related illnesses in hot environments',
                'category' => 'health',
                'subcategory' => 'environmental',
                'difficulty_level' => 'basic',
                'duration' => 15,
                'key_points' => 'Adequate hydration, Scheduled rest breaks, Symptom recognition, Acclimatization, Buddy system',
                'regulatory_references' => 'OSHA 1910.151',
                'seasonal_relevance' => 'summer',
                'mandatory' => true,
            ],
            [
                'title' => 'Cold Weather Safety',
                'description' => 'Working safely in cold temperature conditions',
                'category' => 'safety',
                'subcategory' => 'environmental',
                'difficulty_level' => 'basic',
                'duration' => 15,
                'key_points' => 'Proper layering techniques, Frostbite recognition, Wind chill factor awareness, Hypothermia signs, Warm-up breaks',
                'regulatory_references' => 'OSHA 1910.132',
                'seasonal_relevance' => 'winter',
                'mandatory' => true,
            ],
            [
                'title' => 'Hand Tool Safety',
                'description' => 'Safe use and maintenance of hand tools',
                'category' => 'safety',
                'subcategory' => 'tools',
                'difficulty_level' => 'basic',
                'duration' => 15,
                'key_points' => 'Proper tool selection, Regular maintenance, PPE requirements, Inspection before use, Correct storage',
                'regulatory_references' => 'OSHA 1910.242',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Hazard Communication',
                'description' => 'Understanding and managing chemical hazards',
                'category' => 'safety',
                'subcategory' => 'hazcom',
                'difficulty_level' => 'intermediate',
                'duration' => 30,
                'key_points' => 'SDS access and understanding, Proper labeling requirements, Chemical storage compatibility, Spill response, Personal protective equipment',
                'regulatory_references' => 'OSHA 1910.1200',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'PPE Requirements',
                'description' => 'Proper selection and use of personal protective equipment',
                'category' => 'safety',
                'subcategory' => 'ppe',
                'difficulty_level' => 'basic',
                'duration' => 20,
                'key_points' => 'PPE selection criteria, Inspection procedures, Proper donning and doffing, Maintenance and cleaning, Limitations of PPE',
                'regulatory_references' => 'OSHA 1910.132',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Ladder Safety',
                'description' => 'Safe use of ladders and step stools',
                'category' => 'safety',
                'subcategory' => 'heights',
                'difficulty_level' => 'basic',
                'duration' => 15,
                'key_points' => 'Maintain 3-point contact, Proper ladder angle (4:1 ratio), Regular inspection, Secure placement, Never stand on top cap',
                'regulatory_references' => 'OSHA 1910.23',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Machine Guardering',
                'description' => 'Safety around machinery and equipment',
                'category' => 'safety',
                'subcategory' => 'machinery',
                'difficulty_level' => 'intermediate',
                'duration' => 25,
                'key_points' => 'Guard requirements for different machines, Never remove or bypass guards, Emergency stop locations, Regular inspection, Lockout procedures',
                'regulatory_references' => 'OSHA 1910.212',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Fall Protection',
                'description' => 'Working at height safety procedures',
                'category' => 'safety',
                'subcategory' => 'heights',
                'difficulty_level' => 'advanced',
                'duration' => 30,
                'key_points' => 'Harness inspection criteria, Proper anchor point selection, 100% tie-off requirement, Rescue plan awareness, Equipment compatibility',
                'regulatory_references' => 'OSHA 1926.501',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Confined Space Entry',
                'description' => 'Safe procedures for working in confined spaces',
                'category' => 'safety',
                'subcategory' => 'confined_spaces',
                'difficulty_level' => 'advanced',
                'duration' => 45,
                'key_points' => 'Permit-required space identification, Atmospheric testing requirements, Attendant responsibilities, Rescue plan review, Entry team roles',
                'regulatory_references' => 'OSHA 1910.146',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Lockout/Tagout',
                'description' => 'Controlling hazardous energy sources',
                'category' => 'safety',
                'subcategory' => 'lot',
                'difficulty_level' => 'intermediate',
                'duration' => 40,
                'key_points' => 'Six-step procedure, Multiple lockout devices, Verification of isolation, Authorized vs affected employees, Release procedures',
                'regulatory_references' => 'OSHA 1910.147',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Welding Safety',
                'description' => 'Safe welding and cutting practices',
                'category' => 'safety',
                'subcategory' => 'hot_work',
                'difficulty_level' => 'intermediate',
                'duration' => 25,
                'key_points' => 'Adequate ventilation requirements, Fire watch responsibilities, PPE requirements for welding, Cylinder storage and handling, Hot work permit system',
                'regulatory_references' => 'OSHA 1910.252',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Ergonomics',
                'description' => 'Preventing musculoskeletal disorders',
                'category' => 'health',
                'subcategory' => 'ergonomics',
                'difficulty_level' => 'basic',
                'duration' => 20,
                'key_points' => 'Proper lifting techniques, Workstation setup guidelines, Micro-breaks and stretching, Tool and equipment design, Reporting early symptoms',
                'regulatory_references' => 'OSHA General Duty Clause',
                'seasonal_relevance' => 'all_year',
                'mandatory' => false,
            ],
            [
                'title' => 'Noise Exposure',
                'description' => 'Protecting hearing health in noisy environments',
                'category' => 'safety',
                'subcategory' => 'environmental',
                'difficulty_level' => 'basic',
                'duration' => 15,
                'key_points' => 'Hearing protection selection, Noise monitoring results, Audiogram requirements, Dual protection when needed, Equipment maintenance',
                'regulatory_references' => 'OSHA 1910.95',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Respiratory Protection',
                'description' => 'Proper use and maintenance of respirators',
                'category' => 'health',
                'subcategory' => 'ppe',
                'difficulty_level' => 'intermediate',
                'duration' => 30,
                'key_points' => 'Fit testing requirements, Medical clearance, Cleaning and storage procedures, Cartridge change schedules, Seal checks',
                'regulatory_references' => 'OSHA 1910.134',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Crane Safety',
                'description' => 'Safe crane and rigging operations',
                'category' => 'safety',
                'subcategory' => 'machinery',
                'difficulty_level' => 'advanced',
                'duration' => 35,
                'key_points' => 'Load chart interpretation, Signal person requirements, Regular inspection criteria, Swing radius awareness, Lift planning',
                'regulatory_references' => 'OSHA 1926.1400',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Excavation Safety',
                'description' => 'Working in and around excavations',
                'category' => 'safety',
                'subcategory' => 'hazard_recognition',
                'difficulty_level' => 'intermediate',
                'duration' => 30,
                'key_points' => 'Soil classification system, Proper sloping and benching, Access and egress requirements, Spoil pile placement, Daily inspections',
                'regulatory_references' => 'OSHA 1926.650',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Scaffold Safety',
                'description' => 'Safe scaffold use and erection',
                'category' => 'safety',
                'subcategory' => 'heights',
                'difficulty_level' => 'intermediate',
                'duration' => 30,
                'key_points' => 'Competent person requirements, Proper platform construction, Fall protection integration, Guardrail specifications, Inspection protocols',
                'regulatory_references' => 'OSHA 1926.451',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Housekeeping',
                'description' => 'Maintaining clean and organized work areas',
                'category' => 'safety',
                'subcategory' => 'general',
                'difficulty_level' => 'basic',
                'duration' => 10,
                'key_points' => 'Clean-as-you-go principle, Proper waste disposal, Aisle and exit marking, Storage organization, Spill prevention',
                'regulatory_references' => 'OSHA 1910.22',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Vehicle Safety',
                'description' => 'Safe operation of work vehicles and equipment',
                'category' => 'safety',
                'subcategory' => 'vehicles',
                'difficulty_level' => 'intermediate',
                'duration' => 25,
                'key_points' => 'Pre-trip inspection checklist, Backing safety procedures, Speed limit compliance, Seatbelt use, Blind spot awareness',
                'regulatory_references' => 'OSHA 1910.178',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Emergency Response',
                'description' => 'Workplace emergency procedures and preparedness',
                'category' => 'safety',
                'subcategory' => 'emergency',
                'difficulty_level' => 'basic',
                'duration' => 20,
                'key_points' => 'Alarm system locations, Assembly point procedures, Head count protocols, Emergency contact information, First responder roles',
                'regulatory_references' => 'OSHA 1910.38',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Chemical Handling',
                'description' => 'Safe chemical management and storage',
                'category' => 'safety',
                'subcategory' => 'hazmat',
                'difficulty_level' => 'intermediate',
                'duration' => 25,
                'key_points' => 'Compatible storage requirements, Secondary containment, Spill kit locations and use, Ventilation needs, Waste disposal procedures',
                'regulatory_references' => 'OSHA 1910.1200',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Radiation Safety',
                'description' => 'Working with radiation sources and equipment',
                'category' => 'health',
                'subcategory' => 'radiation',
                'difficulty_level' => 'advanced',
                'duration' => 40,
                'key_points' => 'ALARA principles (Time, Distance, Shielding), Monitoring requirements, Contamination control, Emergency procedures, Regulatory documentation',
                'regulatory_references' => 'NRC Regulations',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Biological Hazards',
                'description' => 'Working with biological materials and pathogens',
                'category' => 'health',
                'subcategory' => 'biohazards',
                'difficulty_level' => 'intermediate',
                'duration' => 30,
                'key_points' => 'Universal precautions, Disinfection procedures, Waste disposal protocols, Exposure response, Medical surveillance',
                'regulatory_references' => 'OSHA 1910.1030',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Forklift Operation',
                'description' => 'Safe forklift operation and maintenance',
                'category' => 'safety',
                'subcategory' => 'vehicles',
                'difficulty_level' => 'intermediate',
                'duration' => 30,
                'key_points' => 'Load capacity awareness, Pedestrian interaction, Travel speed control, Load stability, Daily inspection',
                'regulatory_references' => 'OSHA 1910.178',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Trenching Safety',
                'description' => 'Safe excavation and trenching procedures',
                'category' => 'safety',
                'subcategory' => 'hazard_recognition',
                'difficulty_level' => 'advanced',
                'duration' => 35,
                'key_points' => 'Protective system requirements, Atmospheric monitoring, Access/egress spacing, Water accumulation hazards, Utility location',
                'regulatory_references' => 'OSHA 1926.651',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
            [
                'title' => 'Overhead Work',
                'description' => 'Safety procedures for overhead tasks',
                'category' => 'safety',
                'subcategory' => 'heights',
                'difficulty_level' => 'intermediate',
                'duration' => 25,
                'key_points' => 'Falling object protection, Tool tethering, Area demarcation, Communication methods, Weather considerations',
                'regulatory_references' => 'OSHA 1926.451',
                'seasonal_relevance' => 'all_year',
                'mandatory' => true,
            ],
        ];

        $createdTopics = [];
        foreach ($topicsData as $topicData) {
            // Map subcategory
            $mappedSubcategory = $this->mapSubcategory($topicData['subcategory']);
            
            // Parse key points into learning objectives
            $keyPointsArray = $this->parseKeyPoints($topicData['key_points']);
            
            $topic = ToolboxTalkTopic::updateOrCreate(
                ['title' => $topicData['title']],
                [
                    'title' => $topicData['title'],
                    'description' => $topicData['description'],
                    'category' => $topicData['category'],
                    'subcategory' => $mappedSubcategory,
                    'difficulty_level' => $topicData['difficulty_level'],
                    'estimated_duration_minutes' => $topicData['duration'],
                    'key_talking_points' => $topicData['key_points'],
                    'learning_objectives' => $keyPointsArray,
                    'regulatory_references' => $topicData['regulatory_references'],
                    'seasonal_relevance' => $topicData['seasonal_relevance'],
                    'is_active' => true,
                    'is_mandatory' => $topicData['mandatory'],
                    'created_by' => $creator->id,
                ]
            );

            $createdTopics[] = $topic;
            $this->command->info("✓ Created/Updated topic: {$topic->title}");
        }

        $this->command->info('');
        $this->command->info("✓ Created/Updated " . count($createdTopics) . " safety toolbox topics.");

        // Optionally create scheduled toolbox talks
        $this->command->info('');
        $this->command->info('Creating scheduled toolbox talks...');

        $companies = Company::where('is_active', true)->get();
        $scheduledCount = 0;

        foreach ($companies as $company) {
            $departments = Department::where('company_id', $company->id)
                ->where('is_active', true)
                ->get();

            if ($departments->isEmpty()) {
                continue;
            }

            // Get supervisors/HSE officers for this company
            $supervisors = User::where('company_id', $company->id)
                ->whereHas('role', function($q) {
                    $q->whereIn('name', ['hse_officer', 'admin', 'super_admin', 'supervisor']);
                })
                ->where('is_active', true)
                ->get();

            if ($supervisors->isEmpty()) {
                $supervisors = User::where('company_id', $company->id)
                    ->where('is_active', true)
                    ->take(1)
                    ->get();
            }

            if ($supervisors->isEmpty()) {
                continue;
            }

            // Schedule mandatory topics for the next 3 months
            $mandatoryTopics = ToolboxTalkTopic::where('is_mandatory', true)
                ->where('is_active', true)
                ->get();

            foreach ($mandatoryTopics as $topic) {
                // Schedule weekly for the next 12 weeks
                for ($week = 1; $week <= 12; $week++) {
                    $scheduledDate = Carbon::now()->addWeeks($week)->startOfWeek()->addDays(1); // Monday
                    $scheduledTime = '08:00:00';

                    // Check if already scheduled
                    $existing = ToolboxTalk::where('company_id', $company->id)
                        ->where('topic_id', $topic->id)
                        ->where('scheduled_date', $scheduledDate->format('Y-m-d'))
                        ->first();

                    if (!$existing) {
                        $supervisor = $supervisors->random();
                        
                        // Combine date and time for scheduled_date
                        $scheduledDateTime = Carbon::parse($scheduledDate->format('Y-m-d') . ' ' . $scheduledTime);
                        $startTime = $scheduledDateTime;
                        $endTime = $scheduledDateTime->copy()->addMinutes($topic->estimated_duration_minutes);
                        
                        // Generate reference number
                        $referenceNumber = 'TT-' . strtoupper(substr(md5($company->id . $topic->id . $scheduledDateTime->timestamp), 0, 8));
                        
                        ToolboxTalk::create([
                            'reference_number' => $referenceNumber,
                            'company_id' => $company->id,
                            'topic_id' => $topic->id,
                            'supervisor_id' => $supervisor->id,
                            'title' => $topic->title,
                            'description' => $topic->description,
                            'scheduled_date' => $scheduledDateTime,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'duration_minutes' => $topic->estimated_duration_minutes,
                            'location' => 'Main Conference Room',
                            'status' => 'scheduled',
                            'talk_type' => $topic->category === 'health' ? 'health' : ($topic->category === 'environment' ? 'environment' : 'safety'),
                        ]);

                        $scheduledCount++;
                    }
                }
            }
        }

        $this->command->info("✓ Created {$scheduledCount} scheduled toolbox talks.");
        $this->command->info('');
        $this->command->info('Safety Toolbox Topics seeding completed!');
    }
}
