<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BaseLanguage;
use App\Models\Bcp47Language;
use App\Models\Country;
use Illuminate\Http\Request;

class BaseLanguageController extends Controller
{
    public function index()
    {
        $baseLanguages = BaseLanguage::with('bcp47Language')->orderBy('name')->get();
        return view('Admin.setting.baseLanguages.index', compact('baseLanguages'));
    }

    public function add()
    {
        $existingNames  = BaseLanguage::pluck('name')->toArray();
        $countries      = Country::where('status', 1)
            ->whereNotIn('name', $existingNames)
            ->orderBy('name')
            ->get();
        $bcp47Languages = Bcp47Language::where('status', 1)->orderBy('code')->get();

        return view('Admin.setting.baseLanguages.add', compact('countries', 'bcp47Languages'));
    }

    public function addProcc(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255|unique:base_languages,name',
            'bcp47_language_id' => 'required|exists:bcp47_languages,id',
            'status'            => 'nullable|in:0,1',
        ]);

        BaseLanguage::create([
            'name'              => $request->name,
            'bcp47_language_id' => $request->bcp47_language_id,
            'language_tag'      => null,
            'is_master'         => ($request->name === 'United States - English'),
            'status'            => $request->input('status', 1),
        ]);

        return redirect()->route('base-languages.index')
            ->with('success', 'Base language added successfully.');
    }

    public function update($id)
    {
        $baseLanguage   = BaseLanguage::with('bcp47Language')->findOrFail($id);
        $existingNames  = BaseLanguage::where('id', '!=', $id)->pluck('name')->toArray();
        $countries      = Country::where('status', 1)
            ->whereNotIn('name', $existingNames)
            ->orderBy('name')
            ->get();
        $bcp47Languages = Bcp47Language::where('status', 1)->orderBy('code')->get();

        return view('Admin.setting.baseLanguages.update', compact('baseLanguage', 'countries', 'bcp47Languages'));
    }

    public function updateProcc(Request $request, $id)
    {
        $request->validate([
            'name'              => 'required|string|max:255|unique:base_languages,name,' . $id . ',id',
            'bcp47_language_id' => 'required|exists:bcp47_languages,id',
            'status'            => 'nullable|in:0,1',
        ]);

        $baseLanguage = BaseLanguage::findOrFail($id);
        $baseLanguage->update([
            'name'              => $request->name,
            'bcp47_language_id' => $request->bcp47_language_id,
            'is_master'         => ($request->name === 'United States - English'),
            'status'            => $request->input('status', 1),
        ]);

        return redirect()->route('base-languages.index')
            ->with('success', 'Base language updated successfully.');
    }

    public function delete($id)
    {
        $baseLanguage = BaseLanguage::findOrFail($id);
        $baseLanguage->delete();

        return redirect()->route('base-languages.index')
            ->with('success', 'Base language deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $baseLanguage = BaseLanguage::findOrFail($id);
        $baseLanguage->status = $baseLanguage->status == 1 ? 0 : 1;
        $baseLanguage->save();

        $message = $baseLanguage->status ? 'Base language enabled successfully.' : 'Base language disabled successfully.';
        return redirect()->route('base-languages.index')->with('success', $message);
    }
}
