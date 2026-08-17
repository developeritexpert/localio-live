<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BaseLanguage;
use App\Models\Country;

class BaseLanguageController extends Controller
{
    public function index()
    {
        $baseLanguages = BaseLanguage::orderBy('name')->get();
        return view('Admin.setting.baseLanguages.index', compact('baseLanguages'));
    }

    public function add()
    {
        $existingNames = BaseLanguage::pluck('name')->toArray();
        $countries = Country::where('status', 1)
            ->whereNotIn('name', $existingNames)
            ->orderBy('name')
            ->get();

        return view('Admin.setting.baseLanguages.add', compact('countries'));
    }

    public function addProcc(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:base_languages,name',
            'code' => 'required|string|max:50|unique:base_languages,code',
            'status' => 'nullable|in:0,1',
        ]);

        $baseLanguage = new BaseLanguage();
        $baseLanguage->name = $request->name;
        $baseLanguage->code = $request->code;
        $baseLanguage->language_tag = $request->language_tag ?? null;
        $baseLanguage->is_master = ($request->name === 'United States - English');
        $baseLanguage->status = $request->input('status', 1);
        $baseLanguage->save();

        return redirect()->route('base-languages.index')->with('success', 'Base language added successfully.');
    }

    public function update($id)
    {
        $baseLanguage = BaseLanguage::findOrFail($id);
        $existingNames = BaseLanguage::where('id', '!=', $id)->pluck('name')->toArray();
        $countries = Country::where('status', 1)
            ->whereNotIn('name', $existingNames)
            ->orderBy('name')
            ->get();

        return view('Admin.setting.baseLanguages.update', compact('baseLanguage', 'countries'));
    }

    public function updateProcc(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:base_languages,name,' . $id . ',id',
            'code' => 'required|string|max:50|unique:base_languages,code,' . $id . ',id',
            'status' => 'nullable|in:0,1',
        ]);

        $baseLanguage = BaseLanguage::findOrFail($id);
        $baseLanguage->name = $request->name;
        $baseLanguage->code = $request->code;
        if ($request->filled('language_tag')) {
            $baseLanguage->language_tag = $request->language_tag;
        }
        $baseLanguage->is_master = ($request->name === 'United States - English');
        $baseLanguage->status = $request->input('status', 1);
        $baseLanguage->save();

        return redirect()->route('base-languages.index')->with('success', 'Base language updated successfully.');
    }

    public function delete($id)
    {
        $baseLanguage = BaseLanguage::findOrFail($id);
        $baseLanguage->delete();

        return redirect()->route('base-languages.index')->with('success', 'Base language deleted successfully.');
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
