<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mood - Daily Mood Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: "Inter", sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #e3f2fd;
        }

        .container {
            margin-top: 50px;
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

        .form-control,
        .form-select,
        .btn {
            border-radius: 10px;
        }

        .btn-back {
            background-color: #6c757d;
            /* Bootstrap secondary */
            border-color: #6c757d;
        }

        .btn-back:hover {
            background-color: #5c636a;
            border-color: #565e64;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/dashboard') }}">Daily Mood Tracker</a>
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
            <h1 class="mb-0">Edit Mood for {{ $mood->entry_date->format('Y-m-d') }}</h1>
            <a href="{{ route('moods.index') }}" class="btn btn-back rounded-pill px-4">
                <i class="fas fa-arrow-left me-1"></i> Back to History
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger rounded">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header">Update Your Mood</div>
            <div class="card-body">
                <form action="{{ route('moods.update', $mood) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Required for update method --}}

                    <div class="mb-3">
                        <label for="entry_date" class="form-label">Date</label>
                        <input type="date"
                            class="form-control rounded-pill @error('entry_date') is-invalid @enderror" id="entry_date"
                            name="entry_date" value="{{ old('entry_date', $mood->entry_date->format('Y-m-d')) }}"
                            required max="{{ date('Y-m-d') }}">
                        @error('entry_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="mood_type" class="form-label">Mood</label>
                        <select class="form-select rounded-pill @error('mood_type') is-invalid @enderror" id="mood_type"
                            name="mood_type" required>
                            <option value="">Select your mood</option>
                            <option value="Happy"
                                {{ old('mood_type', $mood->mood_type) == 'Happy' ? 'selected' : '' }}>Happy</option>
                            <option value="Sad" {{ old('mood_type', $mood->mood_type) == 'Sad' ? 'selected' : '' }}>
                                Sad</option>
                            <option value="Angry"
                                {{ old('mood_type', $mood->mood_type) == 'Angry' ? 'selected' : '' }}>Angry</option>
                            <option value="Excited"
                                {{ old('mood_type', $mood->mood_type) == 'Excited' ? 'selected' : '' }}>Excited
                            </option>
                        </select>
                        @error('mood_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Note (Optional)</label>
                        <textarea class="form-control rounded-pill @error('note') is-invalid @enderror" id="note" name="note"
                            rows="3">{{ old('note', $mood->note) }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2">
                            <i class="fas fa-save me-1"></i> Update Mood
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
