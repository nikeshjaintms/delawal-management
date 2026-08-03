<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $query = FormSubmission::with(['form', 'firm']);

        if (!$isAdmin) {
            $query->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->form_id) {
            $query->where('form_id', $request->form_id);
        }

        $submissions = $query->latest()->paginate(15)->withQueryString();
        
        $formsQuery = Form::select('id', 'form_name', 'firm_id');
        if (!$isAdmin) {
            $formsQuery->where('firm_id', $firmId);
        }
        $forms = $formsQuery->get();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.forms.submissions', compact('submissions', 'forms', 'firms'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $submission = FormSubmission::with(['form.fields', 'firm'])->findOrFail($id);
        if (!$isAdmin && $submission->firm_id != $firmId) {
            abort(403);
        }
        return view('admin.forms.submission_show', compact('submission'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $submission = FormSubmission::findOrFail($id);
        if (!$isAdmin && $submission->firm_id != $firmId) {
            abort(403);
        }
        $submission->delete();
        return redirect()->route('form-submissions.index')->with('success', 'Form submission deleted successfully.');
    }
}
