<?php

namespace App\Http\Controllers;

use App\Models\Metric;
use App\Models\Objective;
use App\Models\User;
use App\Support\Activity;
use App\Support\Refs;
use Illuminate\Http\Request;

class MetricController extends Controller
{
    private array $rules = [
        'name' => 'required|string|max:256',
        'description' => 'nullable|string',
        'metric_type' => 'required|in:KPI,KRI,KCI',
        'objective_id' => 'nullable|uuid|exists:objectives,id',
        'target_value' => 'nullable|numeric',
        'current_value' => 'nullable|numeric',
        'unit' => 'nullable|string|max:32',
        'direction' => 'required|in:higher_is_better,lower_is_better',
        'frequency' => 'nullable|string|max:32',
        'owner_id' => 'nullable|uuid|exists:users,id',
    ];

    public function index(Request $r)
    {
        $q = Metric::with('owner', 'objective')->orderBy('ref_id');
        if ($r->filled('metric_type')) {
            $q->where('metric_type', $r->input('metric_type'));
        }
        if ($r->filled('search')) {
            $q->where('name', 'like', '%'.$r->input('search').'%');
        }
        $items = $q->paginate(40)->withQueryString();

        return view('metrics.index', ['items' => $items, 'filters' => $r->only('metric_type', 'search')]);
    }

    public function create()
    {
        return view('metrics.form', ['item' => new Metric(['metric_type' => 'KPI', 'direction' => 'higher_is_better']), 'users' => $this->users(), 'objectives' => $this->objectives()]);
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules);
        $data['ref_id'] = Refs::next(Metric::class, 'MET');
        $m = Metric::create($data);
        Activity::log('CREATE', 'metric', $m->id);

        return redirect()->route('metrics.show', $m)->with('status', 'Metric created.');
    }

    public function show(Metric $metric)
    {
        $metric->load('owner', 'objective', 'measurements');

        return view('metrics.show', ['metric' => $metric]);
    }

    public function edit(Metric $metric)
    {
        return view('metrics.form', ['item' => $metric, 'users' => $this->users(), 'objectives' => $this->objectives()]);
    }

    public function update(Request $r, Metric $metric)
    {
        $metric->update($r->validate($this->rules));
        Activity::log('UPDATE', 'metric', $metric->id);

        return redirect()->route('metrics.show', $metric)->with('status', 'Metric updated.');
    }

    public function destroy(Metric $metric)
    {
        Activity::log('DELETE', 'metric', $metric->id);
        $metric->delete();

        return redirect()->route('metrics.index')->with('status', 'Metric deleted.');
    }

    public function addMeasurement(Request $r, Metric $metric)
    {
        $data = $r->validate([
            'value' => 'required|numeric',
            'captured_at' => 'nullable|date',
            'note' => 'nullable|string',
        ]);
        $captured = $data['captured_at'] ?? now()->toDateString();
        $metric->measurements()->create(['value' => $data['value'], 'captured_at' => $captured, 'note' => $data['note'] ?? null]);
        $metric->update(['current_value' => $data['value'], 'last_measured' => $captured]);
        Activity::log('MEASURE', 'metric', $metric->id);

        return back()->with('status', 'Measurement recorded.');
    }

    private function users()
    {
        return User::orderBy('full_name')->get(['id', 'full_name']);
    }

    private function objectives()
    {
        return Objective::orderBy('ref_id')->get(['id', 'ref_id', 'title']);
    }
}
