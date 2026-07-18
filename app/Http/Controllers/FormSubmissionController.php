<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

class FormSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = FormSubmission::with('form');

        if ($request->form_id) {
            $query->where('form_id', $request->form_id);
        }

        $submissions = $query->latest()->paginate(15);
        $forms = Form::select('id', 'form_name')->get();

        return view('admin.forms.submissions', compact('submissions', 'forms'));
    }

    public function show($id)
    {
        $submission = FormSubmission::with('form.fields')->findOrFail($id);
        return view('admin.forms.submission_show', compact('submission'));
    }

    public function destroy($id)
    {
        $submission = FormSubmission::findOrFail($id);
        $submission->delete();
        return redirect()->route('form-submissions.index')->with('success', 'Form submission deleted successfully.');
    }
}
