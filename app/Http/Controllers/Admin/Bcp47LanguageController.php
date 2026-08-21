<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bcp47Language;
use Illuminate\Http\Request;

class Bcp47LanguageController extends Controller
{
    public function index()
    {
        $bcp47Languages = Bcp47Language::withCount('baseLanguages')->orderBy('code')->get();
        return view('Admin.setting.bcp47Languages.index', compact('bcp47Languages'));
    }

    public function add()
    {
        return view('Admin.setting.bcp47Languages.add');
    }

    public function addProcc(Request $request)
    {
        $request->validate([
            'code'   => 'required|string|max:20|unique:bcp47_languages,code',
            'name'   => 'nullable|string|max:255',
            'status' => 'nullable|in:0,1',
        ]);

        Bcp47Language::create([
            'code'   => trim($request->code),
            'name'   => $request->name ?? null,
            'status' => $request->input('status', 1),
        ]);

        return redirect()->route('bcp47-languages.index')
            ->with('success', 'BCP 47 language added successfully.');
    }

    public function update($id)
    {
        $bcp47Language = Bcp47Language::findOrFail($id);
        return view('Admin.setting.bcp47Languages.update', compact('bcp47Language'));
    }

    public function updateProcc(Request $request, $id)
    {
        $request->validate([
            'code'   => 'required|string|max:20|unique:bcp47_languages,code,' . $id . ',id',
            'name'   => 'nullable|string|max:255',
            'status' => 'nullable|in:0,1',
        ]);

        $bcp47Language = Bcp47Language::findOrFail($id);
        $bcp47Language->update([
            'code'   => trim($request->code),
            'name'   => $request->name ?? null,
            'status' => $request->input('status', 1),
        ]);

        return redirect()->route('bcp47-languages.index')
            ->with('success', 'BCP 47 language updated successfully.');
    }

    public function delete($id)
    {
        $bcp47Language = Bcp47Language::findOrFail($id);

        $usageCount = $bcp47Language->baseLanguages()->count();
        if ($usageCount > 0) {
            return redirect()->route('bcp47-languages.index')
                ->with('error', 'Cannot delete — this BCP 47 code is assigned to ' . $usageCount . ' base language(s).');
        }

        $bcp47Language->delete();
        return redirect()->route('bcp47-languages.index')
            ->with('success', 'BCP 47 language deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $bcp47Language = Bcp47Language::findOrFail($id);
        $bcp47Language->status = $bcp47Language->status == 1 ? 0 : 1;
        $bcp47Language->save();

        $message = $bcp47Language->status ? 'BCP 47 language enabled successfully.' : 'BCP 47 language disabled successfully.';
        return redirect()->route('bcp47-languages.index')->with('success', $message);
    }
}
