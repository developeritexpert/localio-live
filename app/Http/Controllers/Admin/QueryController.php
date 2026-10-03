<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Query;
use App\Services\MailService;
use Illuminate\Http\Request;

class QueryController extends Controller
{
    /**
     * Query List
     */
    public function index()
    {
        $queries = Query::latest()->get();

        return view('Admin.queries.index', compact('queries'));
    }

    /**
     * Query Details
     */
    public function show($id)
    {
        $query = Query::findOrFail($id);

        return view('admin.queries.show', compact('query'));
    }

    /**
     * Delete Query
     */
    public function delete($id)
    {
        $query = Query::findOrFail($id);

        if ($query->attachment && file_exists(public_path('storage/' . $query->attachment))) {
            unlink(public_path('storage/' . $query->attachment));
        }

        $query->delete();

        return redirect()
            ->route('admin.queries.index')
            ->with('success', 'Query deleted successfully.');
    }

    /**
     * Send Email Reply
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $query = Query::findOrFail($id);

        MailService::sendTemplate('query_reply', $query->email, [
            'name'          => $query->name ?? 'User',
            'subject'       => $request->subject,
            'reply_message' => $request->message,
        ], 1);

        return redirect()
            ->back()
            ->with('success', 'Email sent successfully.');
    }
}