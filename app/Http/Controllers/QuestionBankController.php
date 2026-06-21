<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionBankController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = QuestionBank::with('subject', 'options', 'teacher.user');

        if ($user->hasRole('Teacher')) {
            $query->where('teacher_id', $user->teacher?->id);
        }

        $questions = $query->latest()->paginate(15);
        $subjects = Subject::orderBy('name')->get();

        return view('questions.index', compact('questions', 'subjects'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('name')->get();
        return view('questions.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false,fill_blank,short_answer,essay,matching,multi_select,numeric',
            'default_marks' => 'nullable|numeric|min:0|max:999',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'topic' => 'nullable|string|max:255',
            'explanation' => 'nullable|string',
            'tags' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'options' => 'nullable|array',
            'options.*.option_text' => 'required_with:options|string',
            'options.*.is_correct' => 'nullable|boolean',
            'options.*.order' => 'nullable|integer',
        ]);

        $data['teacher_id'] = Auth::user()->teacher?->id;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['tags'] = $request->tags ? array_map('trim', explode(',', $request->tags)) : null;

        $question = QuestionBank::create($data);

        if ($request->has('options')) {
            foreach ($request->options as $index => $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['option_text'],
                    'is_correct' => $opt['is_correct'] ?? false,
                    'order' => $opt['order'] ?? $index + 1,
                ]);
            }
        }

        ActivityLogger::log('question-created', 'QuestionBank', $question->id, "Created question: {$question->question_text}");

        return redirect()->route('questions.index')->with('success', 'Question created successfully.');
    }

    public function show(QuestionBank $question)
    {
        $question->load('subject', 'options', 'teacher.user');
        return view('questions.show', compact('question'));
    }

    public function edit(QuestionBank $question)
    {
        $subjects = Subject::orderBy('name')->get();
        $question->load('options');
        return view('questions.edit', compact('question', 'subjects'));
    }

    public function update(Request $request, QuestionBank $question)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false,fill_blank,short_answer,essay,matching,multi_select,numeric',
            'default_marks' => 'nullable|numeric|min:0|max:999',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'topic' => 'nullable|string|max:255',
            'explanation' => 'nullable|string',
            'tags' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'options' => 'nullable|array',
            'options.*.id' => 'nullable|exists:question_options,id',
            'options.*.option_text' => 'required_with:options|string',
            'options.*.is_correct' => 'nullable|boolean',
            'options.*.order' => 'nullable|integer',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['tags'] = $request->tags ? array_map('trim', explode(',', $request->tags)) : null;

        $question->update($data);

        if ($request->has('options')) {
            $existingIds = $question->options->pluck('id')->toArray();
            $incomingIds = [];

            foreach ($request->options as $index => $opt) {
                if (!empty($opt['id'])) {
                    $incomingIds[] = $opt['id'];
                    QuestionOption::where('id', $opt['id'])->update([
                        'option_text' => $opt['option_text'],
                        'is_correct' => $opt['is_correct'] ?? false,
                        'order' => $opt['order'] ?? $index + 1,
                    ]);
                } else {
                    $newOpt = QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $opt['option_text'],
                        'is_correct' => $opt['is_correct'] ?? false,
                        'order' => $opt['order'] ?? $index + 1,
                    ]);
                    $incomingIds[] = $newOpt->id;
                }
            }

            $toDelete = array_diff($existingIds, $incomingIds);
            QuestionOption::whereIn('id', $toDelete)->delete();
        }

        ActivityLogger::log('question-updated', 'QuestionBank', $question->id, "Updated question: {$question->question_text}");

        return redirect()->route('questions.index')->with('success', 'Question updated successfully.');
    }

    public function destroy(QuestionBank $question)
    {
        ActivityLogger::log('question-deleted', 'QuestionBank', $question->id, "Deleted question: {$question->question_text}");
        $question->delete();
        return redirect()->route('questions.index')->with('success', 'Question deleted successfully.');
    }
}
