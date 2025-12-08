<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ToolboxTalkTopic;
use App\Models\ToolboxTalk;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;

class ITToolboxTopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding IT Toolbox Topics...');

        // Get IT departments from all companies
        $itDepartments = Department::where('name', 'LIKE', '%IT%')
            ->orWhere('name', 'LIKE', '%Information Technology%')
            ->orWhere('code', 'IT')
            ->get();

        if ($itDepartments->isEmpty()) {
            $this->command->warn('No IT departments found. Creating topics without department relevance.');
        }

        // Get a user to assign as creator (prefer HSE officer or admin)
        $creator = User::whereHas('role', function($q) {
            $q->whereIn('name', ['hse_officer', 'admin', 'super_admin']);
        })->first() ?? User::first();

        // IT Safety Topics
        $itTopics = [
            [
                'title' => 'Cybersecurity Best Practices',
                'description' => 'Essential cybersecurity practices to protect company data and systems from threats.',
                'category' => 'safety',
                'subcategory' => 'electrical_safety',
                'difficulty_level' => 'intermediate',
                'estimated_duration_minutes' => 20,
                'learning_objectives' => [
                    'Understand common cyber threats',
                    'Learn password security best practices',
                    'Recognize phishing attempts',
                    'Know how to report security incidents'
                ],
                'key_talking_points' => 'Strong passwords, two-factor authentication, email security, software updates, suspicious link recognition, data backup procedures.',
                'real_world_examples' => 'Recent phishing attacks, ransomware incidents, data breaches in similar industries.',
                'regulatory_references' => 'Data Protection Act, GDPR compliance, ISO 27001 standards.',
                'presentation_content' => [
                    'Introduction to cybersecurity threats',
                    'Password management best practices',
                    'Email and phishing awareness',
                    'Software update importance',
                    'Incident reporting procedures'
                ],
                'discussion_questions' => [
                    'What are the most common cybersecurity threats you encounter?',
                    'How do you ensure your passwords are secure?',
                    'What should you do if you receive a suspicious email?'
                ],
                'quiz_questions' => [
                    [
                        'question' => 'What is the minimum recommended password length?',
                        'options' => ['6 characters', '8 characters', '12 characters', '16 characters'],
                        'correct_answer' => 2
                    ],
                    [
                        'question' => 'What should you do if you receive a suspicious email?',
                        'options' => ['Open it', 'Delete it', 'Report it to IT', 'Forward it'],
                        'correct_answer' => 2
                    ]
                ],
                'required_materials' => ['Laptop', 'Projector', 'Security awareness handout'],
                'is_mandatory' => true,
            ],
            [
                'title' => 'Electrical Safety in IT Environments',
                'description' => 'Safe handling of electrical equipment, cables, and power systems in IT workspaces.',
                'category' => 'safety',
                'subcategory' => 'electrical_safety',
                'difficulty_level' => 'basic',
                'estimated_duration_minutes' => 15,
                'learning_objectives' => [
                    'Identify electrical hazards in IT environments',
                    'Learn proper cable management',
                    'Understand power surge protection',
                    'Know emergency procedures for electrical incidents'
                ],
                'key_talking_points' => 'Cable management, power strip safety, surge protectors, equipment grounding, overload prevention, emergency shutdown procedures.',
                'real_world_examples' => 'Electrical fires from overloaded circuits, equipment damage from power surges, tripping hazards from cables.',
                'regulatory_references' => 'OSHA electrical safety standards, local electrical codes.',
                'presentation_content' => [
                    'Common electrical hazards in IT',
                    'Cable management best practices',
                    'Power distribution safety',
                    'Surge protection importance',
                    'Emergency procedures'
                ],
                'discussion_questions' => [
                    'What electrical hazards have you noticed in your workspace?',
                    'How do you manage cables in your work area?',
                    'What would you do in case of an electrical emergency?'
                ],
                'quiz_questions' => [
                    [
                        'question' => 'What is the maximum number of devices you should plug into a single power strip?',
                        'options' => ['As many as fit', 'Up to 80% capacity', 'Only 3 devices', 'Depends on voltage'],
                        'correct_answer' => 1
                    ]
                ],
                'required_materials' => ['Power strip samples', 'Cable management examples', 'Safety posters'],
                'is_mandatory' => false,
            ],
            [
                'title' => 'Ergonomics for IT Workers',
                'description' => 'Proper workstation setup and practices to prevent musculoskeletal disorders.',
                'category' => 'health',
                'subcategory' => 'ergonomics',
                'difficulty_level' => 'basic',
                'estimated_duration_minutes' => 15,
                'learning_objectives' => [
                    'Understand proper workstation ergonomics',
                    'Learn correct posture techniques',
                    'Identify ergonomic risk factors',
                    'Know exercises to prevent strain'
                ],
                'key_talking_points' => 'Monitor height, keyboard position, chair adjustment, wrist support, eye level positioning, regular breaks, stretching exercises.',
                'real_world_examples' => 'Carpal tunnel syndrome, neck and back pain, eye strain from prolonged screen time.',
                'regulatory_references' => 'OSHA ergonomic guidelines, workplace health standards.',
                'presentation_content' => [
                    'Ergonomic workstation setup',
                    'Proper posture techniques',
                    'Common ergonomic injuries',
                    'Prevention strategies',
                    'Stretching exercises'
                ],
                'discussion_questions' => [
                    'How is your current workstation set up?',
                    'Do you experience any discomfort during work?',
                    'What changes could improve your workspace ergonomics?'
                ],
                'quiz_questions' => [
                    [
                        'question' => 'How often should you take breaks from screen work?',
                        'options' => ['Every hour', 'Every 2 hours', 'Every 4 hours', 'When you feel tired'],
                        'correct_answer' => 0
                    ]
                ],
                'required_materials' => ['Ergonomic assessment checklist', 'Posture diagrams', 'Exercise guide'],
                'is_mandatory' => false,
            ],
            [
                'title' => 'Data Backup and Recovery Procedures',
                'description' => 'Importance of regular data backups and recovery procedures for business continuity.',
                'category' => 'procedural',
                'subcategory' => 'other',
                'difficulty_level' => 'intermediate',
                'estimated_duration_minutes' => 20,
                'learning_objectives' => [
                    'Understand backup importance',
                    'Learn backup procedures',
                    'Know recovery processes',
                    'Identify critical data'
                ],
                'key_talking_points' => '3-2-1 backup rule, automated backups, cloud storage, local backups, recovery testing, data classification.',
                'real_world_examples' => 'Data loss incidents, successful recovery stories, ransomware recovery.',
                'regulatory_references' => 'Data retention policies, business continuity requirements.',
                'presentation_content' => [
                    'Why backups are critical',
                    'Backup strategies (3-2-1 rule)',
                    'Automated backup systems',
                    'Recovery procedures',
                    'Testing and verification'
                ],
                'discussion_questions' => [
                    'What data do you consider critical?',
                    'How often do you backup your work?',
                    'Have you tested your recovery procedures?'
                ],
                'quiz_questions' => [
                    [
                        'question' => 'What does the 3-2-1 backup rule mean?',
                        'options' => ['3 backups, 2 locations, 1 format', '3 copies, 2 different media, 1 offsite', '3 days, 2 weeks, 1 month', '3 servers, 2 clouds, 1 local'],
                        'correct_answer' => 1
                    ]
                ],
                'required_materials' => ['Backup procedure guide', 'Recovery checklist', 'Data classification chart'],
                'is_mandatory' => true,
            ],
            [
                'title' => 'Server Room Safety',
                'description' => 'Safety protocols for working in server rooms and data centers.',
                'category' => 'safety',
                'subcategory' => 'equipment_safety',
                'difficulty_level' => 'intermediate',
                'estimated_duration_minutes' => 20,
                'learning_objectives' => [
                    'Understand server room hazards',
                    'Learn access control procedures',
                    'Know environmental safety requirements',
                    'Recognize emergency procedures'
                ],
                'key_talking_points' => 'Access control, temperature monitoring, fire suppression, electrical safety, cable management, emergency exits, visitor protocols.',
                'real_world_examples' => 'Server room fires, overheating incidents, unauthorized access breaches.',
                'regulatory_references' => 'Data center safety standards, fire safety regulations.',
                'presentation_content' => [
                    'Server room hazards',
                    'Access control procedures',
                    'Environmental monitoring',
                    'Fire safety systems',
                    'Emergency procedures'
                ],
                'discussion_questions' => [
                    'Who has access to our server room?',
                    'What environmental factors are monitored?',
                    'What would you do in a server room emergency?'
                ],
                'quiz_questions' => [
                    [
                        'question' => 'What is the ideal temperature range for server rooms?',
                        'options' => ['15-20°C', '18-27°C', '25-30°C', '30-35°C'],
                        'correct_answer' => 1
                    ]
                ],
                'required_materials' => ['Access control guide', 'Environmental monitoring checklist', 'Emergency procedure poster'],
                'is_mandatory' => true,
            ],
            [
                'title' => 'Software License Compliance',
                'description' => 'Understanding software licensing and compliance requirements.',
                'category' => 'procedural',
                'subcategory' => 'other',
                'difficulty_level' => 'basic',
                'estimated_duration_minutes' => 15,
                'learning_objectives' => [
                    'Understand software licensing types',
                    'Learn compliance requirements',
                    'Know how to verify licenses',
                    'Recognize risks of non-compliance'
                ],
                'key_talking_points' => 'License types, compliance audits, license tracking, open source licenses, vendor agreements, legal risks.',
                'real_world_examples' => 'Software audit penalties, license violation cases, compliance success stories.',
                'regulatory_references' => 'Software licensing laws, vendor agreements.',
                'presentation_content' => [
                    'Types of software licenses',
                    'Compliance requirements',
                    'License tracking methods',
                    'Audit procedures',
                    'Risk management'
                ],
                'discussion_questions' => [
                    'How do we track software licenses?',
                    'What are the risks of non-compliance?',
                    'Who is responsible for license management?'
                ],
                'quiz_questions' => [
                    [
                        'question' => 'What should you do before installing new software?',
                        'options' => ['Just install it', 'Check license requirements', 'Ask a colleague', 'Use trial version'],
                        'correct_answer' => 1
                    ]
                ],
                'required_materials' => ['License tracking guide', 'Compliance checklist', 'Vendor contact list'],
                'is_mandatory' => false,
            ],
            [
                'title' => 'Network Security Fundamentals',
                'description' => 'Basic network security practices and protocols.',
                'category' => 'safety',
                'subcategory' => 'other',
                'difficulty_level' => 'intermediate',
                'estimated_duration_minutes' => 25,
                'learning_objectives' => [
                    'Understand network security threats',
                    'Learn firewall basics',
                    'Know VPN usage',
                    'Recognize secure network practices'
                ],
                'key_talking_points' => 'Firewalls, VPNs, network segmentation, wireless security, intrusion detection, network monitoring.',
                'real_world_examples' => 'Network breaches, unauthorized access incidents, successful security implementations.',
                'regulatory_references' => 'Network security standards, ISO 27001.',
                'presentation_content' => [
                    'Network security overview',
                    'Firewall configuration',
                    'VPN best practices',
                    'Wireless security',
                    'Monitoring and detection'
                ],
                'discussion_questions' => [
                    'How is our network protected?',
                    'When should you use VPN?',
                    'What suspicious network activity should you report?'
                ],
                'quiz_questions' => [
                    [
                        'question' => 'What is the primary purpose of a firewall?',
                        'options' => ['Speed up network', 'Block unauthorized access', 'Store data', 'Connect devices'],
                        'correct_answer' => 1
                    ]
                ],
                'required_materials' => ['Network diagram', 'Security policy document', 'VPN setup guide'],
                'is_mandatory' => true,
            ],
            [
                'title' => 'Incident Response for IT Security',
                'description' => 'Procedures for responding to IT security incidents.',
                'category' => 'emergency',
                'subcategory' => 'emergency_procedures',
                'difficulty_level' => 'advanced',
                'estimated_duration_minutes' => 30,
                'learning_objectives' => [
                    'Understand incident types',
                    'Learn response procedures',
                    'Know escalation paths',
                    'Recognize containment strategies'
                ],
                'key_talking_points' => 'Incident classification, response team, containment procedures, evidence preservation, communication protocols, recovery steps.',
                'real_world_examples' => 'Malware outbreaks, data breaches, system compromises, successful incident responses.',
                'regulatory_references' => 'Incident response frameworks, data breach notification laws.',
                'presentation_content' => [
                    'Types of security incidents',
                    'Incident response team',
                    'Containment procedures',
                    'Evidence collection',
                    'Recovery and lessons learned'
                ],
                'discussion_questions' => [
                    'What types of incidents have we experienced?',
                    'Who should you contact in case of an incident?',
                    'What information should be preserved?'
                ],
                'quiz_questions' => [
                    [
                        'question' => 'What is the first step in incident response?',
                        'options' => ['Contain the threat', 'Identify the incident', 'Notify management', 'Shut down systems'],
                        'correct_answer' => 1
                    ]
                ],
                'required_materials' => ['Incident response plan', 'Contact list', 'Evidence collection kit'],
                'is_mandatory' => true,
            ],
        ];

        $createdTopics = [];
        foreach ($itTopics as $topicData) {
            $topic = ToolboxTalkTopic::create([
                'title' => $topicData['title'],
                'description' => $topicData['description'],
                'category' => $topicData['category'],
                'subcategory' => $topicData['subcategory'],
                'difficulty_level' => $topicData['difficulty_level'],
                'estimated_duration_minutes' => $topicData['estimated_duration_minutes'],
                'learning_objectives' => $topicData['learning_objectives'],
                'key_talking_points' => $topicData['key_talking_points'],
                'real_world_examples' => $topicData['real_world_examples'],
                'regulatory_references' => $topicData['regulatory_references'],
                'presentation_content' => $topicData['presentation_content'] ?? null,
                'discussion_questions' => $topicData['discussion_questions'] ?? null,
                'quiz_questions' => $topicData['quiz_questions'] ?? null,
                'required_materials' => $topicData['required_materials'] ?? null,
                'department_relevance' => $itDepartments->pluck('id')->toArray(),
                'seasonal_relevance' => 'all_year',
                'is_active' => true,
                'is_mandatory' => $topicData['is_mandatory'] ?? false,
                'created_by' => $creator->id ?? null,
            ]);

            $createdTopics[] = $topic;
            $this->command->info("Created topic: {$topic->title}");
        }

        $this->command->info("Created " . count($createdTopics) . " IT toolbox topics.");

        // Now create scheduled toolbox talks for these topics
        $this->command->info('Scheduling toolbox talks...');

        $companies = Company::where('is_active', true)->get();
        $scheduledCount = 0;

        foreach ($companies as $company) {
            $companyItDepartments = Department::where('company_id', $company->id)
                ->where(function($q) {
                    $q->where('name', 'LIKE', '%IT%')
                      ->orWhere('name', 'LIKE', '%Information Technology%')
                      ->orWhere('code', 'IT');
                })
                ->get();

            if ($companyItDepartments->isEmpty()) {
                // If no IT department, use first department or create talks without department
                $companyItDepartments = Department::where('company_id', $company->id)->take(1)->get();
            }

            // Get supervisors (prefer IT managers or HSE officers)
            $supervisors = User::where('company_id', $company->id)
                ->whereHas('role', function($q) {
                    $q->whereIn('name', ['hse_officer', 'admin', 'hod']);
                })
                ->orWhere(function($q) use ($company) {
                    $q->where('company_id', $company->id)
                      ->where('name', 'LIKE', '%IT%');
                })
                ->get();

            if ($supervisors->isEmpty()) {
                $supervisors = User::where('company_id', $company->id)->take(1)->get();
            }

            if ($supervisors->isEmpty()) {
                continue; // Skip if no supervisors
            }

            // Schedule talks for the next 8 weeks (2 per week)
            $startDate = Carbon::now()->addDays(1)->startOfWeek(); // Next Monday

            foreach ($createdTopics as $index => $topic) {
                // Schedule mandatory topics first, then others
                $weeksAhead = (int)($index / 2); // 2 topics per week
                $dayOfWeek = ($index % 2) === 0 ? 0 : 3; // Monday and Thursday

                $scheduledDate = $startDate->copy()->addWeeks($weeksAhead)->addDays($dayOfWeek);
                $startTime = $scheduledDate->copy()->setTime(9, 0); // 9:00 AM
                $endTime = $startTime->copy()->addMinutes($topic->estimated_duration_minutes);

                $department = $companyItDepartments->random();
                $supervisor = $supervisors->random();

                // Map topic category to talk_type enum
                $talkTypeMap = [
                    'safety' => 'safety',
                    'health' => 'health',
                    'environment' => 'environment',
                    'incident_review' => 'incident_review',
                    'procedural' => 'custom',
                    'emergency' => 'safety',
                ];
                $talkType = $talkTypeMap[$topic->category] ?? 'safety';

                $talk = ToolboxTalk::create([
                    'reference_number' => 'TT-' . date('Ymd') . '-' . str_pad($scheduledCount + 1, 4, '0', STR_PAD_LEFT),
                    'company_id' => $company->id,
                    'department_id' => $department->id,
                    'supervisor_id' => $supervisor->id,
                    'topic_id' => $topic->id,
                    'title' => $topic->title,
                    'description' => $topic->description,
                    'status' => 'scheduled',
                    'scheduled_date' => $scheduledDate,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'location' => 'IT Department / Conference Room',
                    'talk_type' => $talkType,
                    'duration_minutes' => $topic->estimated_duration_minutes,
                    'materials' => $topic->required_materials,
                    'regulatory_references' => $topic->regulatory_references,
                ]);

                $scheduledCount++;
                $this->command->info("Scheduled: {$talk->title} for {$company->name} on {$scheduledDate->format('Y-m-d')}");
            }
        }

        $this->command->info("Scheduled {$scheduledCount} toolbox talks across " . $companies->count() . " companies.");
        $this->command->info('IT Toolbox Topics seeding completed!');
    }
}

