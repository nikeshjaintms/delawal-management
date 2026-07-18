<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormRequest;

use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class FormController extends Controller
{
    public function index(Request $request)
    {
        $query = Form::query();

        if ($request->search) {
            $query->where('form_name', 'like', '%' . $request->search . '%')
                ->orWhere('form_type', 'like', '%' . $request->search . '%');
        }

        $forms = $query->latest()->paginate(10);

        return view('admin.forms.index', compact('forms'));
    }

    public function create()
    {
        return view('admin.forms.create');
    }

    public function store(FormRequest $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $form = Form::create([
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
        $fields = $form->fields()->where('status', 'active')->orderBy('sort_order', 'asc')->get();
        return view('admin.forms.show', compact('form', 'fields'));
    }

    public function edit(Form $form)
    {
        $fields = $form->fields()->orderBy('sort_order', 'asc')->get();
        return view('admin.forms.edit', compact('form', 'fields'));
    }

    public function update(FormRequest $request, Form $form)
    {
        return DB::transaction(function () use ($request, $form) {
            try {
                $form->update([
                    'form_name'   => $request->form_name,
                    'form_type'   => $request->form_type,
                    'description' => $request->description,
                    'status'      => $request->status,
                ]);

                $form->fields()->delete();

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

    public function toggleStatus(Form $form)
    {
        $form->update([
            'status' => $form->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('forms.index')->with('success', 'Form status updated successfully.');
    }

    public function submit(Request $request, Form $form)
    {
        $fields = $form->fields()->where('status', 'active')->orderBy('sort_order', 'asc')->get();

        $rules    = [];
        $messages = [];

        foreach ($fields as $field) {
            $rule = [];
            if ($field->is_required) {
                $rule[]     = 'required';
                $messages[$field->field_name . '.required'] = "The {$field->label} field is required.";
            } else {
                $rule[] = 'nullable';
            }

            if ($field->field_type === 'email') {
                $rule[]     = 'email';
                $messages[$field->field_name . '.email'] = "The {$field->label} must be a valid email address.";
            } elseif ($field->field_type === 'number') {
                $rule[]     = 'numeric';
                $messages[$field->field_name . '.numeric'] = "The {$field->label} must be a number.";
            } elseif ($field->field_type === 'file') {
                $rule[]     = 'file|max:10240';
                $messages[$field->field_name . '.max'] = "The {$field->label} must not be greater than 10MB.";
            }

            $rules[$field->field_name] = implode('|', $rule);
        }
        

        $submissionData = [];
        foreach ($fields as $field) {
            $fieldName = $field->field_name;
            if ($field->field_type === 'file' && $request->hasFile($fieldName)) {
                $path = $request->file($fieldName)->store('form-submissions', 'public');
                $submissionData[$fieldName] = [
                    'type'          => 'file',
                    'value'         => $path,
                    'original_name' => $request->file($fieldName)->getClientOriginalName(),
                ];
            } elseif ($field->field_type === 'checkbox') {
                $submissionData[$fieldName] = $request->input($fieldName, []);
            } else {
                $submissionData[$fieldName] = $request->input($fieldName);
            }
        }

        FormSubmission::create([
            'form_id'        => $form->id,
            'submitted_data' => $submissionData,
        ]);

        return redirect()->route('forms.show', $form->id)->with('success', 'Form submitted successfully.');
    }

    public function destroy(Form $form)
    {
        $form->fields()->delete();
        $form->delete();
        return redirect()->route('forms.index')->with('success', 'Form deleted successfully.');
    }
}
