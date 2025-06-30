<?php

namespace App\Http\Controllers;

use App\Models\Mood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use PDF;

class MoodController extends Controller
{
    public function index(Request $request)
    {

        $userMoodsQuery = Auth::user()->moods()->withTrashed();

        $userMoodsQuery->orderBy('entry_date', 'desc');

        if ($request->has('start_date') && $request->has('end_date')) {
            $request->validate([
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            if ($request->filled('start_date')) {
                $userMoodsQuery->where('entry_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $userMoodsQuery->where('entry_date', '<=', $request->end_date);
            }
        }

        $moods = $userMoodsQuery->get();

        $streakDays = $this->calculateStreak(Auth::user()->id);

        $moodOfTheMonth = $this->getMoodOfTheMonth(Auth::user()->id);

        $weeklyMoodSummary = $this->getWeeklyMoodSummary(Auth::user()->id);

        return view('moods.index', compact('moods', 'streakDays', 'moodOfTheMonth', 'weeklyMoodSummary'));
    }

    public function create()
    {
        return view('moods.create');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'mood_type' => ['required', 'string', Rule::in(['Happy', 'Sad', 'Angry', 'Excited'])],
            'note' => ['nullable', 'string', 'max:500'],
            'entry_date' => [
                'required',
                'date',
                'before_or_equal:today',
                Rule::unique('moods')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
        ]);

        Auth::user()->moods()->create([
            'mood_type' => $request->mood_type,
            'note' => $request->note,
            'entry_date' => $request->entry_date,
        ]);

        return redirect()->route('moods.index')->with('success', 'Mood entry added successfully!');
    }

    public function show(Mood $mood)
    {
        if ($mood->user_id !== Auth::id()) {
            abort(403);
        }

        return view('moods.show', compact('mood'));
    }

    public function edit(Mood $mood)
    {

        if ($mood->user_id !== Auth::id()) {
            abort(403);
        }

        return view('moods.edit', compact('mood'));
    }

    public function update(Request $request, Mood $mood)
    {
        if ($mood->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'mood_type' => ['required', 'string', Rule::in(['Happy', 'Sad', 'Angry', 'Excited'])],
            'note' => ['nullable', 'string', 'max:500'],
            'entry_date' => [
                'required',
                'date',
                'before_or_equal:today',
                Rule::unique('moods')->where(function ($query) use ($mood) {
                    return $query->where('user_id', Auth::id())->where('id', '!=', $mood->id);
                }),
            ],
        ]);

        $mood->update([
            'mood_type' => $request->mood_type,
            'note' => $request->note,
            'entry_date' => $request->entry_date,
        ]);

        return redirect()->route('moods.index')->with('success', 'Mood entry updated successfully!');
    }

    public function destroy(Mood $mood)
    {
        if ($mood->user_id !== Auth::id()) {
            abort(403);
        }

        $mood->delete();

        return redirect()->route('moods.index')->with('success', 'Mood entry soft deleted successfully!');
    }

    public function restore($id)
    {
        $mood = Mood::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $mood->restore(); // Restores the soft-deleted entry

        return redirect()->route('moods.index')->with('success', 'Mood entry restored successfully!');
    }

    private function calculateStreak(int $userId): int
    {
        $userMoods = Mood::where('user_id', $userId)
            ->whereNull('deleted_at')
            ->orderBy('entry_date', 'desc')
            ->get();

        if ($userMoods->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $today = Carbon::today();

        $latestMoodEntry = $userMoods->first();
        $latestMoodDate = Carbon::parse($latestMoodEntry->entry_date);

        if ($latestMoodDate->isToday()) {
            $streak = 1;
            $comparisonDate = $today->subDay();
        } elseif ($latestMoodDate->isYesterday()) {
            $streak = 1;
            $comparisonDate = $latestMoodDate->subDay();
        } else {
            return 0;
        }

        foreach ($userMoods as $index => $mood) {
            if ($index === 0) {
                continue;
            }

            $currentMoodDate = Carbon::parse($mood->entry_date);

            if ($currentMoodDate->equalTo($comparisonDate)) {
                $streak++;
                $comparisonDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    private function getMoodOfTheMonth(int $userId): ?string
    {
        $thirtyDaysAgo = Carbon::today()->subDays(30);

        $mostFrequentMood = Mood::select('mood_type')
            ->where('user_id', $userId)
            ->where('entry_date', '>=', $thirtyDaysAgo)
            ->whereNull('deleted_at')
            ->groupBy('mood_type')
            ->orderByRaw('COUNT(*) DESC')
            ->first();

        return $mostFrequentMood ? $mostFrequentMood->mood_type : null;
    }

    private function getWeeklyMoodSummary(int $userId): array
    {
        $startOfWeek = Carbon::today()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::today()->endOfWeek(Carbon::SUNDAY);

        $weeklyMoods = Mood::select('mood_type')
            ->where('user_id', $userId)
            ->whereBetween('entry_date', [$startOfWeek, $endOfWeek])
            ->whereNull('deleted_at')
            ->get();

        $moodCounts = $weeklyMoods->groupBy('mood_type')->map->count();

        $allMoodTypes = ['Happy', 'Sad', 'Angry', 'Excited'];
        $summary = array_fill_keys($allMoodTypes, 0);

        foreach ($moodCounts as $moodType => $count) {
            $summary[$moodType] = $count;
        }

        return $summary;
    }

    public function exportPdf(Request $request)
    {
        $query = Auth::user()->moods()->withTrashed();
        $query->orderBy('entry_date', 'desc');
        if ($request->has('start_date') && $request->has('end_date')) {
            if ($request->filled('start_date')) {
                $query->where('entry_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->where('entry_date', '<=', $request->end_date);
            }
        }

        $moodsToExport = $query->get();

        $data = [
            'moods' => $moodsToExport,
            'userName' => Auth::user()->name,
            'reportDate' => Carbon::now()->format('Y-m-d H:i:s'),
            'filterInfo' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'show_deleted' => true,
            ]
        ];

        $pdf = PDF::loadView('moods.pdf_template', $data);
        return $pdf->download('mood_log_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }
}
