@extends('frontend.layouts.app')

@section('title', 'Cruscotto')

 @section('content')







     <div class="dashboard-main-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <h6 class="fw-semibold mb-0">Cruscotto</h6>
    <ul class="d-flex align-items-center gap-2">
        <li class="fw-medium">
            <a href="index.html" class="d-flex align-items-center gap-1 hover-text-primary">
                <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                Home
            </a>
        </li>
        <li>-</li>
        <li class="fw-medium">CRM</li>
    </ul>
</div>


         <div class="row gy-4">


















             {{-- resources/views/dashboard.blade.php --}}
             <div class="col-xxl-12">
  <div class="row gy-4">

    {{-- Utenti Totali --}}
    <div class="col-xxl-4 col-sm-6">
      <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-1">
        <div class="card-body p-0">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
            <div class="d-flex align-items-center gap-2">
              <span
                class="mb-0 w-48-px h-48-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6">
                <iconify-icon icon="mingcute:user-follow-fill"></iconify-icon>
              </span>
              <div>
                <span class="mb-2 fw-medium text-secondary-light text-sm">Utenti Totali</span>
                <h6 class="fw-semibold">{{ number_format($totalUsers) }}</h6>
              </div>
            </div>
            <div id="total-users-chart"></div>
          </div>
          <p class="text-sm mb-0">Da creazione del gruppo</p>
        </div>
      </div>
    </div>

    {{-- Ricette Totali --}}
    <div class="col-xxl-4 col-sm-6">
      <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-2">
        <div class="card-body p-0">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
            <div class="d-flex align-items-center gap-2">
              <span
                class="mb-0 w-48-px h-48-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center rounded-circle h6">
                <iconify-icon icon="uis:box" class="icon"></iconify-icon>
              </span>
              <div>
                <span class="mb-2 fw-medium text-secondary-light text-sm">Ricette Totali</span>
                <h6 class="fw-semibold">{{ number_format($totalRecipes) }}</h6>
              </div>
            </div>
            <div id="total-recipes-chart"></div>
          </div>
          <p class="text-sm mb-0">Tra tutti gli utenti del gruppo</p>
        </div>
      </div>
    </div>

    {{-- Vetrine Totali --}}
    <div class="col-xxl-4 col-sm-6">
      <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-3">
        <div class="card-body p-0">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
            <div class="d-flex align-items-center gap-2">
              <span
                class="mb-0 w-48-px h-48-px bg-yellow text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                <iconify-icon icon="mdi:television-ambient-light" class="icon"></iconify-icon>
              </span>
              <div>
                <span class="mb-2 fw-medium text-secondary-light text-sm">Vetrine Totali</span>
                <h6 class="fw-semibold">{{ number_format($totalShowcases) }}</h6>
              </div>
            </div>
            <div id="total-showcase-chart"></div>
          </div>
          <p class="text-sm mb-0">Conteggio totale</p>
        </div>
      </div>
    </div>

    @can('Dashboard(Sales, Costs)')
      {{-- Vendite (Anno) --}}
      <div class="col-xxl-4 col-sm-6">
        <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-4">
          <div class="card-body p-0">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
              <div class="d-flex align-items-center gap-2">
                <span
                  class="mb-0 w-48-px h-48-px bg-purple text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                  <iconify-icon icon="iconamoon:discount-fill"></iconify-icon>
                </span>
                <div>
                  <span class="mb-2 fw-medium text-secondary-light text-sm">Vendite ({{ $year }})</span>
                  <h6 class="fw-semibold">${{ number_format($totalSaleThisYear, 2) }}</h6>
                </div>
              </div>
              <div id="total-sales-chart"></div>
            </div>
            <p class="text-sm mb-0">Anno in corso</p>
          </div>
        </div>
      </div>
    @endcan

    {{-- Sprechi (Anno) --}}
    <div class="col-xxl-4 col-sm-6">
      <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-5">
        <div class="card-body p-0">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
            <div class="d-flex align-items-center gap-2">
              <span
                class="mb-0 w-48-px h-48-px bg-pink text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                <iconify-icon icon="fluent:trash-24-regular" class="icon"></iconify-icon>
              </span>
              <div>
                <span class="mb-2 fw-medium text-secondary-light text-sm">Sprechi ({{ $year }})</span>
                <h6 class="fw-semibold">{{ number_format($totalWasteThisYear) }}</h6>
              </div>
            </div>
            <div id="total-waste-chart"></div>
          </div>
          <p class="text-sm mb-0">Quantità anno in corso</p>
        </div>
      </div>
    </div>

    @can('Dashboard(Sales, Costs)')
      {{-- Profitto (Anno) --}}
      <div class="col-xxl-4 col-sm-6">
        <div class="card p-3 shadow-2 radius-8 border input-form-light h-100 bg-gradient-end-6">
          <div class="card-body p-0">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
              <div class="d-flex align-items-center gap-2">
                <span
                  class="mb-0 w-48-px h-48-px bg-cyan text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6">
                  <iconify-icon icon="streamline:bag-dollar-solid" class="icon"></iconify-icon>
                </span>
                <div>
                  <span class="mb-2 fw-medium text-secondary-light text-sm">Profitto ({{ $year }})</span>
                  <h6 class="fw-semibold">${{ number_format($totalProfitThisYear, 2) }}</h6>
                </div>
              </div>
              <div id="total-profit-chart"></div>
            </div>
            <p class="text-sm mb-0">Margine anno in corso</p>
          </div>
        </div>
      </div>
    @endcan

  </div>
</div>

































            @can('Dashboard(Sales, Costs)')
    <div class="col-xxl-12">
        <div class="card h-100 radius-8 border-0">
            <div class="card-body p-24">

                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                    <div>
                        <h6 class="mb-2 fw-bold text-lg">Statistiche Guadagni</h6>
                        <span class="text-sm fw-medium text-secondary-light">Panoramica vendite mensili</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <input type="date" id="startDate" class="form-control form-control-sm" />
                        <input type="date" id="endDate" class="form-control form-control-sm" />
                        <button id="applyDateFilter" class="btn btn-sm btn-primary">Applica</button>
                    </div>
                </div>

                <div class="mt-20 d-flex justify-content-center flex-wrap gap-3">
                    <div
                        class="d-inline-flex align-items-center gap-2 p-2 radius-8 border pe-36 br-hover-primary group-item">
                        <span
                            class="bg-neutral-100 w-44-px h-44-px text-xxl radius-8 d-flex justify-content-center align-items-center text-secondary-light group-hover:bg-primary-600 group-hover:text-white">
                            <iconify-icon icon="fluent:cart-16-filled" class="icon"></iconify-icon>
                        </span>
                        <div>
                            <span class="text-secondary-light text-sm fw-medium">Vendite</span>
                            <h6 class="text-md fw-semibold mb-0">${{ number_format($sales, 2) }}</h6>
                        </div>
                    </div>

                    <div
                        class="d-inline-flex align-items-center gap-2 p-2 radius-8 border pe-36 br-hover-primary group-item">
                        <span
                            class="bg-neutral-100 w-44-px h-44-px text-xxl radius-8 d-flex justify-content-center align-items-center text-secondary-light group-hover:bg-primary-600 group-hover:text-white">
                            <iconify-icon icon="uis:chart" class="icon"></iconify-icon>
                        </span>
                        <div>
                            <span class="text-secondary-light text-sm fw-medium">Margine Lordo</span>
                            <h6 class="text-md fw-semibold mb-0">${{ number_format($plus, 2) }}</h6>
                        </div>
                    </div>

                    <div
                        class="d-inline-flex align-items-center gap-2 p-2 radius-8 border pe-36 br-hover-primary group-item">
                        <span
                            class="bg-neutral-100 w-44-px h-44-px text-xxl radius-8 d-flex justify-content-center align-items-center text-secondary-light group-hover:bg-primary-600 group-hover:text-white">
                            <iconify-icon icon="ph:arrow-fat-up-fill" class="icon"></iconify-icon>
                        </span>
                        <div>
                            <span class="text-secondary-light text-sm fw-medium">Profitto Netto</span>
                            <h6 class="text-md fw-semibold mb-0">${{ number_format($realMargin, 2) }}</h6>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div id="barChart"></div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-xxl-4">
        <div class="card h-100 radius-8 border-0">
            <div class="card-body p-24">
                {!! $comparisonChart->container() !!}
            </div>
        </div>
    </div>

    <div class="col-xxl-4 mb-4">
        <div class="card h-100 radius-8 border-0">
            <div class="card-body p-24">
                {!! $yearlyCostChart->container() !!}
            </div>
        </div>
    </div>

    <div class="col-xxl-4 mb-4">
        <div class="card h-100 radius-8 border-0">
            <div class="card-body p-24">
                {!! $yearlyIncomeChart->container() !!}
            </div>
        </div>
    </div>
@endcan

















<div class="col-xxl-6 mb-4">
    <div class="card h-100">
        <div
            class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
            <h6 class="text-lg fw-semibold mb-0">Top 5 Prodotti Venduti</h6>
            <div class="d-flex align-items-center gap-2">
                <input type="date" id="soldStart" class="form-control form-control-sm" />
                <input type="date" id="soldEnd" class="form-control form-control-sm" />
                <button id="soldFilter" class="btn btn-sm btn-primary">Applica</button>
            </div>
        </div>
        <div class="card-body p-24">
            <div class="table-responsive scroll-sm mb-4">
                <table class="table bordered-table mb-0" id="soldTable">
                    <thead>
                        <tr>
                            <th>Prodotto</th>
                            <th class="text-end">Quantità Venduta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topSold as $item)
                            <tr>
                                <td>{{ $item->recipe->recipe_name }}</td>
                                <td class="text-end">{{ $item->sold }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="soldPie"></div>
        </div>
    </div>
</div>


             {{-- Top 5 Wasted Products --}}
             <div class="col-xxl-6 mb-4">
    <div class="card h-100">
        <div
            class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
            <h6 class="text-lg fw-semibold mb-0">Top 5 Prodotti Sprecati</h6>
            <div class="d-flex align-items-center gap-2">
                <input type="date" id="wastedStart" class="form-control form-control-sm" />
                <input type="date" id="wastedEnd" class="form-control form-control-sm" />
                <button id="wastedFilter" class="btn btn-sm btn-primary">Applica</button>
            </div>
        </div>
        <div class="card-body p-24">
            <div class="table-responsive scroll-sm mb-4">
                <table class="table bordered-table mb-0" id="wastedTable">
                    <thead>
                        <tr>
                            <th>Prodotto</th>
                            <th class="text-end">Quantità Sprecata</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topWasted as $item)
                            <tr>
                                <td>{{ $item->recipe->recipe_name }}</td>
                                <td class="text-end">{{ $item->waste }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="wastedPie"></div>
        </div>
    </div>
</div>

         </div>


































         {{-- resources/views/dashboard.blade.php --}}
         <div class="row gy-4 mb-4">
             {{-- Return vs Used --}}
            <div class="col-xxl-4 col-sm-6">
    <div class="card h-100 radius-8 border-0">
        <div class="card-body p-24">
            <h6 class="text-lg fw-semibold mb-3">Resi vs Riforniti</h6>
            <p>Totale Fornito: {{ number_format($totalSupplied) }}</p>
            <p>Totale Resi: {{ number_format($totalReturned) }}</p>
            {!! $returnRateChart->container() !!}
        </div>
    </div>
</div>


             {{-- Production by Chef --}}
             <div class="col-xxl-4 col-sm-6">
    <div class="card h-100 radius-8 border-0">
        <div class="card-body p-24">
            <h6 class="text-lg fw-semibold mb-3">Produzione per Pasticcere</h6>
            {!! $chefChart->container() !!}
        </div>
    </div>
</div>

<div class="col-xxl-4 col-sm-6">
    <div class="card h-100 radius-8 border-0">
        <div class="card-body p-24">
            <h6 class="text-lg fw-semibold mb-3">Costi per Categoria</h6>
            {!! $costCategoryChart->container() !!}
        </div>
    </div>
</div>











@can('Dashboard(Sales, Costs)')
<div class="col-xxl-12 mb-4">
  <div class="card h-100">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
      <h6 class="text-lg fw-semibold mb-0">Incidenza Costi vs Ricavi</h6>
      <div class="d-flex align-items-center gap-2">
        <input type="date" id="incStart" class="form-control form-control-sm" />
        <input type="date" id="incEnd"   class="form-control form-control-sm" />
        <button id="incFilter" class="btn btn-sm btn-primary">Applica</button>
      </div>
    </div>
    <div class="card-body p-24">
      <div id="incomeCostDonut"></div>
    </div>
  </div>
</div>
@endcan



























             {{-- Production vs Waste Trend --}}
             <div class="col-xxl-12">
  <div class="card h-100 radius-8 border-0">
    <div class="card-body p-24">
      <h6 class="text-lg fw-semibold mb-3">Produzione vs Spreco</h6>
      {!! $prodWasteChart->container() !!}
    </div>
  </div>
</div>

         </div>



     </div>
     </div>


 @endsection

@section('scripts')
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

  {{-- LarapexChart scripts (if you’re still using them elsewhere) --}}
  {!! $chart->script() !!}
  {!! $comparisonChart->script() !!}
  {!! $yearlyCostChart->script() !!}
  {!! $yearlyIncomeChart->script() !!}
  {!! $soldPieChart->script() !!}
  {!! $wastedPieChart->script() !!}
  {!! $returnRateChart->script() !!}
  {!! $chefChart->script() !!}
  {!! $prodWasteChart->script() !!}
  {!! $costCategoryChart->script() !!}

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    //
    // 1) BAR CHART: Monthly Sales with client-side date filter
    //
    const fullData = @json($fullMonthlyData);
    const barChart = new ApexCharts(
      document.querySelector('#barChart'),
      {
        series: [{ name: 'Guadagni', data: fullData.map(i => i.total) }],
        chart: { type: 'bar', height: 350 },
        xaxis: {
          categories: fullData.map(i =>
            new Date(i.date).toLocaleString('default',{ month:'short' })
          )
        }
      }
    );
    barChart.render();

    document.getElementById('applyDateFilter')
      .addEventListener('click', () => {
        const s = document.getElementById('startDate').value;
        const e = document.getElementById('endDate').value;
        if (!s || !e) return;
        const fd = fullData.filter(i => i.date >= s && i.date <= e);
        barChart.updateOptions({
          xaxis: { categories: fd.map(i =>
            new Date(i.date).toLocaleString('default',{ month:'short' })
          )}
        });
        barChart.updateSeries([{
          name: 'Guadagni',
          data: fd.map(i => i.total)
        }]);
      });
  });
</script>


<script>
  document.addEventListener('DOMContentLoaded', function() {
    //
    // 2) TOP-5 SOLD & WASTED DONUTS + FILTER
    //
    const fullSold   = @json($fullSoldData);
    const fullWasted = @json($fullWastedData);

    function aggregateTop(data, key) {
      const agg = {};
      data.forEach(i => agg[i.recipe_name] = (agg[i.recipe_name]||0) + i[key]);
      return Object.entries(agg)
        .map(([name,val])=>({name,val}))
        .sort((a,b)=>b.val - a.val)
        .slice(0,5);
    }

    // init Sold donut
    const soldTop   = aggregateTop(fullSold, 'sold');
    const soldChart = new ApexCharts(
      document.querySelector('#soldPie'),
      { chart:{type:'donut',height:250},
        series: soldTop.map(i=>i.val),
        labels: soldTop.map(i=>i.name)
      }
    );
    soldChart.render();

    // init Wasted donut
    const wastedTop   = aggregateTop(fullWasted, 'waste');
    const wastedChart = new ApexCharts(
      document.querySelector('#wastedPie'),
      { chart:{type:'donut',height:250},
        series: wastedTop.map(i=>i.val),
        labels: wastedTop.map(i=>i.name)
      }
    );
    wastedChart.render();

    // bind Sold filter
    document.getElementById('soldFilter').addEventListener('click', () => {
      const s = document.getElementById('soldStart').value;
      const e = document.getElementById('soldEnd').value;
      if (!s||!e) return;
      const fd = fullSold.filter(i=>i.date>=s&&i.date<=e);
      const top = aggregateTop(fd,'sold');
      document.querySelector('#soldTable tbody').innerHTML =
        top.map(i=>`<tr><td>${i.name}</td><td class="text-end">${i.val}</td></tr>`).join('');
      soldChart.updateOptions({ labels: top.map(i=>i.name) });
      soldChart.updateSeries(top.map(i=>i.val));
    });

    // bind Wasted filter
    document.getElementById('wastedFilter').addEventListener('click', () => {
      const s = document.getElementById('wastedStart').value;
      const e = document.getElementById('wastedEnd').value;
      if (!s||!e) return;
      const fd  = fullWasted.filter(i=>i.date>=s&&i.date<=e);
      const top = aggregateTop(fd,'waste');
      document.querySelector('#wastedTable tbody').innerHTML =
        top.map(i=>`<tr><td>${i.name}</td><td class="text-end">${i.val}</td></tr>`).join('');
      wastedChart.updateOptions({ labels: top.map(i=>i.name) });
      wastedChart.updateSeries(top.map(i=>i.val));
    });
  });
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
  const costs   = @json($fullCostData);
  const incomes = @json($fullIncomeData);
  const CATS = ['Materie prime','Stipendi + TFR','Affitto','Energia elettrica','Altri costi'];

  function calcIncidence(s,e) {
    const fC = costs.filter(x=>x.date>=s&&x.date<=e),
          fI = incomes.filter(x=>x.date>=s&&x.date<=e);
    const totalInc = fI.reduce((sum,i)=>sum+i.amount,0);
    const sums = Object.fromEntries(CATS.map(c=>[c,0]));
    fC.forEach(c=>{
      if (c.category==='Materie prime') sums['Materie prime'] += c.amount;
      else if (['Stipendi','TFR'].includes(c.category)) sums['Stipendi + TFR'] += c.amount;
      else if (c.category==='Affitto') sums['Affitto'] += c.amount;
      else if (c.category==='Energia elettrica') sums['Energia elettrica'] += c.amount;
      else sums['Altri costi'] += c.amount;
    });
    const sumCosts = Object.values(sums).reduce((a,b)=>a+b,0),
          net = totalInc - sumCosts;
    return {
      totalInc,
      series: [...CATS.map(cat=>sums[cat]), net],
      labels: [...CATS,'Net']
    };
  }

  // default full-range
  const dates = incomes.map(i=>i.date).sort();
  const d0 = dates[0]||new Date().toISOString().slice(0,10),
        d1 = dates.at(-1)||d0;
  document.getElementById('incStart').value = d0;
  document.getElementById('incEnd').value   = d1;

  const init = calcIncidence(d0,d1);
  const incChart = new ApexCharts(
    document.querySelector('#incomeCostDonut'),
    {
      chart: { type:'donut', height:300 },
      series: init.series,
      labels: init.labels,
      colors: [
        '#008FFB', // Materie prime
        '#00E396', // Stipendi + TFR
        '#FEB019', // Affitto
        '#FF4560', // Energia elettrica
        '#775DD0', // Altri costi
        '#e2ae76'  // Net
      ],
      plotOptions:{
        pie:{
          donut:{
            labels:{
              show: true,
              name: { show:false },
              value:{
                show:true,
                formatter: () => init.totalInc.toLocaleString() + '€',
                offsetY: 8,
                style:{ fontSize:'20px', fontWeight:'700', color:'#000' }
              },
              total:{
                show:true,
                label:'Total Income',
                formatter: () => init.totalInc.toLocaleString() + '€',
                style:{ fontSize:'14px', fontWeight:'600', color:'#000' }
              }
            }
          }
        }
      },
      dataLabels:{
        formatter:(pct,o)=> {
          const v = o.w.config.series[o.seriesIndex];
          return `${v.toLocaleString()}€ (${pct.toFixed(1)}%)`;
        },
        style:{ colors:['#000'] }
      },
      legend:{ labels:{ colors:'#000' } },
      tooltip:{ y:{ formatter: v=>`${v.toLocaleString()}€` } }
    }
  );
  incChart.render();

  document.getElementById('incFilter').addEventListener('click', () => {
    const s = document.getElementById('incStart').value,
          e = document.getElementById('incEnd').value;
    if (!s||!e) return;
    const upd = calcIncidence(s,e);
    incChart.updateOptions({
      labels: upd.labels,
      colors: [
        '#008FFB',
        '#00E396',
        '#FEB019',
        '#FF4560',
        '#775DD0',
        '#e2ae76'
      ],
      plotOptions: {
        pie: {
          donut: {
            labels: {
              value: { formatter: () => upd.totalInc.toLocaleString()+'€' },
              total:{ formatter: () => upd.totalInc.toLocaleString()+'€' }
            }
          }
        }
      }
    });
    incChart.updateSeries(upd.series);
  });
});
</script>




@endsection
