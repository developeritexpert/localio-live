<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BaseLanguage;

class BaseLanguageController extends Controller
{
    public function index()
    {
        $baseLanguages = BaseLanguage::orderBy('is_master', 'desc')->orderBy('name')->get();
        return view('Admin.setting.baseLanguages.index', compact('baseLanguages'));
    }

    public function add()
    {
        return view('Admin.setting.baseLanguages.add');
    }

    public function addProcc(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:base_languages,code',
            'language_tag' => 'required|string|max:100',
            'is_master' => 'nullable|boolean',
            'status' => 'nullable|in:0,1',
        ]);

        $baseLanguage = new BaseLanguage();
        $baseLanguage->name = $request->name;
        $baseLanguage->code = $request->code;
        $baseLanguage->language_tag = $request->language_tag;
        $baseLanguage->is_master = $request->has('is_master') ? (bool) $request->is_master : false;
        $baseLanguage->status = $request->input('status', 1);

        // If this is set as master, unset any other master
        if ($baseLanguage->is_master) {
            BaseLanguage::where('is_master', true)->update(['is_master' => false]);
        }

        $baseLanguage->save();

        return redirect()->route('base-languages.index')->with('success', 'Base language added successfully.');
    }

    public function update($id)
    {
        $baseLanguage = BaseLanguage::findOrFail($id);
        return view('Admin.setting.baseLanguages.update', compact('baseLanguage'));
    }

    public function updateProcc(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:base_languages,code,' . $id . ',id',
            'language_tag' => 'required|string|max:100',
            'is_master' => 'nullable|boolean',
            'status' => 'nullable|in:0,1',
        ]);

        $baseLanguage = BaseLanguage::findOrFail($id);
        $baseLanguage->name = $request->name;
        $baseLanguage->code = $request->code;
        $baseLanguage->language_tag = $request->language_tag;
        $baseLanguage->is_master = $request->has('is_master') ? (bool) $request->is_master : false;
        $baseLanguage->status = $request->input('status', 1);

        if ($baseLanguage->is_master) {
            BaseLanguage::where('id', '!=', $id)->where('is_master', true)->update(['is_master' => false]);
        }

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
