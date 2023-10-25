<?php

namespace App\Http\Controllers\Projects;

use Inertia\Inertia;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\User;
use App\Models\QR;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    public function view()
    {
        $id = current_project()->id;
        $data['project'] = Project::with(['i18n','sources','qr_params','quiz.answers'])
            ->whereHas('qr', function ($query) {
                $query->where('is_unique', 0);
            })
            ->find($id);

        return Inertia::render('Projects/Quiz', $data);
    }

    public function actions()
    {
        if(request('delQuestion')):
            Quiz::find(request('delQuestion'))->delete();
            return redirect()->back()->with('status','Question deleted!');
        endif;

        if(request('delAnswer')):
            QuizAnswer::find(request('delAnswer'))->delete();
            return redirect()->back()->with('status','Answer deleted!');
        endif;

        if(request('toggleCorrectAnswer')):
            $tca = QuizAnswer::find(request('cid'));
            $tca->is_correct = request('value') == 1 ? true : false;
            $tca->save();
            return redirect()->back()->with('status','Correct answer toggled!');
        endif;

        if(request('changeQuestionTitle')):
            $ca = Quiz::find(request('cid'));
            $ca->title = request('changeQuestionTitle');
            $ca->save();
            return redirect()->back()->with('status','Question edited!');
        endif;

        if(request('changeAnswerTitle')):
            $ca = QuizAnswer::find(request('cid'));
            $ca->title = request('changeAnswerTitle');
            $ca->save();
            return redirect()->back()->with('status','Answer edited!');
        endif;

        if(request('changeAnswerContent')):
            $ca = QuizAnswer::find(request('cid'));
            $ca->content = request('changeAnswerContent');
            $ca->save();
            return redirect()->back()->with('status','Answer content edited!');
        endif;
    }

    public function store()
    {
        $id = current_project()->id;    
            
        $q = new Quiz;
        $q->title = request('title');
        $q->if_correct = request('if_correct');
        $q->if_incorrect = request('if_incorrect');
        $q->is_always_correct = request('is_always_correct') ? true : false;
        $q->is_multi_answer = request('is_multi_answer') ? true : false;
        $q->tags = request('tags');
        $q->source = request('source');
        $q->details = request('details');
        $q->project_id = $id;
        $q->qrcode_id = request('qrcode') ? request('qrcode')['id'] : null;
        $q->source_id = request('source_id') ? request('source_id')['id'] : null;
        $q->source_value = request('qrcode') ? request('qrcode')['keyword'] : null;
        $q->country = request('country') ? request('country')['code'] : null;
        $q->language = request('language') ? request('language')['code'] : null;

        $q->save();

        foreach(request('answers') as $answer){
            if($answer['title']):
                $a = new QuizAnswer;
                $a->title = $answer['title'];
                $a->content = isset($answer['content']) ? $answer['content'] : null;
                $a->question_id = $q->id;
                $a->is_correct = request('is_always_correct') ? true : $answer['is_correct'];
                $a->save();
            endif;
        }

        if($q->save()){
            return redirect()->back()->with('status','Quiz Question Added');
        }
    }
}