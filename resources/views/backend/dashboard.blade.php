@extends('backend.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h6>Visite Totali</h6>
                    <h4>{{ $totalVisits }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h6>Visite Uniche Totali</h6>
                    <h4>{{ $uniqueTotalVisits }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h6>Visite Oggi</h6>
                    <h4>{{ $todayVisits }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h6>Visite Settimana</h6>
                    <h4>{{ $weekVisits }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <h6>Visite Mese</h6>
                    <h4>{{ $monthVisits }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h6>Ultimi 7 Giorni - Tutte le Visite</h6>
                    <canvas id="chartTotal"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h6>Ultimi 7 Giorni - Visite Uniche</h6>
                    <canvas id="chartUnique"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const totalCtx = document.getElementById('chartTotal').getContext('2d');
    const uniqueCtx = document.getElementById('chartUnique').getContext('2d');

    const labels = @json(array_column($last7Days, 'date'));
    const totalData = @json(array_column($last7Days, 'visits'));
    const uniqueData = @json(array_column($last7DaysUnique, 'visits'));

    new Chart(totalCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Visite Totali',
                data: totalData,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        }
    });

    new Chart(uniqueCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Visite Uniche',
                data: uniqueData,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        }
    });
</script>
@endsection
