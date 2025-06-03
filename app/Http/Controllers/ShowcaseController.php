<?php

namespace App\Http\Controllers;

use App\Models\Showcase;
use App\Models\ShowcaseRecipe;
use App\Models\Recipe;
use App\Models\LaborCost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowcaseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $groupRootId = $user->created_by ?? $user->id;

        $groupUserIds = User::where('created_by', $groupRootId)
            ->pluck('id')
            ->push($groupRootId);

        $showcases = Showcase::with([
            'recipes.recipe.ingredients.ingredient',
            'recipes.recipe'  // for price, sell_mode, recipe_weight, total_pieces
        ])
            ->whereIn('user_id', $groupUserIds)
            ->latest()
            ->get();



        return view('frontend.showcase.index', compact('showcases'));
    }




 public function create()
{
    $user        = Auth::user();
    $groupRootId = $user->created_by ?? $user->id;

    // get the group's labor cost record
    $laborCost     = LaborCost::where('user_id', $groupRootId)->first();
    $laborCostRate = $laborCost;

    // all user IDs in this group
    $groupUserIds = User::where('created_by', $groupRootId)
        ->pluck('id')
        ->push($groupRootId);

    // fetch valid recipes with ingredients
    $recipes = Recipe::with('ingredients')
        ->whereIn('user_id', $groupUserIds)
        ->where(function ($q) {
            $q->where(fn($q2) =>
                    $q2->where('sell_mode','kg')
                        ->where('selling_price_per_kg','>',0)
                )
                ->orWhere(fn($q2) =>
                    $q2->where('sell_mode','piece')
                        ->where('selling_price_per_piece','>',0)
                );
        })
        ->get();

    // compute each recipe's batch costs
    $recipes->each(function ($r) use ($laborCostRate) {
        $rate = $r->labor_cost_mode === 'external'
            ? ($laborCostRate->external_cost_per_min ?? 0)
            : ($laborCostRate->shop_cost_per_min     ?? 0);
        $r->batch_labor_cost = round($r->labour_time_min * $rate, 2);
        $r->batch_ing_cost   = $r->ingredients_cost_per_batch;
    });

    // load full Showcase models for "Choose Template"
  $templates = Showcase::where('save_template', true)
        ->whereIn('user_id', $groupUserIds)
        ->pluck('showcase_name', 'id');

    // flag for view
    $isEdit = false;

    return view('frontend.showcase.create', compact(
        'recipes',
        'laborCost',
        'laborCostRate',
        'templates',
        'isEdit'
    ));
}


    // public function create()
    // {
    //     $user = Auth::user();
    //     $groupRootId = $user->created_by ?? $user->id;

    //     $groupUserIds = User::where('created_by', $groupRootId)
    //         ->pluck('id')
    //         ->push($groupRootId);

    //     // $recipes    = Recipe::whereIn('user_id', $groupUserIds)->get();
    //     // $laborCost  = LaborCost::first();
    //     $recipes = Recipe::whereIn('user_id', $groupUserIds)
    //         ->where(function ($q) {
    //             $q->where(function ($q2) {
    //                 // if we sell by kg, price_per_kg must be > 0
    //                 $q2->where('sell_mode', 'kg')
    //                     ->where('selling_price_per_kg', '>', 0);
    //             })
    //                 ->orWhere(function ($q2) {
    //                     // if we sell by piece, price_per_piece must be > 0
    //                     $q2->where('sell_mode', 'piece')
    //                         ->where('selling_price_per_piece', '>', 0);
    //                 });
    //         })
    //         ->get();

    //     $groupRootId = $user->created_by ?? $user->id;
    //     $laborCost = \App\Models\LaborCost::where('user_id', $groupRootId)->first();

    //     $templates  = Showcase::where('save_template', true)
    //         ->whereIn('user_id', $groupUserIds)
    //         ->pluck('showcase_name', 'id');

    //     $isEdit = false;

    //     return view('frontend.showcase.create', compact(
    //         'recipes',
    //         'laborCost',
    //         'templates',
    //         'isEdit'
    //     ));
    // }

public function store(Request $request)
{
    // messaggi di validazione personalizzati
    $messages = [
        'showcase_name.required'              => 'Il nome della vetrina è obbligatorio.',
        'showcase_date.required'              => 'La data della vetrina è obbligatoria.',
        'template_action.required'            => 'Seleziona un\'azione valida per il salvataggio.',
        'items.required'                      => 'Devi aggiungere almeno un articolo alla vetrina.',
        'items.*.recipe_id.required'          => 'Seleziona una ricetta per ciascuna riga.',
        'items.*.price.required'              => 'Il prezzo è obbligatorio e deve essere un numero.',
        'items.*.quantity.required'           => 'La quantità è obbligatoria e deve essere un numero intero.',
        'items.*.sold.required'               => 'Il numero di venduti è obbligatorio e deve essere un numero intero.',
        'items.*.reuse.required'              => 'Il numero di riutilizzi è obbligatorio e deve essere un numero intero.',
        'items.*.waste.required'              => 'Il numero di scarti è obbligatorio e deve essere un numero intero.',
        'items.*.potential_income.required'   => 'Il potenziale è obbligatorio e deve essere un numero.',
        'items.*.actual_revenue.required'     => 'Il ricavo effettivo è obbligatorio e deve essere un numero.',
        'total_revenue.required'              => 'Il ricavo totale è obbligatorio e deve essere un numero.',
        'plus.required'                       => 'Il valore "Extra" è obbligatorio e deve essere un numero.',
        'real_margin.required'                => 'Il margine reale è obbligatorio e deve essere un numero.',
    ];

    // 1) validazione base
    $request->validate([
        'showcase_name'   => 'nullable|string|max:255',
        'showcase_date'   => 'required|date',
        'template_action' => 'required|in:none,template,both',
        'template_id'     => 'nullable|exists:showcases,id',
        'items'           => 'required|array|min:1',
        'items.*.recipe_id'        => 'required|exists:recipes,id',
        'items.*.price'            => 'required|numeric|min:0',
        'items.*.quantity'         => 'required|integer|min:0',
        'items.*.sold'             => 'required|integer|min:0',
        'items.*.reuse'            => 'required|integer|min:0',
        'items.*.waste'            => 'required|integer|min:0',
        'items.*.potential_income' => 'required|numeric|min:0',
        'items.*.actual_revenue'   => 'required|numeric|min:0',
        'total_revenue'            => 'required|numeric|min:0',
        'plus'                     => 'required|numeric',
        'real_margin'              => 'required|numeric',
    ], $messages);

    // se salvo come modello, il nome diventa obbligatorio
    if (in_array($request->template_action, ['template','both'])) {
        $request->validate([
            'showcase_name' => 'required|string|max:255',
        ], $messages);
    }

    $data       = $request->all();
    $userId     = Auth::id();
    $action     = $data['template_action'];
    $templateId = $data['template_id'] ?? null;

    // helper per sincronizzare le righe
    $syncLines = function(Showcase $sc) use($data, $userId){
        $sc->recipes()->delete();
        foreach($data['items'] as $row){
            ShowcaseRecipe::create([
                'showcase_id'      => $sc->id,
                'recipe_id'        => $row['recipe_id'],
                'price'            => $row['price'],
                'quantity'         => $row['quantity'],
                'sold'             => $row['sold'],
                'reuse'            => $row['reuse'],
                'waste'            => $row['waste'],
                'potential_income' => $row['potential_income'],
                'actual_revenue'   => $row['actual_revenue'],
                'user_id'          => $userId,
            ]);
        }
    };

    // ── Caso A: Salva come Modello → crea nuovo record template
    if ($action === 'template') {
        $newTemplate = Showcase::create([
            'showcase_name'   => $data['showcase_name'],
            'showcase_date'   => $data['showcase_date'],
            'break_even'      => $data['break_even'],
            'template_action' => $action,
            'save_template'   => true,
            'total_revenue'   => $data['total_revenue'],
            'plus'            => $data['plus'],
            'real_margin'     => $data['real_margin'],
            'user_id'         => $userId,
        ]);
        $syncLines($newTemplate);

        return redirect()
            ->route('showcase.create')
            ->with('success','Nuovo modello creato con successo.');
    }

    // ── Caso B: Salva e Modello → aggiorna template + crea nuova vetrina
    if ($action === 'both' && $templateId) {
        $tpl = Showcase::findOrFail($templateId);
        $tpl->update([
            'showcase_name'   => $data['showcase_name'],
            'showcase_date'   => $data['showcase_date'],
            'break_even'      => $data['break_even'],
            'template_action' => $action,
            'save_template'   => true,
            'total_revenue'   => $data['total_revenue'],
            'plus'            => $data['plus'],
            'real_margin'     => $data['real_margin'],
            'user_id'         => $userId,
        ]);
        $syncLines($tpl);
        // prosegue per creare nuova vetrina
    }

    // ── Caso C: Solo Salva o Salva & Modello
    $new = Showcase::create([
        'showcase_name'   => $data['showcase_name'],
        'showcase_date'   => $data['showcase_date'],
        'break_even'      => $data['break_even'],
        'template_action' => $action,
        'save_template'   => false,
        'total_revenue'   => $data['total_revenue'],
        'plus'            => $data['plus'],
        'real_margin'     => $data['real_margin'],
        'user_id'         => $userId,
    ]);
    $syncLines($new);

    return redirect()
        ->route('showcase.create')
        ->with('success','Vetrina salvata con successo.');
}




   public function update(Request $request, Showcase $showcase)
{
    // solo il proprietario può modificare
    abort_if($showcase->user_id !== Auth::id(), 403);

    // messaggi di validazione personalizzati
    $messages = [
        'showcase_date.required'           => 'La data della vetrina è obbligatoria.',
        'items.required'                   => 'Devi aggiungere almeno un articolo alla vetrina.',
        'items.*.recipe_id.required'       => 'Seleziona una ricetta per ciascuna riga.',
        'items.*.price.required'           => 'Il prezzo è obbligatorio e deve essere un numero.',
        'items.*.quantity.required'        => 'La quantità è obbligatoria e deve essere un numero intero.',
        'items.*.sold.required'            => 'Il numero dei venduti è obbligatorio e deve essere un numero intero.',
        'items.*.reuse.required'           => 'Il numero di riutilizzi è obbligatorio e deve essere un numero intero.',
        'items.*.waste.required'           => 'Il numero di scarti è obbligatorio e deve essere un numero intero.',
        'items.*.potential_income.required'=> 'Il potenziale è obbligatorio e deve essere un numero.',
        'items.*.actual_revenue.required'  => 'Il ricavo effettivo è obbligatorio e deve essere un numero.',
        'total_revenue.required'           => 'Il ricavo totale è obbligatorio e deve essere un numero.',
        'plus.required'                    => 'Il valore "Extra" è obbligatorio e deve essere un numero.',
        'real_margin.required'             => 'Il margine reale è obbligatorio e deve essere un numero.',
    ];

    // validazione principale
    $request->validate([
        'showcase_name'              => 'nullable|string|max:255',
        'showcase_date'              => 'required|date',
        'template_action'            => 'nullable|in:none,template,both',
        'items'                      => 'required|array|min:1',
        'items.*.recipe_id'          => 'required|exists:recipes,id',
        'items.*.price'              => 'required|numeric|min:0',
        'items.*.quantity'           => 'required|integer|min:0',
        'items.*.sold'               => 'required|integer|min:0',
        'items.*.reuse'              => 'required|integer|min:0',
        'items.*.waste'              => 'required|integer|min:0',
        'items.*.potential_income'   => 'required|numeric|min:0',
        'items.*.actual_revenue'     => 'required|numeric|min:0',
        'total_revenue'              => 'required|numeric|min:0',
        'plus'                       => 'required|numeric',
        'real_margin'                => 'required|numeric',
    ], $messages);

    // se salvo come modello, il nome diventa obbligatorio
    if (in_array($request->template_action, ['template', 'both'])) {
        $request->validate([
            'showcase_name' => 'required|string|max:255',
        ], [
            'showcase_name.required' => 'Il nome della vetrina è obbligatorio quando si salva come modello.',
        ]);
    }

    $data    = $request->all();
    $userId  = Auth::id();
    $saveTpl = in_array($data['template_action'], ['template', 'both']);

    // aggiorna la vetrina
    $showcase->update([
        'showcase_name'            => $data['showcase_name']             ?? null,
        'showcase_date'            => $data['showcase_date'],
        'template_action'          => $data['template_action'],
        'save_template'            => $saveTpl,
        'break_even'               => $data['break_even'],
        'total_revenue'            => $data['total_revenue'],
        'plus'                     => $data['plus'],
        'potential_income_average' => 0,
        'real_margin'              => $data['real_margin'],
        'user_id'                  => $userId,
    ]);

    // rimuovi e ricrea tutte le righe
    $showcase->recipes()->delete();
    foreach ($data['items'] as $item) {
        ShowcaseRecipe::create([
            'showcase_id'      => $showcase->id,
            'recipe_id'        => $item['recipe_id'],
            'price'            => $item['price'],
            'quantity'         => $item['quantity'],
            'sold'             => $item['sold'],
            'reuse'            => $item['reuse'],
            'waste'            => $item['waste'],
            'potential_income' => $item['potential_income'],
            'actual_revenue'   => $item['actual_revenue'],
            'user_id'          => $userId,
        ]);
    }

    return redirect()
        ->route('showcase.index')
        ->with('success', 'Vetrina aggiornata con successo.');
}



   public function getTemplate($id)
{
    $template = Showcase::with('recipes')
        ->where('id', $id)
        ->where('save_template', true)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    // map child rows
    $details = $template->recipes->map(fn($r) => [
        'recipe_id'        => $r->recipe_id,
        'price'            => $r->price,
        'quantity'         => $r->quantity,
        'sold'             => $r->sold,
        'reuse'            => $r->reuse,
        'waste'            => $r->waste,
        'potential_income' => $r->potential_income,
        'actual_revenue'   => $r->actual_revenue,
    ]);

    return response()->json([
        'showcase_name'    => $template->showcase_name,
        'showcase_date'    => $template->showcase_date->format('Y-m-d'),
        'break_even'       => $template->break_even,
        'template_action'  => $template->template_action ?? 'none', // ✅ this fixes the missing field
        'details'          => $details,
    ]);
}


  public function edit(Showcase $showcase)
{
    abort_if($showcase->user_id !== Auth::id(), 403);

    $userId    = Auth::id();
    $recipes   = Recipe::where('user_id', $userId)->get();
    $laborCost = LaborCost::first();

    // ← define $templates just like in create()
    $templates = Showcase::where('save_template', true)
        ->where('user_id', $userId)
        ->get();

    $isEdit = true;
    $showcase->load('recipes');

    return view('frontend.showcase.create', compact(
        'showcase',
        'recipes',
        'laborCost',
        'templates',   // ← make sure this is here
        'isEdit'
    ));
}



    public function destroy(Showcase $showcase)
    {
        abort_if($showcase->user_id !== Auth::id(), 403);

        $showcase->recipes()->delete();
        $showcase->delete();

        return redirect()
            ->route('showcase.index')
            ->with('success', 'Vetrina eliminata con successo.');
    }

// in app/Http/Controllers/YourShowcaseController.php

// app/Http/Controllers/ShowcaseController.php

public function show(Showcase $showcase)
{
    // we need department on each ShowcaseRecipe, in addition to recipe & user
    $showcase->load([
        'recipes.recipe',
              'recipes.recipe.department', 

        'user'
    ]);

    return view('frontend.showcase.show', compact('showcase'));
}


}