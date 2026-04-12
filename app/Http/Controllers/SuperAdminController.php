<?php

namespace App\Http\Controllers;

use App\Models\AdminProfile;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function pendingAdmins(Request $request)
    {
        // Get all pending admins with their associated user data
        $admins = AdminProfile::with('user')
            ->where('status', 'pending')
            ->get();

        return response()->json($admins);
    }

    public function approveAdmin(Request $request, $id)
    {
        $profile = AdminProfile::findOrFail($id);
        
        $profile->status = 'approved';
        $profile->verified_by = $request->user()->id; // Super admin's ID
        $profile->verified_at = now();
        $profile->save();

        return response()->json([
            'message' => 'Admin approved successfully.',
            'admin' => $profile->load('user')
        ]);
    }

    public function rejectAdmin(Request $request, $id)
    {
        $profile = AdminProfile::findOrFail($id);
        
        $profile->status = 'rejected';
        $profile->verified_by = $request->user()->id;
        $profile->verified_at = now();
        $profile->save();

        return response()->json([
            'message' => 'Admin rejected.'
        ]);
    }
}
