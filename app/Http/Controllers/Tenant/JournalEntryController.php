<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\JournalEntry;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = JournalEntry::with(['details.account', 'user'])
                ->orderBy('created_at', 'desc');

            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $total = $query->count();
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $entries = $query->offset($offset)->limit($limit)->get();

            return response()->json([
                'data' => $entries,
                'total' => $total
            ]);
        }

        return view('tenant.accounting.journal_entries.index', [
            'config' => [
                'routes' => [
                    'index' => route('journal-entries.index'),
                ],
                'tokens' => [
                    'csrf' => csrf_token()
                ]
            ]
        ]);
    }
}
