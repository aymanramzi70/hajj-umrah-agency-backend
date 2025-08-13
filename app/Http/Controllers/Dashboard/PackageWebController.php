<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; 

class PackageWebController extends Controller
{
    /**
     * Display a listing of the packages.
     */
    public function index(Request $request)
    {
        $query = Package::query();
        $user = Auth::user();

        
        
        
        

        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        }

        
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $packages = $query->latest()->paginate(10);

        $packageTypes = ['Hajj', 'Umrah', 'Tour'];
        $packageStatuses = ['active', 'full', 'archived'];

        return view('packages.index', compact('packages', 'packageTypes', 'packageStatuses', 'user')); 
    }

    /**
     * Show the form for creating a new package.
     */
    public function create()
    {
        
        if (Auth::user()->role !== 'admin') {
            abort(403, 'غير مصرح لك بإنشاء باقات جديدة.');
        }
        return view('packages.create');
    }

    /**
     * Store a newly created package in storage.
     */
    public function store(Request $request)
    {
        
        if (Auth::user()->role !== 'admin') {
            abort(403, 'غير مصرح لك بإضافة هذه الباقة.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', Rule::in(['Hajj', 'Umrah', 'Tour'])],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'price_per_person' => ['required', 'numeric', 'min:0'],
            'agent_price_per_person' => ['nullable', 'numeric', 'min:0'],
            'number_of_days' => ['required', 'integer', 'min:1'],
            'available_seats' => ['required', 'integer', 'min:0'],
            'status' => ['nullable', 'string', Rule::in(['active', 'full', 'archived'])],
            'includes_text' => ['nullable', 'string'],
            'excludes_text' => ['nullable', 'string'],
        ]);

        $includes = $request->filled('includes_text') ? array_filter(array_map('trim', explode(',', $request->input('includes_text')))) : [];
        $excludes = $request->filled('excludes_text') ? array_filter(array_map('trim', explode(',', $request->input('excludes_text')))) : [];

        Package::create(array_merge($request->except(['includes_text', 'excludes_text']), [
            'includes' => $includes,
            'excludes' => $excludes,
        ]));

        return redirect()->route('packages.index')->with('success', 'تم إضافة الباقة بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        
        return view('packages.show', compact('package')); 
        
        
    }

    /**
     * Show the form for editing the specified package.
     */
    public function edit(Package $package)
    {
        
        if (Auth::user()->role !== 'admin') {
            abort(403, 'غير مصرح لك بتعديل هذه الباقة.');
        }
        $package->includes_text = implode(', ', $package->includes ?? []);
        $package->excludes_text = implode(', ', $package->excludes ?? []);

        return view('packages.edit', compact('package'));
    }

    /**
     * Update the specified package in storage.
     */
    public function update(Request $request, Package $package)
    {
        
        if (Auth::user()->role !== 'admin') {
            abort(403, 'غير مصرح لك بتحديث هذه الباقة.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', Rule::in(['Hajj', 'Umrah', 'Tour'])],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'price_per_person' => ['required', 'numeric', 'min:0'],
            'agent_price_per_person' => ['nullable', 'numeric', 'min:0'],
            'number_of_days' => ['required', 'integer', 'min:1'],
            'available_seats' => ['required', 'integer', 'min:0'],
            'status' => ['nullable', 'string', Rule::in(['active', 'full', 'archived'])],
            'includes_text' => ['nullable', 'string'],
            'excludes_text' => ['nullable', 'string'],
        ]);

        $includes = $request->filled('includes_text') ? array_filter(array_map('trim', explode(',', $request->input('includes_text')))) : [];
        $excludes = $request->filled('excludes_text') ? array_filter(array_map('trim', explode(',', $request->input('excludes_text')))) : [];

        $package->update(array_merge($request->except(['includes_text', 'excludes_text']), [
            'includes' => $includes,
            'excludes' => $excludes,
        ]));

        return redirect()->route('packages.index')->with('success', 'تم تحديث الباقة بنجاح.');
    }

    /**
     * Remove the specified package from storage.
     */
    public function destroy(Package $package)
    {
        
        if (Auth::user()->role !== 'admin') {
            abort(403, 'غير مصرح لك بحذف هذه الباقة.');
        }
        $package->delete();

        return redirect()->route('packages.index')->with('success', 'تم حذف الباقة بنجاح.');
    }
}
