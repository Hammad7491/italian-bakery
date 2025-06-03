<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use App\Models\Recipe;
use App\Models\LaborCost;
use App\Models\ReturnedGood;
use Illuminate\Http\Request;
use App\Models\ExternalSupply;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ExternalSuppliesController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1) Build your two-level visibility group
        if (is_null($user->created_by)) {
            // Top-level: yourself + anyone you created
            $visibleUserIds = User::where('created_by', $user->id)
                                  ->pluck('id')
                                  ->push($user->id)
                                  ->unique();
        } else {
            // Child user: yourself + your creator
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        // 2) Load supplies & returns for that group
        $supplies = ExternalSupply::with(['client','recipes.recipe','user'])
                        ->whereIn('user_id', $visibleUserIds)
                        ->get();

        $returns  = ReturnedGood::with(['client','recipes.supplyLine.recipe','user'])
                        ->whereIn('user_id', $visibleUserIds)
                        ->get();

        // 3) Flatten into one collection
        $all = collect();

        foreach ($supplies as $supply) {
            $all->push([
                'type'               => 'supply',
                'client'             => $supply->client->name,
                'date'               => $supply->supply_date->toDateString(),
                'external_supply_id' => $supply->id,
                'lines'              => $supply->recipes,
                'revenue'            => $supply->total_amount,
                'created_by'         => $supply->user->name ?? '—',
            ]);
        }

        foreach ($returns as $return) {
            $returnedLines = $return->recipes->map(fn($r) => (object)[
                'recipe'       => $r->supplyLine->recipe ?? null,
                'qty'          => $r->qty,
                'total_amount' => $r->total_amount,
            ]);

            $all->push([
                'type'               => 'return',
                'client'             => $return->client->name,
                'date'               => $return->return_date->toDateString(),
                'external_supply_id' => $return->external_supply_id,
                'lines'              => $returnedLines,
                'revenue'            => -1 * $return->total_amount,
                'created_by'         => $return->user->name ?? '—',
            ]);
        }

        // 4) Group by client, then sort & group by date
        $grouped = $all
            ->groupBy('client')
            ->map(function ($byClient) {
                return $byClient
                    ->sortByDesc('date')
                    ->groupBy('date');
            });

        return view('frontend.external-supplies.index', ['all' => $grouped]);
    }

    public function show(ExternalSupply $externalSupply)
    {
        $externalSupply->load(['client', 'recipes.recipe', 'user']);

        return view('frontend.external-supplies.show', compact('externalSupply'));
    }

    public function create()
    {
        $user = Auth::user();

        if (is_null($user->created_by)) {
            $groupUserIds = User::where('created_by', $user->id)
                                ->pluck('id')
                                ->push($user->id)
                                ->unique();
        } else {
            $groupUserIds = collect([$user->id, $user->created_by])->unique();
        }

        $groupRootId = $user->created_by ?? $user->id;
        $laborCost   = LaborCost::where('user_id', $groupRootId)->first();
        $user        = Auth::user();

        // 1) Build your two-level group of user IDs
        if (is_null($user->created_by)) {
            // Root user → see yourself + anyone you created
            $visibleUserIds = \App\Models\User::where('created_by', $user->id)
                                  ->pluck('id')
                                  ->push($user->id)
                                  ->unique();
        } else {
            // Child user → see yourself + your creator
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        // 2) Fetch clients owned by those users OR global clients (user_id IS NULL)
        $clients = \App\Models\Client::with('user')
                      ->where(function($q) use ($visibleUserIds) {
                          $q->whereIn('user_id', $visibleUserIds)
                            ->orWhereNull('user_id');
                      })
                      ->latest()
                      ->paginate(10);
        $recipes   = Recipe::where('labor_cost_mode', 'external')->whereIn('user_id', $groupUserIds)->get();
        $templates = ExternalSupply::where('save_template', true)
                                   ->whereIn('user_id', $groupUserIds)
                                   ->pluck('supply_name', 'id');

        return view('frontend.external-supplies.create', compact(
            'laborCost',
            'clients',
            'recipes',
            'templates'
        ));
    }

    public function store(Request $request)
    {
        $messages = [
            'supply_name.required_if'     => 'Il nome della fornitura è obbligatorio quando si salva come modello.',
            'client_id.required'          => 'Devi selezionare un cliente.',
            'client_id.exists'            => 'Il cliente selezionato non esiste.',
            'supply_date.required'        => 'La data della fornitura è obbligatoria.',
            'supply_date.date'            => 'Inserisci una data valida.',
            'recipes.required'            => 'Devi aggiungere almeno una ricetta.',
            'recipes.*.id.required'       => 'Seleziona una ricetta per ogni riga.',
            'recipes.*.id.exists'         => 'La ricetta selezionata non esiste.',
            'recipes.*.price.required'    => 'Il prezzo è obbligatorio e deve essere un numero.',
            'recipes.*.qty.required'      => 'La quantità è obbligatoria e deve essere un numero intero.',
            'recipes.*.total_amount.required' => 'L\'importo totale è obbligatorio e deve essere un numero.',
        ];

        $data = $request->validate([
            'supply_name'            => 'required_if:template_action,template,both|max:255',
            'template_action'        => 'nullable|in:none,template,both',
            'client_id'              => 'required|exists:clients,id',
            'supply_date'            => 'required|date',
            'recipes'                => 'required|array|min:1',
            'recipes.*.id'           => 'required|exists:recipes,id',
            'recipes.*.price'        => 'required|numeric|min:0',
            'recipes.*.qty'          => 'required|integer|min:0',
            'recipes.*.total_amount' => 'required|numeric|min:0',
        ], $messages);

        $saveTemplate = in_array($data['template_action'], ['template','both']);
        $totalAmount  = array_sum(array_column($data['recipes'], 'total_amount'));

        $supply = ExternalSupply::create([
            'client_id'     => $data['client_id'],
            'supply_name'   => $data['supply_name'],
            'supply_date'   => $data['supply_date'],
            'total_amount'  => $totalAmount,
            'save_template' => $saveTemplate,
            'user_id'       => auth()->id(),
        ]);
        foreach ($data['recipes'] as $row) {
            $supply->recipes()->create([
                'recipe_id'    => $row['id'],
                'category'     => $row['category'] ?? '',
                'price'        => $row['price'],
                'qty'          => $row['qty'],
                'total_amount' => $row['total_amount'],
                'user_id'      => auth()->id(),
            ]);
        }

        return redirect()
            ->route('external-supplies.create')
            ->with('success', 'Fornitura esterna salvata con successo!');
    }

    public function getTemplate($id)
    {
        $supply = ExternalSupply::with('recipes')->findOrFail($id);

        $rows = $supply->recipes->map(fn($r) => [
            'recipe_id'    => $r->recipe_id,
            'price'        => $r->price,
            'qty'          => $r->qty,
            'total_amount' => $r->total_amount,
        ]);

        return response()->json([
            'client_id'       => $supply->client_id,
            'supply_name'     => $supply->supply_name,
            'supply_date'     => $supply->supply_date->format('Y-m-d'),
            'template_action' => $supply->save_template ? 'template' : 'none',
            'rows'            => $rows,
        ]);
    }

    public function edit(ExternalSupply $externalSupply)
    {
        $externalSupply->load('recipes.recipe');

        $laborCost = LaborCost::first();
        $user      = Auth::user();

        // 1) Build your two-level group of user IDs
        if (is_null($user->created_by)) {
            // Root user → see yourself + anyone you created
            $visibleUserIds = \App\Models\User::where('created_by', $user->id)
                                  ->pluck('id')
                                  ->push($user->id)
                                  ->unique();
        } else {
            // Child user → see yourself + your creator
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        // 2) Fetch clients owned by those users OR global clients (user_id IS NULL)
        $clients = \App\Models\Client::with('user')
                      ->where(function($q) use ($visibleUserIds) {
                          $q->whereIn('user_id', $visibleUserIds)
                            ->orWhereNull('user_id');
                      })
                      ->latest()
                      ->paginate(10);
        $recipes   = Recipe::where('labor_cost_mode', 'external')->get();
        $templates = ExternalSupply::where('save_template', true)
                                   ->pluck('supply_name', 'id');

        return view('frontend.external-supplies.create', compact(
            'externalSupply', 'laborCost', 'clients', 'recipes', 'templates'
        ));
    }

    public function update(Request $request, ExternalSupply $externalSupply)
    {
        $messages = [
            'supply_name.required_if'     => 'Il nome della fornitura è obbligatorio quando si salva come modello.',
            'client_id.required'          => 'Devi selezionare un cliente.',
            'client_id.exists'            => 'Il cliente selezionato non esiste.',
            'supply_date.required'        => 'La data della fornitura è obbligatoria.',
            'supply_date.date'            => 'Inserisci una data valida.',
            'recipes.required'            => 'Devi aggiungere almeno una ricetta.',
            'recipes.*.id.required'       => 'Seleziona una ricetta per ogni riga.',
            'recipes.*.id.exists'         => 'La ricetta selezionata non esiste.',
            'recipes.*.price.required'    => 'Il prezzo è obbligatorio e deve essere un numero.',
            'recipes.*.qty.required'      => 'La quantità è obbligatoria e deve essere un numero intero.',
            'recipes.*.total_amount.required' => 'L\'importo totale è obbligatorio e deve essere un numero.',
        ];

        $data = $request->validate([
            'supply_name'            => 'required_if:template_action,template,both|max:255',
            'template_action'        => 'nullable|in:none,template,both',
            'client_id'              => 'required|exists:clients,id',
            'supply_date'            => 'required|date',
            'recipes'                => 'required|array|min:1',
            'recipes.*.id'           => 'required|exists:recipes,id',
            'recipes.*.price'        => 'required|numeric|min:0',
            'recipes.*.qty'          => 'required|integer|min:0',
            'recipes.*.total_amount' => 'required|numeric|min:0',
        ], $messages);

        $saveTemplate = in_array($data['template_action'], ['template','both']);
        $totalAmount  = array_sum(array_column($data['recipes'], 'total_amount'));

        $externalSupply->update([
            'client_id'     => $data['client_id'],
            'supply_name'   => $data['supply_name'],
            'supply_date'   => $data['supply_date'],
            'total_amount'  => $totalAmount,
            'save_template' => $saveTemplate,
        ]);

        $externalSupply->recipes()->delete();
        foreach ($data['recipes'] as $row) {
            $externalSupply->recipes()->create([
                'recipe_id'    => $row['id'],
                'category'     => $row['category'] ?? '',
                'price'        => $row['price'],
                'qty'          => $row['qty'],
                'total_amount' => $row['total_amount'],
                'user_id'      => auth()->id(),
            ]);
        }

        return redirect()
            ->route('external-supplies.index')
            ->with('success', 'Fornitura esterna aggiornata con successo!');
    }

    public function destroy(ExternalSupply $externalSupply)
    {
        $externalSupply->recipes()->delete();
        $externalSupply->delete();

        return redirect()
            ->route('external-supplies.index')
            ->with('success', 'Fornitura esterna eliminata con successo!');
    }
}
