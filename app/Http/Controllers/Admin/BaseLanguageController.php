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
        // 1. Delete base languages whose linked country no longer exists
        // (This is a fallback in case cascade delete is not triggered, or for untracked country_id rows)
        $existingCountryIds = Country::pluck('id')->toArray();
        BaseLanguage::whereNotNull('country_id')
            ->whereNotIn('country_id', $existingCountryIds)
            ->delete();

        // Also clean up any orphaned records by name that aren't mapped
        $existingNames = Country::pluck('name')->toArray();
        BaseLanguage::whereNull('country_id')
            ->whereNotIn('name', $existingNames)
            ->delete();

        // 2. Delete the obsolete entry "English (US) — Master"
        BaseLanguage::where('name', 'English (US) — Master')->delete();

        // 3. Sync existing countries to base languages
        $countries = Country::all();
        $bcp47List = Bcp47Language::all();

        foreach ($countries as $c) {
            // Find base language by country_id first, then fallback to name
            $bl = BaseLanguage::where('country_id', $c->id)->first();
            if (!$bl) {
                $bl = BaseLanguage::where('name', $c->name)->first();
                if ($bl) {
                    $bl->update(['country_id' => $c->id]);
                }
            }

            if (!$bl) {
                // Determine BCP 47 code
                $bcp = $bcp47List->first(function($item) use ($c) {
                    return strtolower($item->code) === strtolower($c->country_code) ||
                           str_ends_with(strtolower($item->code), '-' . strtolower($c->country_code));
                });
                $defaultBcp = $bcp ?: $bcp47List->firstWhere('code', 'en-US');

                BaseLanguage::create([
                    'country_id'        => $c->id,
                    'name'              => $c->name,
                    'bcp47_language_id' => $defaultBcp ? $defaultBcp->id : 1,
                    'language_tag'      => $defaultBcp ? ($defaultBcp->name ?? $defaultBcp->code) : 'English',
                    'status'            => $c->status ?? 1,
                    'is_master'         => ($c->name === 'United States - English'),
                ]);
            } else {
                // If it already exists, update name and status to keep them in sync with Country
                $bl->update([
                    'name'      => $c->name,
                    'status'    => $c->status ?? 1,
                    'is_master' => ($c->name === 'United States - English'),
                ]);
            }
        }

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

        $country = Country::where('name', $request->name)->first();

        BaseLanguage::create([
            'country_id'        => $country ? $country->id : null,
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
            'name'              => 'nullable|string|max:255|unique:base_languages,name,' . $id . ',id',
            'bcp47_language_id' => 'required|exists:bcp47_languages,id',
            'status'            => 'nullable|in:0,1',
        ]);

        $baseLanguage = BaseLanguage::findOrFail($id);
        $dataToUpdate = [
            'bcp47_language_id' => $request->bcp47_language_id,
            'status'            => $request->input('status', 1),
        ];

        if ($request->filled('name')) {
            $country = Country::where('name', $request->name)->first();
            $dataToUpdate['country_id'] = $country ? $country->id : null;
            $dataToUpdate['name'] = $request->name;
            $dataToUpdate['is_master'] = ($request->name === 'United States - English');
        }

        $baseLanguage->update($dataToUpdate);

        return redirect()->route('base-languages.index')
            ->with('success', 'Base language updated successfully.');
    }

    public function delete($id)
    {
        $baseLanguage = BaseLanguage::findOrFail($id);
        
        // The user explicitly wants to allow deletion and let the database 
        // automatically set the connected Languages' base_language_id to NULL.
        $baseLanguage->delete();

        return redirect()->route('base-languages.index')
            ->with('success', 'Base language deleted successfully. Any linked regions have had their base language set to empty.');
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
