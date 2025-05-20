<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Income;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IncomeController extends Controller
{
    /**
     * Display a listing of the logged-in user’s incomes.
     */
    public function index()
    {
        $user = Auth::user();

        // 1) Build your two-level group of user IDs
        if (is_null($user->created_by)) {
            // Root user: yourself + anyone you created
            $children = User::where('created_by', $user->id)->pluck('id');
            $visibleUserIds = $children->isEmpty()
                ? collect([$user->id])
                : $children->push($user->id);
        } else {
            // Child user: yourself + your creator
            $visibleUserIds = collect([$user->id, $user->created_by]);
        }

        // 2) Fetch incomes belonging to that group, newest first
        $incomes = Income::with('user')
            ->whereIn('user_id', $visibleUserIds)
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('frontend.incomes.index', compact('incomes'));
    }

    /**
     * Show the form for creating a new income.
     */
    public function create()
    {
        return view('frontend.incomes.create');
    }

    /**
     * Display the specified income.
     */
    public function show(Income $income)
    {
        // ensure visibility if needed...
        return view('frontend.incomes.show', compact('income'));
    }

    /**
     * Store a newly created income in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'identifier' => 'nullable|string|max:255',
            'amount'     => 'required|numeric|min:0',
            'date'       => 'required|date',
        ]);

        // stamp with the current user's ID
        $data['user_id'] = Auth::id();

        Income::create($data);

        return redirect()
            ->route('incomes.index')
            ->with('success', 'Entrata registrata!');
    }

    /**
     * Show the form for editing the specified income.
     */
    public function edit(Income $income)
    {
        // Only allow owner
        if ($income->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        // reuse the same form as create
        return view('frontend.incomes.create', compact('income'));
    }

    /**
     * Update the specified income in storage.
     */
    public function update(Request $request, Income $income)
    {
        // Only allow owner
        if ($income->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $data = $request->validate([
            'identifier' => 'nullable|string|max:255',
            'amount'     => 'required|numeric|min:0',
            'date'       => 'required|date',
        ]);

        $income->update($data);

        return redirect()
            ->route('incomes.index')
            ->with('success', 'Entrata aggiornata!');
    }

    /**
     * Remove the specified income from storage.
     */
    public function destroy(Income $income)
    {
        // Only allow owner
        if ($income->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $income->delete();

        return back()->with('success', 'Entrata rimossa.');
    }

    /**
     * (Optional) Create a range-sum income entry (date = from).
     */
    public function storeRange(Request $request)
    {
        $data = $request->validate([
            'from'   => 'required|date',
            'to'     => 'required|date|after_or_equal:from',
            'amount' => 'required|numeric|min:0',
        ]);

        $data['user_id'] = Auth::id();

        $inc = Income::create([
            'date'       => $data['from'],
            'amount'     => $data['amount'],
            'user_id'    => $data['user_id'],
            'identifier' => null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'income' => $inc], 201);
        }

        return back()->with('success', 'Intervallo aggiunto all\'entrata!');
    }
}
