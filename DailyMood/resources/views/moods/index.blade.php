<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood History - Daily Mood Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: "Inter", sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #e3f2fd;
        }

        .container {
            margin-top: 30px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #0d6efd;
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            font-size: 1.25rem;
            font-weight: bold;
        }

        .btn-action {
            padding: .375rem .75rem;
            font-size: .875rem;
            line-height: 1.5;
            border-radius: .2rem;
        }

        .table-responsive {
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .table thead th {
            background-color: #f1f1f1;
        }

        .modal-content {
            border-radius: 15px;
        }

        .modal-header {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .alert {
            border-radius: 10px;
        }

        .mood-happy {
            color: #28a745;
            font-weight: bold;
        }

        .mood-sad {
            color: #007bff;
            font-weight: bold;
        }

        .mood-angry {
            color: #dc3545;
            font-weight: bold;
        }

        .mood-excited {
            color: #ffc107;
            font-weight: bold;
        }

        .badge-streak {
            background-color: #6f42c1;
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            margin-left: 10px;
        }

        .badge-mood-of-month {
            background-color: #17a2b8;
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            margin-right: 10px;
        }

        .chart-container {
            position: relative;
            height: 40vh;
            width: 100%;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/moods') }}">Daily Mood Tracker</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item me-3">
                            <span class="navbar-text">
                                Welcome, <strong class="text-primary">{{ Auth::user()->name }}</strong>!
                            </span>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger text-white rounded-pill px-4">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Mood History</h1>
            <div class="d-flex align-items-center">
                @auth
                    @if (isset($moodOfTheMonth) && $moodOfTheMonth)
                        <span class="badge badge-mood-of-month">
                            <i class="fas fa-star me-1"></i> Mood of the Month: {{ $moodOfTheMonth }}
                        </span>
                    @endif
                @endauth

                @auth
                    @if (isset($streakDays) && $streakDays >= 3)
                        <span class="badge badge-streak">
                            <i class="fas fa-fire me-1"></i> Streak: {{ $streakDays }} Days!
                        </span>
                    @endif
                @endauth
                <a href="{{ route('moods.create') }}" class="btn btn-success ms-3 rounded-pill px-4">
                    <i class="fas fa-plus me-1"></i> Add New Mood
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card mb-4">
            <div class="card-header">Weekly Mood Summary (This Week:
                {{ \Carbon\Carbon::today()->startOfWeek(\Carbon\Carbon::MONDAY)->format('M d') }} -
                {{ \Carbon\Carbon::today()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('M d') }})</div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="weeklyMoodChart"></canvas>
                </div>
                @if (empty(array_filter($weeklyMoodSummary)))
                    <div class="alert alert-info text-center mt-3 mb-0">No mood entries logged this week.</div>
                @endif
            </div>
        </div>


        <div class="card mb-4">
            <div class="card-header">Filter Mood Entries</div>
            <div class="card-body">
                <form id="filterForm" action="{{ route('moods.index') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5 col-lg-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control rounded-pill" id="start_date" name="start_date"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-5 col-lg-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control rounded-pill" id="end_date" name="end_date"
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-6 col-lg-2">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill"><i
                                    class="fas fa-filter me-1"></i> Filter</button>
                        </div>
                        <div class="col-md-6 col-lg-2">
                            <a href="{{ route('moods.index') }}" class="btn btn-secondary w-100 rounded-pill"><i
                                    class="fas fa-eraser me-1"></i> Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-3 d-flex justify-content-end">
            <button id="exportPdfButton" class="btn btn-info text-white rounded-pill px-4">
                <i class="fas fa-file-pdf me-1"></i> Export as PDF
            </button>
        </div>

        @if ($moods->isEmpty())
            <div class="alert alert-info text-center rounded p-4">
                No mood entries found. <a href="{{ route('moods.create') }}">Add your first mood today!</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered bg-white rounded">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Mood</th>
                            <th>Note</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($moods as $mood)
                            <tr>
                                <td>{{ $mood->entry_date->format('Y-m-d') }}</td>
                                <td class="mood-{{ strtolower($mood->mood_type) }}">
                                    {{ $mood->mood_type }}
                                    @if ($mood->trashed())
                                        <span class="badge bg-secondary ms-2">Deleted</span>
                                    @endif
                                </td>
                                <td>{{ $mood->note ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if ($mood->trashed())
                                        <form action="{{ route('moods.restore', $mood->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-success btn-sm btn-action rounded-pill">
                                                <i class="fas fa-undo"></i> Restore
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('moods.edit', $mood) }}"
                                            class="btn btn-warning btn-sm btn-action me-2 rounded-pill mb-1">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        {{-- FIXED: Added data-bs-toggle and data-bs-target attributes --}}
                                        <button type="button" class="btn btn-danger btn-sm btn-action rounded-pill"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteMoodModal{{ $mood->id }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            <!-- Delete Confirmation Modal (remains the same) -->
                            <div class="modal fade" id="deleteMoodModal{{ $mood->id }}" tabindex="-1"
                                aria-labelledby="deleteMoodModalLabel{{ $mood->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" id="deleteMoodModalLabel{{ $mood->id }}">
                                                Confirm Delete</h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete the mood entry for
                                            <strong>{{ $mood->entry_date->format('Y-m-d') }}
                                                ({{ $mood->mood_type }})
                                            </strong>? This action can be undone.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary rounded-pill"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('moods.destroy', $mood) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger rounded-pill">
                                                    Yes, Delete It
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const weeklyMoodData = @json($weeklyMoodSummary);
        const ctx = document.getElementById('weeklyMoodChart').getContext('2d');

        const moodColors = {
            'Happy': 'rgba(40, 167, 69, 0.7)',
            'Sad': 'rgba(0, 123, 255, 0.7)',
            'Angry': 'rgba(220, 53, 69, 0.7)',
            'Excited': 'rgba(255, 193, 7, 0.7)'
        };
        const moodBorderColors = {
            'Happy': 'rgba(40, 167, 69, 1)',
            'Sad': 'rgba(0, 123, 255, 1)',
            'Angry': 'rgba(220, 53, 69, 1)',
            'Excited': 'rgba(255, 193, 7, 1)'
        };

        const labels = Object.keys(weeklyMoodData);
        const data = Object.values(weeklyMoodData);
        const backgroundColors = labels.map(label => moodColors[label]);
        const borderColors = labels.map(label => moodBorderColors[label]);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Mood Count',
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Entries'
                        },
                        ticks: {
                            callback: function(value) {
                                if (Number.isInteger(value)) {
                                    return value;
                                }
                            },
                            stepSize: 1
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Mood Type'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        });

        document.getElementById('exportPdfButton').addEventListener('click', function() {
            const form = document.getElementById('filterForm');
            const url = new URL("{{ route('moods.export.pdf') }}");
            const startDate = form.querySelector('[name="start_date"]').value;
            const endDate = form.querySelector('[name="end_date"]').value;

            if (startDate) {
                url.searchParams.append('start_date', startDate);
            }
            if (endDate) {
                url.searchParams.append('end_date', endDate);
            }
            url.searchParams.append('show_deleted', 'on');

            window.location.href = url.toString();
        });
    </script>
</body>

</html>
