<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormRequest;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSubmission;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class FormController extends Controller
{
    public function index(Request $request)
    {
        $query = Form::with('firm');

        if (!Auth::user()->isAdmin()) {
            $query->where('firm_id', Auth::user()->firm_id);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('form_name', 'like', "%{$search}%")
                  ->orWhere('form_type', 'like', "%{$search}%")
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$search}%"));
            });
        }

        $forms = $query->latest()->paginate(10)->withQueryString();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.forms.index', compact('forms', 'firms'));
    }

    public function create()
    {
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        return view('admin.forms.create', compact('firms'));
    }

    public function store(FormRequest $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $firmId = $request->firm_id ?? Auth::user()->firm_id;

                $form = Form::create([
                    'firm_id'     => $firmId,
                    'form_name'   => $request->form_name,
                    'form_type'   => $request->form_type,
                    'description' => $request->description,
                    'status'      => $request->status,
                ]);

                if ($request->has('fields') && is_array($request->fields)) {
                    foreach ($request->fields as $fieldData) {
                        $form->fields()->create([
                            'label'       => $fieldData['label'],
                            'field_name'  => strtolower(str_replace(' ', '_', $fieldData['field_name'])),
                            'field_type'  => $fieldData['field_type'],
                            'is_required' => isset($fieldData['is_required']) && $fieldData['is_required'] ? true : false,
                            'options'     => $fieldData['options'] ?? null,
                            'sort_order'  => $fieldData['sort_order'],
                            'status'      => $fieldData['status'],
                        ]);
                    }
                }

                if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Form created successfully.',
                        'form' => $form
                    ]);
                }

                return redirect()->route('forms.index')->with('success', 'Form created successfully.');
            } catch (Exception $e) {
                if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error creating form: ' . $e->getMessage()
                    ], 500);
                }
                return back()->withInput()->with('error', 'Error creating form: ' . $e->getMessage());
            }
        });
    }

    public function show(Form $form)
    {
        if (!Auth::user()->isAdmin() && $form->firm_id != Auth::user()->firm_id) abort(403);

        $fields = $form->fields()->where('status', 'active')->orderBy('sort_order', 'asc')->get();
        return view('admin.forms.show', compact('form', 'fields'));
    }

    public function edit(Form $form)
    {
        if (!Auth::user()->isAdmin() && $form->firm_id != Auth::user()->firm_id) abort(403);

        $fields = $form->fields()->orderBy('sort_order', 'asc')->get();
        $firms  = Firm::where('status', 'active')->orderBy('firm_name')->get();
        return view('admin.forms.edit', compact('form', 'fields', 'firms'));
    }

    public function update(FormRequest $request, Form $form)
    {
        if (!Auth::user()->isAdmin() && $form->firm_id != Auth::user()->firm_id) abort(403);

        return DB::transaction(function () use ($request, $form) {
            try {
                $firmId = $request->firm_id ?? $form->firm_id;

                $form->update([
                    'firm_id'     => $firmId,
                    'form_name'   => $request->form_name,
                    'form_type'   => $request->form_type,
                    'description' => $request->description,
                    'status'      => $request->status,
                ]);

                if ($request->has('fields') && is_array($request->fields)) {
                    // Sync fields
                    $existingIds = [];
                    foreach ($request->fields as $fieldData) {
                        if (isset($fieldData['id']) && $fieldData['id']) {
                            $field = FormField::find($fieldData['id']);
                            if ($field) {
                                $field->update([
                                    'label'       => $fieldData['label'],
                                    'field_name'  => strtolower(str_replace(' ', '_', $fieldData['field_name'])),
                                    'field_type'  => $fieldData['field_type'],
                                    'is_required' => isset($fieldData['is_required']) && $fieldData['is_required'] ? true : false,
                                    'options'     => $fieldData['options'] ?? null,
                                    'sort_order'  => $fieldData['sort_order'],
                                    'status'      => $fieldData['status'],
                                ]);
                                $existingIds[] = $field->id;
                            }
                        } else {
                            $newField = $form->fields()->create([
                                'label'       => $fieldData['label'],
                                'field_name'  => strtolower(str_replace(' ', '_', $fieldData['field_name'])),
                                'field_type'  => $fieldData['field_type'],
                                'is_required' => isset($fieldData['is_required']) && $fieldData['is_required'] ? true : false,
                                'options'     => $fieldData['options'] ?? null,
                                'sort_order'  => $fieldData['sort_order'],
                                'status'      => $fieldData['status'],
                            ]);
                            $existingIds[] = $newField->id;
                        }
                    }
                    $form->fields()->whereNotIn('id', $existingIds)->delete();
                }

                if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Form updated successfully.',
                        'form' => $form
                    ]);
                }

                return redirect()->route('forms.index')->with('success', 'Form updated successfully.');
            } catch (Exception $e) {
                if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error updating form: ' . $e->getMessage()
                    ], 500);
                }
                return back()->withInput()->with('error', 'Error updating form: ' . $e->getMessage());
            }
        });
    }

    public function destroy(Form $form)
    {
        if (!Auth::user()->isAdmin() && $form->firm_id != Auth::user()->firm_id) abort(403);

        $form->delete();

        return redirect()->route('forms.index')->with('success', 'Form deleted successfully.');
    }

    public function toggleStatus(Form $form)
    {
        if (!Auth::user()->isAdmin() && $form->firm_id != Auth::user()->firm_id) abort(403);

        $form->status = $form->status === 'active' ? 'inactive' : 'active';
        $form->save();

        return redirect()->route('forms.index')->with('success', 'Form status updated successfully.');
    }

    public function submit(Request $request, Form $form)
    {
        $firmId = $form->firm_id ?: Auth::user()->firm_id;

        FormSubmission::create([
            'firm_id'        => $firmId,
            'form_id'        => $form->id,
            'submitted_data' => $request->except(['_token', 'firm_id']),
        ]);

        return redirect()->back()->with('success', 'Form submitted successfully.');
    }
}
