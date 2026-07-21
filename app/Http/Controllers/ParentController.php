<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Child;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ParentController extends Controller
{
    // LIST - show families grouped by shared children
    public function index(Request $request)
    {
        // AJAX: return paginated JSON data
        if ($request->ajax() || $request->wantsJson()) {
            return $this->fetchFamilies($request);
        }

        // Stats for the cards
        $stats = $this->getStats();

        return view('parent.index', compact('stats'));
    }

    /**
     * Fetch paginated family data for AJAX calls.
     */
    private function fetchFamilies(Request $request)
    {
        $search  = (string) $request->get('search', '');
        $perPage = $request->get('per_page', 10);
        $page    = $request->get('page', 1);

        // Build families collection
        $families = $this->buildFamilies($search);

        // Paginate the collection manually
        $total    = $families->count();
        $paginated = $families->forPage($page, $perPage)->values();

        return response()->json([
            'data'         => $paginated,
            'current_page' => (int) $page,
            'per_page'     => (int) $perPage,
            'total'        => $total,
            'last_page'    => (int) ceil($total / $perPage),
            'from'         => $total > 0 ? (($page - 1) * $perPage) + 1 : 0,
            'to'           => min($page * $perPage, $total),
        ]);
    }

    /**
     * Build normalized family groups from the database.
     * Uses batch queries to avoid N+1.
     */
    private function buildFamilies(?string $search = ''): \Illuminate\Support\Collection
    {
        $search = (string) $search;
        $query = \App\Models\User::where('role', 'parent1');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $mainParents = $query->with('children')->get();

        // Collect ALL child IDs in one pass
        $allChildIds = $mainParents->pluck('children.*.id')->flatten()->unique()->filter()->toArray();

        // Single batch query: all parent2/guardian users linked to any of these children
        $relatedUsers = collect();
        if (!empty($allChildIds)) {
            $relatedUsers = \App\Models\User::whereIn('role', ['parent2', 'guardian'])
                ->whereHas('guardianships', fn($q) => $q->whereIn('child_id', $allChildIds))
                ->with(['guardianships' => fn($q) => $q->whereIn('child_id', $allChildIds)])
                ->get();
        }

        $usedUserIds = [];
        $families = collect();

        foreach ($mainParents as $main) {
            $childIds = $main->children->pluck('id')->toArray();

            // Find related users by matching child_ids from the batch
            $second   = null;
            $guardian = null;

            if (!empty($childIds)) {
                $second = $relatedUsers
                    ->where('role', 'parent2')
                    ->reject(fn($u) => in_array($u->id, $usedUserIds))
                    ->first(fn($u) => !empty(array_intersect(
                        $u->guardianships->pluck('child_id')->toArray(),
                        $childIds
                    )));

                $guardian = $relatedUsers
                    ->where('role', 'guardian')
                    ->reject(fn($u) => in_array($u->id, $usedUserIds))
                    ->first(fn($u) => !empty(array_intersect(
                        $u->guardianships->pluck('child_id')->toArray(),
                        $childIds
                    )));
            }

            $families->push([
                'main'       => $main,
                'second'     => $second,
                'guardian'   => $guardian,
                'children'   => $main->children,
                'childCount' => $main->children->count(),
            ]);

            $usedUserIds[] = $main->id;
            if ($second)   $usedUserIds[] = $second->id;
            if ($guardian) $usedUserIds[] = $guardian->id;
        }

        return $families;
    }

    /**
     * Get aggregate stats for the stat cards.
     */
    private function getStats(): array
    {
        $families  = $this->buildFamilies();
        $totalMain = \App\Models\User::where('role', 'parent1')->count();

        return [
            'totalFamilies'  => $families->count(),
            'totalChildren'  => $families->sum('childCount'),
            'totalMain'      => $totalMain,
            'totalSecond'    => \App\Models\User::where('role', 'parent2')->count(),
            'totalGuardian'  => \App\Models\User::where('role', 'guardian')->count(),
            'verified'       => $families->filter(fn($f) => $f['main']->verified)->count(),
            'guardianCount'  => $families->filter(fn($f) => $f['guardian'])->count(),
        ];
    }

    /**
     * Export all families to CSV.
     */
    public function exportCsv()
    {
        $families = $this->buildFamilies();
        $filename = 'loving_guardians_' . now()->format('Y-m-d_His') . '.csv';

        return response()->stream(
            $this->exportCallback($families),
            200,
            ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => "attachment; filename=\"$filename\""]
        );
    }

    /**
     * Export all families to Excel (.xls) — styled HTML table.
     */
    public function exportExcel()
    {
        $families = $this->buildFamilies();
        $filename = 'loving_guardians_' . now()->format('Y-m-d_His') . '.xls';

        $html = view('parent.exports.excel', compact('families'))->render();

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control'       => 'no-cache',
        ]);
    }

    /**
     * Export all families to PDF.
     */
    public function exportPdf()
    {
        $families = $this->buildFamilies();
        $filename = 'loving_guardians_' . now()->format('Y-m-d_His') . '.pdf';

        $pdf = Pdf::loadView('parent.exports.pdf', compact('families'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    /**
     * Shared CSV/Excel export callback.
     */
    private function exportCallback($families, string $delimiter = ','): \Closure
    {
        return function () use ($families, $delimiter) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF"); // BOM

            fputcsv($output, [
                '#', 'Main Parent', 'Phone', 'Email', 'Role',
                'Second Parent', 'Second Phone', 'Second Email',
                'Guardian', 'Guardian Phone', 'Guardian Email',
                'Children', 'Child Count', 'Verified',
            ], $delimiter);

            $i = 1;
            foreach ($families as $family) {
                $main   = $family['main'];
                $second = $family['second'];
                $guard  = $family['guardian'];
                $kids   = $family['children']->pluck('name')->implode(', ');

                fputcsv($output, [
                    $i++,
                    $main->name,
                    $main->phone_number ?? '-',
                    $main->email ?? '-',
                    ucfirst(str_replace('parent', 'Parent ', $main->role)),
                    $second ? $second->name : '-',
                    $second ? ($second->phone_number ?? '-') : '-',
                    $second ? ($second->email ?? '-') : '-',
                    $guard ? $guard->name : '-',
                    $guard ? ($guard->phone_number ?? '-') : '-',
                    $guard ? ($guard->email ?? '-') : '-',
                    $kids ?: '-',
                    $family['childCount'],
                    $main->verified ? 'Yes' : 'No',
                ], $delimiter);
            }

            fclose($output);
        };
    }

    // CREATE FORM
    public function create()
    {
        return view('parent.create');
    }

    // STORE
    public function store(Request $request)
    {
        $isJson = $request->expectsJson();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ($isJson ? 'nullable' : 'required') . '|string|min:8',
            'phone' => 'required|string|max:20',
            'address' => $isJson ? 'nullable|string' : 'required|string',
            'age' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'role' => 'nullable|in:parent1,parent2,guardian',
            'verified' => 'nullable|boolean',
            'child_ids' => 'nullable|array',
            'child_ids.*' => 'exists:children,id',
        ];

        // Add second parent & guardian rules for full form
        if (!$isJson) {
            $rules = array_merge($rules, [
                'second_name' => 'nullable|string|max:255',
                'second_email' => 'nullable|email|unique:users,email',
                'second_password' => 'nullable|string|min:8',
                'second_phone' => 'nullable|string|max:20',
                'second_address' => 'nullable|string',
                'second_age' => 'nullable|string',
                'second_photo' => 'nullable|image|max:2048',
                'guardian_name' => 'nullable|string|max:255',
                'guardian_email' => 'nullable|email|unique:users,email',
                'guardian_password' => 'nullable|string|min:8',
                'guardian_phone' => 'nullable|string|max:20',
                'guardian_address' => 'nullable|string',
                'guardian_age' => 'nullable|string',
                'guardian_photo' => 'nullable|image|max:2048',
            ]);
        }

        $request->validate($rules);

        $role = $request->input('role', 'parent1');

        $user = \App\Models\User::create([
            'name' => $request->name,
            'age' => $request->age,
            'email' => $request->email,
            'password' => Hash::make($request->password ?: 'password123'),
            'phone_number' => $request->phone,
            'address' => $request->address,
            'role' => $role,
            'verified' => $request->has('verified') || $isJson,
        ]);

        // Save photo
        if ($request->hasFile('photo')) {
            $user->update(['photo' => $request->file('photo')->store('parents', 'public')]);
        }

        // Create children from form & link them to this parent (and second/guardian)
        $childrenInput = $request->input('children', []);
        $createdChildIds = [];

        foreach ($childrenInput as $i => $childData) {
            if (empty($childData['name'])) continue;

            // Validate IC format & extract DOB
            $ic = $childData['ic_number'] ?? null;
            $dob = null;
            if ($ic) {
                $cleanIc = str_replace(['-', ' '], '', $ic);
                if (strlen($cleanIc) !== 12 || !preg_match('/^\d{12}$/', $cleanIc)) {
                    return back()->withInput()->withErrors(['children.' . $i . '.ic_number' => 'IC must be exactly 12 digits (YYMMDDXXXXXX).']);
                }
                $dob = $this->dobFromIc($cleanIc);
                if (!$dob) {
                    return back()->withInput()->withErrors(['children.' . $i . '.ic_number' => 'Invalid or future date in IC (YYMMDD).']);
                }
            }

            // Age auto-calculated from IC's year (simple: current year - birth year)
            $age = (int) ($childData['age'] ?? 0);
            if (!$age && $dob) {
                $age = now()->year - (int) substr($dob, 0, 4);
            }
            if ($age < 1 || $age > 12) {
                return back()->withInput()->withErrors(['children.' . $i . '.age' => 'Child age must be between 1 and 12.']);
            }

            $child = \App\Models\Child::create([
                'name'         => $childData['name'],
                'age'          => $age,
                'ic_number'    => $ic,
                'classroom_id' => $childData['classroom_id'] ?? null,
                'dob'          => $childData['dob'] ?? $dob,
                'is_active'    => true,
                'enrollment_date' => now(),
                'qr_code'      => 'KID-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            ]);

            $createdChildIds[] = $child->id;

            // Link main parent
            \App\Models\Guardianship::create([
                'user_id'       => $user->id,
                'child_id'      => $child->id,
                'relationship'  => $role === 'parent2' ? 'second_parent' : ($role === 'guardian' ? 'guardian' : 'main_parent'),
                'is_emergency_contact' => $role === 'parent1',
            ]);
        }

        // Create second parent & guardian (if provided)
        $secondUser = null;
        $guardianUser = null;

        if (!$isJson) {
            if ($request->filled('second_name') && $request->filled('second_email')) {
                $secondUser = \App\Models\User::firstOrCreate(
                    ['email' => $request->second_email],
                    [
                        'name' => $request->second_name,
                        'age' => $request->second_age,
                        'password' => Hash::make($request->second_password ?? 'password123'),
                        'phone_number' => $request->second_phone,
                        'address' => $request->second_address,
                        'role' => 'parent2',
                        'verified' => true,
                    ]
                );
                if ($request->hasFile('second_photo')) {
                    $secondUser->update(['photo' => $request->file('second_photo')->store('parents', 'public')]);
                }
            }

            if ($request->filled('guardian_name') && $request->filled('guardian_email')) {
                $guardianUser = \App\Models\User::firstOrCreate(
                    ['email' => $request->guardian_email],
                    [
                        'name' => $request->guardian_name,
                        'age' => $request->guardian_age,
                        'password' => Hash::make($request->guardian_password ?? 'password123'),
                        'phone_number' => $request->guardian_phone,
                        'address' => $request->guardian_address,
                        'role' => 'guardian',
                        'verified' => true,
                    ]
                );
                if ($request->hasFile('guardian_photo')) {
                    $guardianUser->update(['photo' => $request->file('guardian_photo')->store('parents', 'public')]);
                }
            }
        }

        // Link second parent & guardian to the same children as main parent
        if ($secondUser) {
            foreach ($createdChildIds as $childId) {
                \App\Models\Guardianship::updateOrCreate(
                    ['user_id' => $secondUser->id, 'child_id' => $childId, 'relationship' => 'second_parent'],
                    ['is_emergency_contact' => false]
                );
            }
        }
        if ($guardianUser) {
            foreach ($createdChildIds as $childId) {
                \App\Models\Guardianship::updateOrCreate(
                    ['user_id' => $guardianUser->id, 'child_id' => $childId, 'relationship' => 'guardian'],
                    ['is_emergency_contact' => true]
                );
            }
        }

        if ($isJson) {
            return response()->json(['success' => true, 'user_id' => $user->id, 'message' => 'Registered & linked.']);
        }

        return redirect()->route('parents.index')
            ->with('success', 'Parent registered successfully!');
    }

    /**
     * Extract DOB from Malaysian IC format (YYMMDDXXXXXX or YYMMDD-XX-XXXX).
     * Supports birth years 2000-2026 (children aged 0-17 as of 2026).
     * Returns null if date is invalid or in the future.
     */
    private function dobFromIc(?string $ic): ?string
    {
        if (!$ic) return null;
        $clean = str_replace(['-', ' '], '', $ic);
        if (strlen($clean) < 6) return null;
        $yy = (int) substr($clean, 0, 2);
        $mm = (int) substr($clean, 2, 2);
        $dd = (int) substr($clean, 4, 2);

        // Children: YY 00-26 → 2000-2026 (ages 0-26, main use is 1-17)
        $year = 2000 + $yy;

        if (!checkdate($mm, $dd, $year)) return null;

        // DOB must not be in the future
        $dob = sprintf('%04d-%02d-%02d', $year, $mm, $dd);
        if ($dob > now()->toDateString()) return null;

        return $dob;
    }

    // AJAX CHECK EMAIL
    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        if (!$email) {
            return response()->json(['available' => false, 'message' => 'Email is required']);
        }

        $exists = \App\Models\User::where('email', $email)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Email already taken' : 'Email available'
        ]);
    }

    // SHOW
    public function show($id)
    {
        $parent = \App\Models\User::with('children')->findOrFail($id);

        // Find related family members — other users linked to the same children
        $childIds = $parent->children->pluck('id');
        $relatedUsers = \App\Models\User::where('id', '!=', $parent->id)
            ->whereHas('guardianships', function ($q) use ($childIds) {
                $q->whereIn('child_id', $childIds);
            })
            ->with(['guardianships' => function ($q) use ($childIds) {
                $q->whereIn('child_id', $childIds);
            }])
            ->get()
            ->groupBy('role');

        return view('parent.show', compact('parent', 'relatedUsers'));
    }

    public function edit($id)
    {
        $currentUser = \App\Models\User::with('children')->findOrFail($id);
        $childIds = $currentUser->children->pluck('id')->toArray();

        // Find all family members sharing these children
        $familyUsers = \App\Models\User::whereHas('guardianships', fn($q) => $q->whereIn('child_id', $childIds))
            ->whereIn('role', ['parent1', 'parent2', 'guardian'])
            ->with('children')
            ->get()
            ->keyBy('role');

        $main = $familyUsers->get('parent1');
        $second = $familyUsers->get('parent2');
        $guardian = $familyUsers->get('guardian');

        // If editing a parent2 or guardian, ensure the main parent is also loaded
        if (!$main && $currentUser->role !== 'parent1') {
            $main = $currentUser; // fallback if somehow no parent1 found
        }
        if (!$main) $main = $currentUser;

        // All children linked to any family member
        $allFamilyChildIds = \App\Models\Guardianship::whereIn('user_id', $familyUsers->pluck('id'))
            ->pluck('child_id')->unique()->toArray();
        $familyChildren = \App\Models\Child::whereIn('id', $allFamilyChildIds)->with('classroom')->get();
        $allChildren = \App\Models\Child::with('classroom')->orderBy('name')->get();

        return view('parent.edit', compact(
            'currentUser', 'main', 'second', 'guardian',
            'familyChildren', 'allChildren', 'allFamilyChildIds'
        ));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'age' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'verified' => 'nullable|boolean',
            'child_ids' => 'nullable|array',
            'child_ids.*' => 'exists:children,id',
        ]);

        $parent = \App\Models\User::findOrFail($id);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'age' => $request->age,
            'phone_number' => $request->phone,
            'address' => $request->address,
            'verified' => $request->has('verified'),
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Update photo
        if ($request->hasFile('photo')) {
            if ($parent->photo && Storage::disk('public')->exists($parent->photo)) {
                Storage::disk('public')->delete($parent->photo);
            }
            $data['photo'] = $request->file('photo')->store('parents', 'public');
        }

        $parent->update($data);

        // Sync children only when child_ids is present in the request
        if ($request->has('child_ids')) {
            $childIds = $request->input('child_ids', []);
            $relationship = match($parent->role) {
                'parent2' => 'second_parent',
                'guardian' => 'guardian',
                default => 'main_parent',
            };

            \App\Models\Guardianship::where('user_id', $parent->id)
                ->whereNotIn('child_id', $childIds)
                ->delete();

            foreach ($childIds as $childId) {
                \App\Models\Guardianship::where('child_id', $childId)
                    ->where('relationship', $relationship)
                    ->where('user_id', '!=', $parent->id)
                    ->delete();

                \App\Models\Guardianship::updateOrCreate(
                    ['user_id' => $parent->id, 'child_id' => $childId],
                    ['relationship' => $relationship, 'is_emergency_contact' => $parent->role === 'parent1']
                );
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Updated.']);
        }

        return redirect()->route('parent.index')->with('success', 'Parent updated successfully.');
    }

    // DELETE
    public function destroy($id)
    {
        $parent = \App\Models\User::findOrFail($id);

        // Delete photo
        if ($parent->photo && Storage::disk('public')->exists($parent->photo)) {
            Storage::disk('public')->delete($parent->photo);
        }

        $parent->delete();

        return redirect()->route('parent.index')
            ->with('success', 'Parent deleted successfully!');
    }
}
