<?php

namespace App\Http\Controllers;

use App\Models\BloodDonation;
use App\Models\BloodRequest;
use App\Models\BloodInventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BloodBankController extends Controller
{
    public function getDashboardStats()
    {
        $user = Auth::user();
        
        if ($user->role === 'donor') {
            return $this->getDonorStats($user);
        } elseif ($user->role === 'hospital') {
            return $this->getHospitalStats($user);
        }
        
        return response()->json(['error' => 'Invalid role'], 403);
    }

    private function getDonorStats($user)
    {
        $donations = BloodDonation::where('donor_id', $user->id)
            ->with('hospital')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $totalDonations = BloodDonation::where('donor_id', $user->id)->count();
        $completedDonations = BloodDonation::where('donor_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $lastDonation = BloodDonation::where('donor_id', $user->id)
            ->where('status', 'completed')
            ->latest('donation_date')
            ->first();

        return response()->json([
            'total_donations' => $totalDonations,
            'completed_donations' => $completedDonations,
            'last_donation_date' => $lastDonation?->donation_date,
            'recent_donations' => $donations,
            'eligible_to_donate' => $this->checkEligibility($user, $lastDonation),
        ]);
    }

    private function getHospitalStats($user)
    {
        $inventory = BloodInventory::where('hospital_id', $user->id)->get();
        $requests = BloodRequest::where('hospital_id', $user->id)
            ->with('matchedDonor')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $totalRequests = BloodRequest::where('hospital_id', $user->id)->count();
        $pendingRequests = BloodRequest::where('hospital_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $fulfilledRequests = BloodRequest::where('hospital_id', $user->id)
            ->where('status', 'fulfilled')
            ->count();

        $availableBloodGroups = [];
        foreach ($inventory as $item) {
            $availableBloodGroups[$item->blood_type] = [
                'available' => $item->units_available,
                'volume_ml' => $item->volume_ml,
                'last_updated' => $item->last_updated,
            ];
        }

        return response()->json([
            'total_requests' => $totalRequests,
            'pending_requests' => $pendingRequests,
            'fulfilled_requests' => $fulfilledRequests,
            'available_blood_groups' => $availableBloodGroups,
            'recent_requests' => $requests,
        ]);
    }

    public function scheduleDonation(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'donor') {
            return response()->json(['error' => 'Only donors can schedule donations'], 403);
        }

        $validated = $request->validate([
            'hospital_id' => 'required|exists:users,id,role,hospital',
            'donation_date' => 'required|date|after:today',
            'donation_time' => 'required|date_format:H:i',
            'donation_type' => 'required|in:regular,emergency,directed',
            'notes' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
        ]);

        $donation = BloodDonation::create([
            'donor_id' => $user->id,
            'hospital_id' => $validated['hospital_id'],
            'donation_type' => $validated['donation_type'],
            'donation_date' => $validated['donation_date'],
            'donation_time' => $validated['donation_time'],
            'status' => 'scheduled',
            'notes' => $validated['notes'] ?? null,
            'location' => $validated['location'] ?? null,
        ]);

        Log::info('Donation scheduled', [
            'donation_id' => $donation->id,
            'donor_id' => $user->id,
            'hospital_id' => $validated['hospital_id'],
        ]);

        return response()->json([
            'message' => 'Donation scheduled successfully',
            'donation' => $donation,
        ], 201);
    }

    public function createBloodRequest(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'hospital') {
            return response()->json(['error' => 'Only hospitals can create blood requests'], 403);
        }

        $validated = $request->validate([
            'urgency' => 'required|in:low,medium,high,critical',
            'blood_type' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'volume_ml' => 'required|integer|min:100|max:1000',
            'patient_details' => 'required|string|max:500',
            'medical_notes' => 'nullable|string|max:1000',
            'required_date' => 'required|date|after_or_equal:today',
            'required_time' => 'nullable|date_format:H:i',
        ]);

        $bloodRequest = BloodRequest::create([
            'hospital_id' => $user->id,
            'request_id' => 'REQ-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
            'urgency' => $validated['urgency'],
            'blood_type' => $validated['blood_type'],
            'volume_ml' => $validated['volume_ml'],
            'patient_details' => $validated['patient_details'],
            'medical_notes' => $validated['medical_notes'] ?? null,
            'required_date' => $validated['required_date'],
            'required_time' => $validated['required_time'] ?? null,
            'status' => 'pending',
        ]);

        $this->findMatchingDonors($bloodRequest);

        Log::info('Blood request created', [
            'request_id' => $bloodRequest->id,
            'hospital_id' => $user->id,
            'blood_type' => $validated['blood_type'],
            'urgency' => $validated['urgency'],
        ]);

        return response()->json([
            'message' => 'Blood request created successfully',
            'request' => $bloodRequest,
        ], 201);
    }

    public function updateInventory(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'hospital') {
            return response()->json(['error' => 'Only hospitals can update inventory'], 403);
        }

        $validated = $request->validate([
            'blood_type' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'volume_ml' => 'required|integer|min:0',
            'units_available' => 'required|integer|min:0',
            'storage_condition' => 'required|in:fresh,refrigerated,frozen',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $inventory = BloodInventory::updateOrCreate(
            [
                'hospital_id' => $user->id,
                'blood_type' => $validated['blood_type'],
            ],
            [
                'volume_ml' => $validated['volume_ml'],
                'units_available' => $validated['units_available'],
                'storage_condition' => $validated['storage_condition'],
                'expiry_date' => $validated['expiry_date'] ?? null,
                'last_updated' => now(),
            ]
        );

        Log::info('Blood inventory updated', [
            'hospital_id' => $user->id,
            'blood_type' => $validated['blood_type'],
            'volume_ml' => $validated['volume_ml'],
        ]);

        return response()->json([
            'message' => 'Blood inventory updated successfully',
            'inventory' => $inventory,
        ]);
    }

    public function findMatchingDonors(BloodRequest $request)
    {
        $compatibleTypes = $this->getCompatibleBloodTypes($request->blood_type);
        
        $matchingDonors = User::where('role', 'donor')
            ->whereHas('donorProfile', function ($query) use ($compatibleTypes) {
                $query->whereIn('blood_group', $compatibleTypes);
            })
            ->whereDoesntHave('donations', function ($query) use ($request) {
                $query->where('donation_date', '>=', $request->required_date)
                    ->where('status', 'completed');
            })
            ->with('donorProfile')
            ->limit(10)
            ->get();

        if ($matchingDonors->isNotEmpty()) {
            $this->notifyMatchingDonors($matchingDonors, $request);
        }

        return $matchingDonors;
    }

    private function getCompatibleBloodTypes($bloodType): array
    {
        $compatibility = [
            'O+' => ['O+', 'A+', 'B+', 'AB+'],
            'O-' => ['O-', 'A-', 'B-', 'AB-'],
            'A+' => ['A+', 'AB+'],
            'A-' => ['A-', 'AB-'],
            'B+' => ['B+', 'AB+'],
            'B-' => ['B-', 'AB-'],
            'AB+' => ['AB+'],
            'AB-' => ['AB-'],
        ];

        return $compatibility[$bloodType] ?? [$bloodType];
    }

    private function checkEligibility($user, $lastDonation): bool
    {
        if (!$lastDonation) {
            return true;
        }

        $daysSinceLastDonation = now()->diffInDays($lastDonation->donation_date);
        $minimumDays = 56; // 8 weeks

        return $daysSinceLastDonation >= $minimumDays;
    }

    private function notifyMatchingDonors($donors, $request)
    {
        foreach ($donors as $donor) {
            Log::info('Notifying matching donor', [
                'donor_id' => $donor->id,
                'request_id' => $request->id,
                'blood_type' => $request->blood_type,
            ]);
        }
    }
}
