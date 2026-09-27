<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $departments = Department::active()
            ->withCount('doctors')
            ->get();

        $featuredDoctors = Doctor::available()
            ->with(['user', 'department'])
            ->take(6)
            ->get();

        return view('home', compact('departments', 'featuredDoctors'));
    }

    public function doctors(Request $request): View
    {
        $departmentSlug = $request->query('department');
        $search = $request->query('search');

        $departments = Department::active()->get();

        $doctorsQuery = Doctor::available()->with(['user', 'department', 'schedules']);

        if ($departmentSlug) {
            $doctorsQuery->whereHas('department', function ($query) use ($departmentSlug): void {
                $query->where('slug', $departmentSlug);
            });
        }

        if ($search) {
            $doctorsQuery->where(function ($query) use ($search): void {
                $query->whereHas('user', function ($sub) use ($search): void {
                    $sub->where('name', 'like', "%{$search}%");
                })->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        $doctors = $doctorsQuery->paginate(9)->withQueryString();

        return view('doctors.index', compact('doctors', 'departments', 'departmentSlug', 'search'));
    }

    public function doctorDetail(Doctor $doctor): View
    {
        $doctor->load(['user', 'department', 'schedules']);

        return view('doctors.show', compact('doctor'));
    }
}
