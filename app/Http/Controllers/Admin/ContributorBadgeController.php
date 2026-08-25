<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContributorBadge;

class ContributorBadgeController extends Controller
{
    public function index()
    {
        $badges = ContributorBadge::orderBy('min_reviews', 'asc')->get();
        return view('Admin.contributor-badges.index', compact('badges'));
    }

    public function create()
    {
        return view('Admin.contributor-badges.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'min_reviews' => 'required|integer|min:0',
            'max_reviews' => 'nullable|integer|gt:min_reviews',
            'icon' => 'nullable|string|max:255',
        ]);

        ContributorBadge::create($request->all());

        return redirect()->route('admin.contributor.badges')->with('success', 'Badge created successfully.');
    }

    public function edit($id)
    {
        $badge = ContributorBadge::findOrFail($id);
        return view('Admin.contributor-badges.edit', compact('badge'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'min_reviews' => 'required|integer|min:0',
            'max_reviews' => 'nullable|integer|gt:min_reviews',
            'icon' => 'nullable|string|max:255',
        ]);

        $badge = ContributorBadge::findOrFail($id);
        $badge->update($request->all());

        return redirect()->route('admin.contributor.badges')->with('success', 'Badge updated successfully.');
    }

    public function destroy($id)
    {
        $badge = ContributorBadge::findOrFail($id);
        $badge->delete();

        return redirect()->route('admin.contributor.badges')->with('success', 'Badge deleted successfully.');
    }
}
