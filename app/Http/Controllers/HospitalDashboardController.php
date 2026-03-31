<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HospitalDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:hospital');
    }

    /**
     * Display the hospital dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $hospitalProfile = $user->hospitalProfile;
        
        // Get dashboard statistics
        $stats = [
            'total_donors' => \App\Models\DonorProfile::count(),
            'recent_donations' => \App\Models\Post::where('type', 'donation')
                ->where('status', 'completed')
                ->whereDate('created_at', '>=', now()->subDays(30))
                ->count(),
            'blood_requests' => \App\Models\Post::where('type', 'request')
                ->where('status', 'pending')
                ->count(),
            'available_blood_groups' => $this->getAvailableBloodGroups(),
        ];

        // Get recent activities
        $recentActivities = \App\Models\Post::with('user')
            ->whereIn('type', ['donation', 'request'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'user' => $user,
            'hospitalProfile' => $hospitalProfile,
            'stats' => $stats,
            'recentActivities' => $recentActivities
        ]);
    }

    /**
     * Get available blood groups statistics
     */
    private function getAvailableBloodGroups()
    {
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $stats = [];

        foreach ($bloodGroups as $group) {
            $stats[$group] = [
                'available' => \App\Models\DonorProfile::where('blood_group', $group)->count(),
                'recent_donations' => \App\Models\Post::where('type', 'donation')
                    ->where('status', 'completed')
                    ->whereHas('user.donorProfile', function($query) use ($group) {
                        $query->where('blood_group', $group);
                    })
                    ->whereDate('created_at', '>=', now()->subDays(30))
                    ->count(),
            ];
        }

        return $stats;
    }

    /**
     * Show hospital profile
     */
    public function profile()
    {
        $user = Auth::user();
        $hospitalProfile = $user->hospitalProfile;
        
        return response()->json([
            'user' => $user,
            'hospitalProfile' => $hospitalProfile
        ]);
    }

    /**
     * Update hospital profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $hospitalProfile = $user->hospitalProfile;

        $request->validate([
            'hospital_name' => 'required|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'emergency_contact' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        // Update user profile
        $user->update([
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        // Update hospital profile
        $hospitalProfile->update([
            'hospital_name' => $request->hospital_name,
            'license_number' => $request->license_number,
            'emergency_contact' => $request->emergency_contact,
        ]);

        return response()->json([
            'message' => 'Profile updated successfully!',
            'user' => $user,
            'hospitalProfile' => $hospitalProfile
        ]);
    }

    /**
     * Show blood request form
     */
    public function createBloodRequest()
    {
        return response()->json([
            'message' => 'Blood request form endpoint'
        ]);
    }

    /**
     * Store blood request
     */
    public function storeBloodRequest(Request $request)
    {
        $request->validate([
            'blood_group' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'units_needed' => 'required|integer|min:1|max:50',
            'urgency_level' => 'required|in:normal,urgent,critical',
            'patient_name' => 'required|string|max:255',
            'reason' => 'required|string|max:1000',
            'required_date' => 'required|date|after:today',
        ]);

        // Map UI urgency to DB urgency
        $urgencyMap = ['normal' => 'medium', 'urgent' => 'high', 'critical' => 'critical'];
        $dbUrgency = $urgencyMap[$request->urgency_level] ?? 'medium';

        $bloodRequest = \App\Models\BloodRequest::create([
            'hospital_id' => Auth::id(),
            'request_id' => 'REQ-' . strtoupper(bin2hex(random_bytes(3))),
            'blood_type' => $request->blood_group,
            'volume_ml' => $request->units_needed * 450, // 1 unit approx 450ml
            'urgency' => $dbUrgency,
            'patient_details' => $request->patient_name . ': ' . $request->reason,
            'required_date' => $request->required_date,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Blood request posted successfully!',
            'request' => $bloodRequest
        ], 201);
    }
}
