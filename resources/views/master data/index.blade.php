@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Audit Dashboard</h3>
                        @if ($isAdmin)
                            <p class="text-muted">You are viewing data from all departments.</p>
                        @else
                            <p class="text-muted">You are viewing data from your department.</p>
                        @endif
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex">
                            <div class="justify-content-end d-flex">
                                <div class="flex-md-grow-1 flex-xl-grow-0">
                                    <span class="btn btn-sm btn-light bg-white" id="currentDateText"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Section for Pie Charts -->
        <div class="row">
            <!-- Task Completion Pie Chart -->
            <div class="col-md-6 mb-4 stretch-card transparent">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Task Completion</h4>
                        <canvas id="completionPieChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Documents and Item Audits Pie Chart -->
            <div class="col-md-6 mb-4 stretch-card transparent">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Documents vs Item Audits</h4>
                        <canvas id="documentsItemsPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateDateTime() {
            var currentDate = new Date();
            var formattedDate = currentDate.toLocaleString();
            document.getElementById('currentDateText').textContent = formattedDate;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Function to create chart with "No Data Available" message
        function createPieChart(ctx, data, labels, backgroundColor, hoverBackgroundColor) {
            if (data.every(value => value === 0)) {
                // Jika tidak ada data, tampilkan pesan No Data Available
                ctx.font = "20px Arial";
                ctx.textAlign = "center";
                ctx.fillText("No Data Available", ctx.canvas.width / 2, ctx.canvas.height / 2);
            } else {
                // Jika ada data, buat chart
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: backgroundColor,
                            hoverBackgroundColor: hoverBackgroundColor
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        }

        // Data for Task Completion Pie Chart
        var ctx1 = document.getElementById('completionPieChart').getContext('2d');
        var completionData = [{{ $completedTasks }}, {{ $totalTasks - $completedTasks }}];
        createPieChart(ctx1, completionData, ['Completed', 'Not Completed'], ['#36A2EB', '#FF6384'], ['#36A2EB',
        '#FF6384']);

        // Data for Documents vs Item Audits Pie Chart
        var ctx2 = document.getElementById('documentsItemsPieChart').getContext('2d');
        var documentsItemsData = [{{ $totalDocuments }}, {{ $totalItemAudits }}];
        createPieChart(ctx2, documentsItemsData, ['Documents', 'Item Audits'], ['#FFCE56', '#FF9F40'], ['#FFCE56',
            '#FF9F40']);
    </script>
@endsection
