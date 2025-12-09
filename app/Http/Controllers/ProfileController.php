<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\ActivityLog;
use App\Models\UserSession;

class ProfileController extends Controller
{
    /**
     * Display the user's profile
     */
    public function show()
    {
        $user = auth()->user();
        $user->load(['company', 'department', 'role', 'directSupervisor']);
        
        // Get recent activity
        $recentActivity = ActivityLog::byUser($user->id)
            ->latest()
            ->limit(10)
            ->get();
        
        // Get active sessions
        $activeSessions = UserSession::where('user_id', $user->id)
            ->where('is_active', true)
            ->latest()
            ->get();
        
        return view('profile.show', compact('user', 'recentActivity', 'activeSessions'));
    }

    /**
     * Show the form for editing the user's profile
     */
    public function edit()
    {
        $user = auth()->user();
        $user->load(['company', 'department', 'role']);
        
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'nationality' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'emergency_contacts' => 'nullable|array',
            'emergency_contacts.*.name' => 'required_with:emergency_contacts|string|max:255',
            'emergency_contacts.*.relationship' => 'required_with:emergency_contacts|string|max:100',
            'emergency_contacts.*.phone' => 'required_with:emergency_contacts|string|max:20',
            'known_allergies' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldData = $user->toArray();
        $data = $request->except(['profile_photo', 'emergency_contacts', 'known_allergies']);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo'] = $path;
        }

        // Handle emergency contacts
        if ($request->has('emergency_contacts')) {
            $data['emergency_contacts'] = $request->emergency_contacts;
        }

        // Handle known allergies
        if ($request->has('known_allergies')) {
            $data['known_allergies'] = $request->known_allergies;
        }

        $user->update($data);

        ActivityLog::log('update', 'profile', 'User', $user->id, "Updated profile: {$user->name}", $oldData, $user->toArray());

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
            'must_change_password' => false,
        ]);

        ActivityLog::log('password_change', 'profile', 'User', $user->id, "Changed password");

        return redirect()->route('profile.show')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Delete profile photo
     */
    public function deletePhoto()
    {
        $user = auth()->user();
        
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }
        
        $user->update(['profile_photo' => null]);

        ActivityLog::log('update', 'profile', 'User', $user->id, "Deleted profile photo");

        return redirect()->back()
            ->with('success', 'Profile photo deleted successfully.');
    }
}

