<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeCategory;
use App\Models\Level;
use App\Models\Section;
use App\Models\Subject;
use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request, string $resource): View
    {
        $config = $this->config($resource);
        $query = $config['model']::query();

        if ($config['school_scoped']) {
            $query->where('school_id', $request->user()->school_id);
        }

        if ($resource === 'fee-categories') {
            $query->where('academic_year_id', $request->user()->school?->defaultAcademicYear()?->id);
        }

        $items = $query->orderBy($config['order_by'])->paginate(50)->withQueryString();

        return view('admin.resources.index', compact('items', 'config', 'resource'));
    }

    public function create(string $resource): View
    {
        return view('admin.resources.create', [
            'config' => $this->config($resource),
            'resource' => $resource,
            'item' => null,
        ]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $config = $this->config($resource);
        $data = $this->validated($request, $config, $resource);

        if ($config['school_scoped']) {
            $data['school_id'] = $request->user()->school_id;
        }

        if ($resource === 'fee-categories') {
            $data['academic_year_id'] = $this->currentYearId($request);
        }

        $item = $config['model']::create($data);
        $this->auditLogger->log($resource.'.created', $item);

        return redirect()->route($this->indexRoute($request), $resource)->with('success', $config['singular'].' créé.');
    }

    public function edit(string $resource, int $id): View
    {
        $config = $this->config($resource);
        $item = $this->find($config, $id, request()->user()->school_id);

        return view('admin.resources.edit', compact('item', 'config', 'resource'));
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $config = $this->config($resource);
        $item = $this->find($config, $id, $request->user()->school_id);
        $old = $item->toArray();
        $data = $this->validated($request, $config, $resource, $item->getKey());

        if ($config['school_scoped']) {
            $data['school_id'] = $request->user()->school_id;
        }

        if ($resource === 'fee-categories') {
            $data['academic_year_id'] = $this->currentYearId($request);
        }

        $item->update($data);
        $this->auditLogger->log($resource.'.updated', $item, $old, $item->fresh()->toArray());

        return redirect()->route($this->indexRoute($request), $resource)->with('success', $config['singular'].' mis à jour.');
    }

    public function destroy(Request $request, string $resource, int $id): RedirectResponse
    {
        $config = $this->config($resource);
        $item = $this->find($config, $id, $request->user()->school_id);

        try {
            $item->delete();
        } catch (QueryException) {
            return back()->withErrors(['resource' => 'Suppression impossible : cet élément est utilisé par d’autres données.']);
        }

        $this->auditLogger->log($resource.'.deleted', $item);

        return redirect()->route($this->indexRoute($request), $resource)->with('success', $config['singular'].' supprimé.');
    }

    /**
     * @param  array{model: class-string<Model>, title: string, singular: string, school_scoped: bool, order_by: string, fields: array<string, array{label: string, type: string, rules: array<int, mixed>, options?: array<string, string>, nullable?: bool}>}  $config
     * @return array<string, mixed>
     */
    private function validated(Request $request, array $config, string $resource, ?int $ignoreId = null): array
    {
        $rules = [];

        foreach ($config['fields'] as $name => $field) {
            $fieldRules = $field['rules'];

            if ($ignoreId && in_array('unique', array_map(fn ($rule) => is_string($rule) ? explode(':', $rule)[0] : '', $fieldRules), true)) {
                $fieldRules = array_map(function ($rule) use ($ignoreId) {
                    if (is_string($rule) && str_starts_with($rule, 'unique:')) {
                        return Rule::unique(substr($rule, 7))->ignore($ignoreId);
                    }

                    return $rule;
                }, $fieldRules);
            }

            $rules[$name] = $fieldRules;
        }

        if ($resource === 'fee-categories') {
            $rules['name'][] = Rule::unique('fee_categories', 'name')
                ->where('academic_year_id', $this->currentYearId($request))
                ->ignore($ignoreId);
        }

        $data = $request->validate($rules);

        foreach ($config['fields'] as $name => $field) {
            if ($field['type'] === 'checkbox') {
                $data[$name] = $request->boolean($name);
            }
        }

        return $data;
    }

    private function currentYearId(Request $request): int
    {
        $year = $request->user()->school?->defaultAcademicYear();
        abort_unless($year, 422, 'Aucune année scolaire active.');

        return $year->id;
    }

    private function indexRoute(Request $request): string
    {
        return str_starts_with((string) $request->route()?->getName(), 'admin.finance-resources')
            ? 'admin.finance-resources.index'
            : 'admin.academic.index';
    }

    /**
     * @param  array{model: class-string<Model>, school_scoped: bool}  $config
     */
    private function find(array $config, int $id, ?int $schoolId): Model
    {
        $query = $config['model']::query();

        if ($config['school_scoped']) {
            $query->where('school_id', $schoolId);
        }

        return $query->findOrFail($id);
    }

    /**
     * @return array{model: class-string<Model>, title: string, singular: string, school_scoped: bool, order_by: string, fields: array<string, array{label: string, type: string, rules: array<int, mixed>, options?: array<string, string>, nullable?: bool}>}
     */
    private function config(string $resource): array
    {
        $configs = [
            'levels' => [
                'model' => Level::class,
                'title' => 'Niveaux',
                'singular' => 'Niveau',
                'school_scoped' => false,
                'order_by' => 'position',
                'fields' => [
                    'name' => ['label' => 'Nom', 'type' => 'text', 'rules' => ['required', 'string', 'max:255', 'unique:levels,name']],
                    'code' => ['label' => 'Code', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:20', 'unique:levels,code']],
                    'position' => ['label' => 'Position', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
                ],
            ],
            'sections' => [
                'model' => Section::class,
                'title' => 'Sections',
                'singular' => 'Section',
                'school_scoped' => false,
                'order_by' => 'name',
                'fields' => [
                    'name' => ['label' => 'Nom', 'type' => 'text', 'rules' => ['required', 'string', 'max:255', 'unique:sections,name']],
                    'code' => ['label' => 'Code', 'type' => 'text', 'rules' => ['required', 'string', 'max:20', 'unique:sections,code']],
                ],
            ],
            'subjects' => [
                'model' => Subject::class,
                'title' => 'Matières',
                'singular' => 'Matière',
                'school_scoped' => false,
                'order_by' => 'name',
                'fields' => [
                    'name' => ['label' => 'Nom', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    'code' => ['label' => 'Code', 'type' => 'text', 'rules' => ['required', 'string', 'max:20', 'unique:subjects,code']],
                    'coefficient' => ['label' => 'Coefficient', 'type' => 'number', 'rules' => ['required', 'numeric', 'min:0.5', 'max:20']],
                    'description' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string'], 'nullable' => true],
                ],
            ],
            'fee-categories' => [
                'model' => FeeCategory::class,
                'title' => 'Catégories de frais',
                'singular' => 'Catégorie de frais',
                'school_scoped' => true,
                'order_by' => 'name',
                'fields' => [
                    'name' => ['label' => 'Nom', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                    'amount' => ['label' => 'Montant', 'type' => 'number', 'rules' => ['required', 'numeric', 'min:0']],
                    'due_date' => ['label' => 'Échéance', 'type' => 'date', 'rules' => ['nullable', 'date']],
                    'description' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['nullable', 'string'], 'nullable' => true],
                    'is_mandatory' => ['label' => 'Obligatoire', 'type' => 'checkbox', 'rules' => ['nullable', 'boolean']],
                ],
            ],
        ];

        abort_unless(isset($configs[$resource]), 404);

        return $configs[$resource];
    }
}
