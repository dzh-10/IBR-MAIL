<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Get a list of employees for auto-complete.
     */
    public function index(Request $request)
    {
        $search = $request->query('q', '');
        $userId = Auth::id();

        $employees = User::where('id', '!=', $userId)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get(['id', 'name', 'email', 'avatar', 'role']);

        return response()->json($employees);
    }
}
