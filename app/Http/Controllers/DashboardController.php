<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cost;
use App\Models\User;
use App\Models\Income;
use App\Models\Recipe;
use App\Models\Showcase;
use App\Models\ShowcaseRecipe;
use App\Models\ProductionDetail;
use App\Models\ReturnedGoodRecipe;
use App\Models\ExternalSupplyRecipe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Imposto la localizzazione italiana per tutte le date
        Carbon::setLocale('it');

        $user = Auth::user();

        // 1) Determine visible users
        if (is_null($user->created_by)) {
            $children = User::where('created_by', $user->id)->pluck('id');
            $visibleUserIds = $children->isEmpty()
                ? collect([$user->id])
                : $children->push($user->id);
        } else {
            $visibleUserIds = collect([$user->id, $user->created_by]);
        }

        // 2) Core metrics
        $totalUsers          = User::whereIn('id', $visibleUserIds)->count();
        $totalRecipes        = Recipe::whereIn('user_id', $visibleUserIds)->count();
        $totalShowcases      = Showcase::whereIn('user_id', $visibleUserIds)->count();

        $year                = Carbon::now()->year;
        $totalSaleThisYear   = Showcase::whereIn('user_id', $visibleUserIds)
            ->whereYear('showcase_date', $year)
            ->sum('total_revenue');

        $totalWasteThisYear  = ShowcaseRecipe::whereIn('user_id', $visibleUserIds)
            ->whereHas('showcase', fn($q) => $q->whereYear('showcase_date', $year))
            ->sum('waste');

        $totalProfitThisYear = Showcase::whereIn('user_id', $visibleUserIds)
            ->whereYear('showcase_date', $year)
            ->sum('real_margin');

        // 2) Earnings metrics
        $sales      = Showcase::whereIn('user_id', $visibleUserIds)->sum('total_revenue');
        $plus       = Showcase::whereIn('user_id', $visibleUserIds)->sum('plus');
        $realMargin = Showcase::whereIn('user_id', $visibleUserIds)->sum('real_margin');

        // 3) Vendite Mensili (anno corrente)
        $monthlyData = Showcase::selectRaw("MONTH(showcase_date) AS month, SUM(total_revenue) AS total")
            ->whereIn('user_id', $visibleUserIds)
            ->whereYear('showcase_date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = $monthlyData->pluck('month')
            ->map(fn($m) =>
                Carbon::createFromDate(null, $m, 1)
                      ->locale('it')
                      ->isoFormat('MMM')
            )->toArray();
        $values = $monthlyData->pluck('total')->toArray();

        $chart = (new LarapexChart)->barChart()
            ->setTitle('Vendite Mensili')
            ->setSubtitle('Anno Corrente')
            ->addData('Ricavi', $values)
            ->setXAxis($labels);

        // 4) Costi e Ricavi (mese corrente)
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();
        $monthlyCost   = Cost::whereIn('user_id', $visibleUserIds)
            ->whereBetween('due_date', [$start, $end])->sum('amount');
        $monthlyIncome = Income::whereIn('user_id', $visibleUserIds)
            ->whereBetween('date', [$start, $end])->sum('amount');

        $comparisonChart = (new LarapexChart)->barChart()
            ->setTitle('Costi e Ricavi')
            ->setSubtitle(Carbon::now()->locale('it')->isoFormat('MMMM YYYY'))
            ->addData('Costi', [$monthlyCost])
            ->addData('Ricavi', [$monthlyIncome])
            ->setXAxis([Carbon::now()->locale('it')->isoFormat('MMMM')]);

        // 5) Confronto Costi Annuali
        $thisYear       = Carbon::now()->year;
        $lastYear       = Carbon::now()->subYear()->year;
        $yearlyCostThis = Cost::whereIn('user_id', $visibleUserIds)
            ->whereYear('due_date', $thisYear)->sum('amount');
        $yearlyCostLast = Cost::whereIn('user_id', $visibleUserIds)
            ->whereYear('due_date', $lastYear)->sum('amount');

        $yearlyCostChart = (new LarapexChart)->barChart()
            ->setTitle('Costi Annuali')
            ->setSubtitle("$lastYear vs $thisYear")
            ->addData('Costi', [$yearlyCostLast, $yearlyCostThis])
            ->setXAxis([$lastYear, $thisYear]);

        // 6) Confronto Ricavi Annuali
        $yearlyIncomeThis = Income::whereIn('user_id', $visibleUserIds)
            ->whereYear('date', $thisYear)->sum('amount');
        $yearlyIncomeLast = Income::whereIn('user_id', $visibleUserIds)
            ->whereYear('date', $lastYear)->sum('amount');

        $yearlyIncomeChart = (new LarapexChart)->barChart()
            ->setTitle('Ricavi Annuali')
            ->setSubtitle("$lastYear vs $thisYear")
            ->addData('Ricavi', [$yearlyIncomeLast, $yearlyIncomeThis])
            ->setXAxis([$lastYear, $thisYear]);

        // 7) Top 5 Prodotti Venduti & Sprechi
        $topSold = ShowcaseRecipe::with(['recipe' => function ($query) {
                $query->select('id', 'recipe_name');
            }])
            ->whereIn('user_id', $visibleUserIds)
            ->selectRaw('recipe_id, SUM(sold) as sold')
            ->groupBy('recipe_id')
            ->orderByDesc('sold')
            ->take(5)
            ->get();

        $topWasted = ShowcaseRecipe::with(['recipe' => function ($query) {
                $query->select('id', 'recipe_name');
            }])
            ->whereIn('user_id', $visibleUserIds)
            ->selectRaw('recipe_id, SUM(waste) as waste')
            ->groupBy('recipe_id')
            ->orderByDesc('waste')
            ->take(5)
            ->get();

        $soldValues = $topSold->pluck('sold')->map(fn($v) => (int) $v)->toArray();
        $soldLabels = $topSold->map(fn($item) => $item->recipe->recipe_name ?? 'Sconosciuto')->toArray();

        $soldPieChart = (new LarapexChart)->donutChart()
            ->setTitle('Distribuzione Vendite')
            ->addData($soldValues)
            ->setLabels($soldLabels);

        $wastedLabels = $topWasted->map(fn($item) => $item->recipe->recipe_name ?? 'Sconosciuto')->toArray();
        $wastedValues = $topWasted->pluck('waste')->toArray();

        $wastedPieChart = (new LarapexChart)->donutChart()
            ->setTitle('Distribuzione Sprechi')
            ->addData($wastedValues)
            ->setLabels($wastedLabels);

        // 8) Incidenza Costi vs Ricavi
        $totalCost   = $monthlyCost;
        $totalRev    = $monthlyIncome;
        $nettoAmount = $totalRev - $totalCost;

        $incidenceChart = (new LarapexChart)->pieChart()
            ->setTitle('Incidenza Costi vs Ricavi')
            ->addData([$totalCost, $totalRev, $nettoAmount])
            ->setLabels(['Costi', 'Ricavi', 'Netto']);

        // 9) Produzione per Pasticcere
        $prodByChef = ProductionDetail::selectRaw('pastry_chef_id, SUM(quantity) AS qty')
            ->whereIn('user_id', $visibleUserIds)
            ->groupBy('pastry_chef_id')
            ->with('chef:id,name')
            ->orderByDesc('qty')
            ->get();

        $chefLabels = $prodByChef->pluck('chef.name')->toArray();
        $chefValues = $prodByChef->pluck('qty')->toArray();

        $chefChart = (new LarapexChart)->barChart()
            ->setTitle('Produzione per Pasticcere')
            ->addData('Unità', $chefValues)
            ->setXAxis($chefLabels);

        // 10) Andamento Produzione vs Spreco (annuale)
        $prodTrend  = ProductionDetail::selectRaw('MONTH(production_date) AS m, SUM(quantity) AS produced')
            ->join('productions','productions.id','production_details.production_id')
            ->whereIn('production_details.user_id',$visibleUserIds)
            ->whereYear('productions.production_date',$year)
            ->groupBy('m')->orderBy('m')->get();

        $wasteTrend = ShowcaseRecipe::selectRaw('MONTH(showcase_date) AS m, SUM(waste) AS waste')
            ->join('showcases','showcases.id','showcase_recipes.showcase_id')
            ->whereIn('showcase_recipes.user_id',$visibleUserIds)
            ->whereYear('showcases.showcase_date',$year)
            ->groupBy('m')->orderBy('m')->get();

        $labelsTrend = $prodTrend->pluck('m')
            ->map(fn($m) =>
                Carbon::createFromDate(null, $m, 1)
                      ->locale('it')
                      ->isoFormat('MMM')
            )->toArray();

        $prodVals  = $prodTrend->pluck('produced')->toArray();
        $wasteVals = $wasteTrend->pluck('waste')->toArray();

        $prodWasteChart = (new LarapexChart)->areaChart()
            ->setTitle('Produzione vs Spreco')
            ->addData('Prodotto', $prodVals)
            ->addData('Spreco',   $wasteVals)
            ->setXAxis($labelsTrend);

        // 11) Ripartizione dei costi per categoria
        $costByCategory = Cost::join('cost_categories', 'costs.category_id', '=', 'cost_categories.id')
            ->whereIn('costs.user_id', $visibleUserIds)
            ->groupBy('cost_categories.id', 'cost_categories.name')
            ->select(
                'cost_categories.name as category',
                DB::raw('SUM(costs.amount) as total')
            )
            ->get();

        $categoryLabels = $costByCategory->pluck('category')->toArray();
        $categoryValues = $costByCategory->pluck('total')->map(fn($v) => (float) $v)->toArray();

        $costCategoryChart = (new LarapexChart)->donutChart()
            ->setTitle('Ripartizione dei costi per categoria')
            ->addData($categoryValues)
            ->setLabels($categoryLabels);

        // 12) Resi vs. quantità fornita
        $totalSupplied = ExternalSupplyRecipe::whereIn('user_id', $visibleUserIds)
            ->sum('qty');
        $totalReturned = ReturnedGoodRecipe::join('returned_goods','returned_goods.id','returned_good_recipes.returned_good_id')
            ->whereIn('returned_goods.user_id', $visibleUserIds)
            ->sum('qty');

        $returnRateChart = (new LarapexChart)->pieChart()
            ->setTitle('Resi vs. Utilizzo')
            ->addData([
                $totalReturned,
                $totalSupplied - $totalReturned
            ])
            ->setLabels(['Resi', 'Utilizzati']);

        // 13) Full datasets for JS filtering
        $fullMonthlyData = $monthlyData->map(fn($row) => [
            'date'  => Carbon::createFromDate(null, $row->month, 1)->format('Y-m-d'),
            'total' => $row->total,
        ])->toArray();

        $fullSoldData = ShowcaseRecipe::with(['recipe','showcase'])
            ->whereIn('user_id', $visibleUserIds)
            ->get()
            ->map(fn($r) => [
                'recipe_name' => $r->recipe->recipe_name,
                'sold'        => $r->sold,
                'date'        => $r->showcase->showcase_date->format('Y-m-d'),
            ])->toArray();

        $fullWastedData = ShowcaseRecipe::with(['recipe','showcase'])
            ->whereIn('user_id', $visibleUserIds)
            ->get()
            ->map(fn($r) => [
                'recipe_name' => $r->recipe->recipe_name,
                'waste'       => $r->waste,
                'date'        => $r->showcase->showcase_date->format('Y-m-d'),
            ])->toArray();

        $fullCostData = Cost::with('category')
            ->whereIn('user_id', $visibleUserIds)
            ->get()
            ->map(fn($c) => [
                'date'     => $c->due_date->format('Y-m-d'),
                'amount'   => (float) $c->amount,
                'category' => $c->category->name,
            ])->toArray();

        $fullIncomeData = Income::whereIn('user_id', $visibleUserIds)
            ->get()
            ->map(fn($i) => [
                'date'   => $i->date->format('Y-m-d'),
                'amount' => (float) $i->amount,
            ])->toArray();

        // 14) Render
        return view('dashboard', compact(
            'sales','plus','realMargin',
            'chart','comparisonChart','yearlyCostChart','yearlyIncomeChart',
            'topSold','topWasted',
            'soldPieChart','wastedPieChart','incidenceChart',
            'totalUsers','totalRecipes','totalShowcases',
            'totalSaleThisYear','totalWasteThisYear','totalProfitThisYear',
            'year','totalSupplied','totalReturned','returnRateChart',
            'chefChart','prodWasteChart','costCategoryChart',
            'fullMonthlyData','fullSoldData','fullWastedData',
            'fullCostData','fullIncomeData'
        ));
    }
}
