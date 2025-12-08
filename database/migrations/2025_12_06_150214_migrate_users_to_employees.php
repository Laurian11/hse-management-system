<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Employee;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing users to employees
        $users = User::whereNotNull('company_id')
                    ->whereNull('deleted_at')
                    ->get();

        foreach ($users as $user) {
            // Split name into first and last name
            $nameParts = explode(' ', $user->name, 2);
            $firstName = $nameParts[0] ?? $user->name;
            $lastName = $nameParts[1] ?? '';

            // Check if employee already exists
            $existingEmployee = Employee::where('employee_id_number', $user->employee_id_number)
                                       ->orWhere('email', $user->email)
                                       ->first();

            if (!$existingEmployee) {
                Employee::create([
                    'company_id' => $user->company_id,
                    'user_id' => $user->id, // Link to user account
                    'department_id' => $user->department_id,
                    'employee_id_number' => $user->employee_id_number ?? 'EMP-' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'profile_photo' => $user->profile_photo,
                    'date_of_birth' => $user->date_of_birth,
                    'nationality' => $user->nationality,
                    'blood_group' => $user->blood_group,
                    'emergency_contacts' => $user->emergency_contacts,
                    'date_of_hire' => $user->date_of_hire,
                    'employment_type' => $user->employment_type ?? 'full_time',
                    'employment_status' => $user->is_active ? 'active' : 'terminated',
                    'job_title' => $user->job_title,
                    'job_role_code' => $user->job_role_code,
                    'job_competency_matrix_id' => $user->job_competency_matrix_id,
                    'hse_training_history' => $user->hse_training_history,
                    'competency_certificates' => $user->competency_certificates,
                    'known_allergies' => $user->known_allergies,
                    'biometric_template_id' => $user->biometric_template_id,
                    'is_active' => $user->is_active,
                    'deactivated_at' => $user->deactivated_at,
                    'deactivation_reason' => $user->deactivation_reason,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove employees that were created from users
        Employee::whereNotNull('user_id')->delete();
    }
};
