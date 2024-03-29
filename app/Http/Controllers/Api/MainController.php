<?php

namespace App\Http\Controllers\Api;

use Inertia\Inertia;
use Auth;
use App;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LogAction;
use App\Models\Project;
use App\Models\QR;
use App\Models\User;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizResult;
use App\Models\Member;
use App\Models\Participant;
use App\Models\Leaderboard;
use App\Models\Prize;
use App\Models\AlphaNumCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApiSendMail;
use App\Mail\ResetPassword;
use App\Models\Article;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->middleware('bearer')->except(['geo', 'alphaNumView', 'qrcodesViews']);
    }

    public function testAPI()
    {
        return ['success' => true];
    }

	public function geo()
	{
		$ip = $this->request->ip();
		//$url = 'http://api.ipstack.com/'.$ip.'?access_key=e94099df11399ecea810e293f05261b3&format=1';
		$url = 'http://ip-api.com/json/'.$ip;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		curl_close ($ch);

		$server_output = json_decode($server_output);
		return response()->json($server_output);
	}

    public function trackSource()
    {
        $keyword = $this->request->project_id;
        $project = Project::where('ucode', $keyword)->first();

        $source = $project->sources->where('id', request('source_id'))->first();
        $source->pivot->increment('count');
    }

    /* Quiz */

    public function quizLog()
    {
        $project = Project::where('ucode', request('project_id'))->first();
        
        $participant = Participant::firstOrNew(['sessid' => $this->request->sessid, 'project_id' => $project->id]);
        $participant->save();

        $answer = QuizAnswer::find(request('answer'));
        $answer->increment('total_answers');

		$log = new QuizResult();
		$log->question_id = $answer->question_id;
		$log->answer_id = $answer->id;
		//$log->is_correct = request('is_correct');
        $log->details = $this->request->input('details') ? json_encode($this->request->input('details')) : null;
        $log->source_id = $this->request->source_id ?? null;
        $log->source_value = $this->request->source_value ?? null;
        $log->source_campaign = $this->request->source_campaign ?? null;
        $log->user_agent = json_encode($this->request->user_agent);
        $log->sessid = request('sessid');
        $log->project_id = $project->id;
        $log->user_id = $participant->id;
		$log->save();

		
    }

    public function quizRandom()
    {
        $validated = $this->request->validate([
            'filter.*' => 'in:source_value,source_id,language,country',
            'filter.source_value' => 'string',
            'filter.source_id' => 'string',
            'filter.language' => 'string',
            'filter.country' => 'string',
            'filter' => 'array',
            'not__in' => 'array',
            'skip' => 'integer',
            'limit' => 'integer',
            'tags' => 'string'
        ]);

        $project = Project::where('ucode', request('project_id'))->first();

        $question = $project->quiz();
        
        if($this->request->input('filter')):
            foreach($this->request->input('filter') as $key => $value):
                $question = $question->where($key, $value);
            endforeach;
        endif;

        if(request('tags')):
            $question = $question->where('tags', 'like', '%'.request('tags').'%');
        endif;

        if(request('not__in')):
            $question = $question->whereNotIn('id', request('not__in'));
        endif;

        if(request('limit')):
            $limit = request('limit') == -1 ? PHP_INT_MAX : request('limit');
            $question = $question->take($limit);
        endif;

        if(request('skip')):
            $question = $question->skip(request('skip'));
            if(!$this->request->has('limit')):
                $question = $question->take(PHP_INT_MAX);
            endif;
        endif;
        
        $question = $question->with(['answers' => function($q){
            $q->inRandomOrder();
        }])->get();
        $questionRand = $question->random();

        if($question):
            return [
                'question' => $questionRand->only(['id','title','answers','is_always_correct','is_multi_answer','if_correct','if_incorrect']),
                'total' => $question->count(),
                'answered' => request('not__in') ? count(request('not__in')) : 0
            ];
        else:
            abort(404, 'Oops... Not found!');
        endif;
    }

    public function quizAll()
    {
        $validated = $this->request->validate([
            'filter.*' => 'in:source_value,source_id,language,country',
            'filter.source_value' => 'string',
            'filter.source_id' => 'string',
            'filter.language' => 'string',
            'filter.country' => 'string',
            'filter' => 'array',
            'not__in' => 'array'
        ]);

        $project = Project::where('ucode', request('project_id'))->first();

        $question = $project->quiz();
        
        if($this->request->input('filter')):
            foreach($this->request->input('filter') as $key => $value):
                $question = $question->where($key, $value);
            endforeach;
        endif;

        if(request('tags')):
            $question = $question->where('tags', 'like', '%'.request('tags').'%');
        endif;

        if(request('random')):
            $question = $question->with(['answers' => function($a){
                $a->inRandomOrder();
            }]);
        else:
            $question = $question->with('answers');
        endif;
        
        $question = $question->get();

		return [
            'question' => $question,
            'total' => $question->count()
        ];
    }

    public function quizNext()
    {
        $validated = $this->request->validate([
            'id' => 'required|integer',
            'filter.*' => 'in:source_value,source_id,language,country',
            'filter.source_value' => 'string',
            'filter.source_id' => 'string',
            'filter.language' => 'string',
            'filter.country' => 'string',
            'filter' => 'array',
            'not__in' => 'array'
        ]);

        $project = Project::where('ucode', request('project_id'))->first();

        $next = $project->quiz()->where('id', '>', request('id'))->min('id');
        $question = $project->quiz();
        
        if($this->request->input('filter')):
            foreach($this->request->input('filter') as $key => $value):
                $question = $question->where($key, $value);
            endforeach;
        endif;

        if(request('tags')):
            $question = $question->where('tags', 'like', '%'.request('tags').'%');
        endif;
        
        $question->with('answers')->find($next);

		return [
            'question' => $question,
			'next' => $next ? true : false
            //'answers' => $question->answers()->inRandomOrder()->get()
        ];
    }

    /* Members */

    public function authResetPassword()
    {
        $validated = $this->request->validate([
            'email' => 'required|email'
        ]);

        $project = Project::where('ucode', request('project_id'))->first();
        $user = Member::whereProjectId($project->id)->whereEmail(request('email'))->first();
        $password = Str::random(8);

        if($user):
            $user->password = Hash::make($password);
            $user->save();

            $author = request('from');
            $subject = request('subject');
            $tpl = request('tpl');

            Mail::to($user->email)->send(new ResetPassword($user, $password, $project, $author, $subject, $tpl));
            return response()->json(['success' => true]);
        else:
            return response()->json(['success' => false]);
        endif;
    }

    public function authLoginOrNew()
    {
        $project = Project::where('ucode', request('project_id'))->first();
        $member = Member::whereProjectId($project->id)->whereEmail(request('email'))->first();

        $participant = Participant::firstOrNew(['sessid' => $this->request->sessid, 'project_id' => $project->id]);
        $participant->profile = $this->request->input('user') ? json_encode($this->request->input('user')) : null;
        $participant->ip_address = $this->request->ip();
        $participant->user_agent = json_encode($this->request->user_agent);
        $participant->source_id = $this->request->source_id ?? null;
        $participant->source_value = $this->request->source_value ?? null;
        $participant->source_campaign = $this->request->source_campaign ?? null;
        $participant->save();

        $member = Member::firstOrNew(['project_id' => $project->id, 'email' => request('email')]);
        $member->name = request('name') ?? null;
        $member->password = request('password') ? Hash::make(request('password')) : null;
        $member->details = request('details') ? json_encode(request('details')) : json_encode(array());
        $member->participant_id = $participant->id;
        $member->save();

        return ['success' => true, 'profile' => $member->only(['id','name','email','details','participant_id'])];
    }

    public function authRegister()
    {
        $project = Project::where('ucode', request('project_id'))->first();
        $member = Member::whereProjectId($project->id)->whereEmail(request('email'))->first();

        if($member):
            return ['success' => false, 'message' => 'Member already exists'];
        endif;

        $participant = Participant::firstOrNew(['sessid' => $this->request->sessid, 'project_id' => $project->id]);
        $participant->profile = $this->request->input('user') ? json_encode($this->request->input('user')) : null;
        $participant->ip_address = $this->request->ip();
        $participant->user_agent = json_encode($this->request->user_agent);
        $participant->source_id = $this->request->source_id ?? null;
        $participant->source_value = $this->request->source_value ?? null;
        $participant->source_campaign = $this->request->source_campaign ?? null;
        $participant->save();

        $member = new Member;
        $member->name = request('name') ?? null;
        $member->email = request('email');
        $member->password = request('password') ? Hash::make(request('password')) : null;
        $member->details = request('details') ? json_encode(request('details')) : json_encode(array());
        $member->project_id = $project->id;
        $member->participant_id = $participant->id;
        $member->save();

        return ['success' => true, 'profile' => $member->only(['id','name','email','details','participant_id'])];
    }

    public function authUpdate()
    {
        $project = Project::where('ucode', request('project_id'))->first();
        $member = Member::whereProjectId($project->id)->whereEmail(request('email'))->first();

        if($member):

            $member->details = request('details') ? json_encode(request('details')) : json_encode(array());
            $member->save();

            return ['success' => true, 'profile' => $member->only(['id','name','email','details','participant_id'])];

        endif;

        return ['success' => false, 'message' => 'User not found'];
    }  

    public function authLogin()
    {
        $project = Project::where('ucode', request('project_id'))->first();
        $member = Member::whereProjectId($project->id)->whereEmail(request('email'))->first();

        if($member):

            if($member->password):
                if (Hash::check(request('password'), $member->password)):
                    session(['simple_api_profile' => $member->only(['id','name','email','details','participant_id'])]);
                    return ['success' => true, 'profile' => $member->only(['id','name','email','details','participant_id'])];
                else:
                    return ['success' => false, 'message' => 'Passowrd is not valid'];
                endif;
            else:
                Session::put('hello','test');
                #Session::put('hello', json_encode($member->only(['id','name','email','details','participant_id'])));
                return ['success' => true, 'profile' => $member->only(['id','name','email','details','participant_id'])];
            endif;
            
        endif;

        return ['success' => false, 'message' => 'Member does not exist'];
    }

    /* /members */

    public function logAction()
    {
        $validated = $this->request->validate([
            'name' => 'required',
            'project_id' => 'required'
        ]);

        $keyword = $this->request->project_id;
        $project = Project::where('ucode', $keyword)->first();
        
        $participant = Participant::firstOrNew(['sessid' => $this->request->sessid, 'project_id' => $project->id]);
        $participant->profile = $this->request->input('user') ? json_encode($this->request->input('user')) : null;
        $participant->ip_address = $this->request->ip();
        $participant->user_agent = json_encode($this->request->user_agent);
        $participant->source_id = $this->request->source_id ?? null;
        $participant->source_value = $this->request->source_value ?? null;
        $participant->source_campaign = $this->request->source_campaign ?? null;
        $participant->save();

        // update last recorded time
        $prevLog = LogAction::whereSessid($this->request->sessid)->whereName('page_view')->orderBy('id','desc')->first();
        if($prevLog):
            $prevLog->updated_at = Carbon::now();
            $prevLog->save(); // increase updated_at to generate engament time
        endif;

        $log = new LogAction;
        $log->name = request('name');
        $log->values = json_encode(request('values'));
        $log->details = $this->request->input('details') ? json_encode($this->request->input('details')) : null;
        $log->project_id = $project->id;
        $log->source_id = $this->request->source_id;
        $log->source_value = $this->request->source_value;
        $log->source_campaign = $this->request->source_campaign;
        $log->sessid = $this->request->sessid;
        $log->user_id = $participant->id;
        $log->user_agent = json_encode($this->request->user_agent);
        $log->save();
    }

    public function qrDetails()
    {
        $validated = $this->request->validate([
            'ucode' => 'required'
        ]);

        $id = request('ucode');
        $code = QR::where('keyword', $id)->first();

        if($code):
            return $code->only('title','keyword','country','language','source_id','details');
        else:
            abort(404);
        endif;
    }

    public function selfieSubmit()
    {
        $validated = $this->request->validate([
            'image' => 'required',
            'project_id' => 'required'
        ]);

        $random = Str::random(10);
        $i18n = '';

        $image = $this->request->image;
        $keyword = $this->request->project_id;
        $project = Project::where('ucode', $keyword)->first();
        
        $language = $this->request->params ? $this->request->params->language : null;
        $country = $this->request->params ? $this->request->params->country : null;

        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = $random . '.png';

        Storage::disk('selfies')->put($imageName, base64_decode($image));

        if($language && $country){
            $i18n = '?locale='.$country.'_'.$language;
        }

        return [
            'uid' => $random,
            'image_url' => 'https://share.appetite.link/media/selfies/'.$imageName,
            'url' => 'https://share.appetite.link/'.$keyword.'/'.$random.$i18n
        ];
    }

    public function leaderboardSubmit()
    {
        $validated = $this->request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'score' => 'required|numeric|gte:0',
            'origin' => 'required',
            'origin_value' => 'required'
        ]);

        //$token = $this->request->number * time();
        //if($this->request->ts + 200000000 < $token):
        //    return ['error' => true, 'message' => 'Invalid token', 'ts' => $token, 'number' => $this->request->number];
        //endif;

        $count_entries = 0;
        $project = Project::where('ucode', $this->request->input('project_id'))->first();
        
        # update participant
        $participant = Participant::firstOrNew(['sessid' => $this->request->sessid, 'project_id' => $project->id]);
        $participant->profile = $this->request->input('user') ? json_encode($this->request->input('user')) : null;
        $participant->ip_address = $this->request->ip();
        $participant->user_agent = json_encode($this->request->user_agent);
        $participant->source_id = $this->request->source_id;
        $participant->source_value = $this->request->source_value;
        $participant->source_campaign = $this->request->source_campaign;
        $participant->save();

        # check if blocked
        if($this->request->input('blocked_emails')):
            if(in_array($this->request->input('email'), $this->request->input('blocked_emails'))):
                return ['error' => true, 'message' => 'E-mail is blocked'];
            endif;
        endif;

        # Rauch #
        if($project->id == 26):
            
            $current_origin = $this->request->input('origin_value');
            $origins = ['popup','quiz','share'];

            // if origin is not, return error
            if(!in_array($current_origin, $origins)):
                return ['error' => true, 'message' => 'Invalid origin'];
            endif;

            $sources = Leaderboard::where('project_id', $project->id)
                        ->where('email', $this->request->input('email'))
                        ->whereDate('created_at', Carbon::today())
                        ->whereIn('origin_value', [$current_origin])
                        ->get();
            
                        $count_entries = Leaderboard::where('project_id', $project->id)->where('email', $this->request->input('email'))->whereDate('created_at', Carbon::today())->get()->count();
            
            if($sources->count() >= 1):
                return [
                    'error' => true, 
                    'message' => 'You have reached the limit of entries per day',
                    'entries' => Leaderboard::where('project_id', $project->id)->where('email', $this->request->input('email'))->whereDate('created_at', Carbon::today())->get(['origin_value','score'])
                ];
            endif;

        endif;

        /*
        # check if limit reached
        $projects_to_limit = [3, 11]; // tetrapaks

        if(in_array($project->id, $projects_to_limit)):

            $emails_banned = ['raqueloc00@gmail.com','itahherrero@gmail.com','victoria_gs1@hotmail.es','aclara1@hotmail.es'];
            if(in_array($this->request->input('email'), $emails_banned)):
                return ['error' => true, 'message' => 'E-mail blocked'];
            endif;

            $ips_banned = ['207.188.139.43'];
            if(in_array($this->request->ip(), $ips_banned)):
                return ['error' => true, 'message' => 'IP blocked'];
            endif;

        endif;

        if($this->request->input('limit_entries') || in_array($project->id, $projects_to_limit)):
            $limit_entries = 50;
            $count_entries = Leaderboard::where('project_id', $project->id)->where('email', $this->request->input('email'))->whereDate('created_at', Carbon::today())->count();
            if($count_entries > $limit_entries):
                return ['error' => true, 'message' => 'Daily limit reached'];
            endif;
        endif;
        */

        # create Leaderboard
        $lb = new Leaderboard;
        $lb->user_id = $participant->id;
        $lb->name = $this->request->input('name');
        $lb->email = $this->request->input('email');
        $lb->participant = $this->request->input('user') ? json_encode($this->request->input('user')) : null;
        $lb->score = $this->request->input('score');
        $lb->details = $this->request->input('details') ? json_encode($this->request->input('details')) : null;
        $lb->origin = $this->request->input('origin');
        $lb->origin_value = $this->request->input('origin_value');
        $lb->source_id = $this->request->input('source_id');
        $lb->source_value = $this->request->input('source_value');
        $lb->source_campaign = $this->request->input('source_campaign');
        $lb->sessid = $this->request->input('sessid');
        $lb->project_id = $project->id;
        #$lb->save(); // don't save yet

        $originJson = json_encode(array(
            'score' => $this->request->input('score'),
            'game' => $this->request->input('origin_value')
        ));

        $log = new LogAction;
        $log->name = 'post_score';
        $log->values = $originJson;
        $log->project_id = $project->id;
        $log->source_id = $this->request->source_id;
        $log->source_value = $this->request->source_value;
        $log->source_campaign = $this->request->source_campaign;
        $log->sessid = $this->request->sessid;
        $log->user_id = $participant->id;
        $log->save();

        /* * Prize * */

        $prize_id = null;
        $prizeInput = $this->request->input('prize');

        # if we have a prize, let's run through this
        if($prizeInput):

            if(isset($prizeInput['id'])):
                $prize = Prize::findOrFail($prizeInput['id']);
                $prize_id = $prize->id;
            endif;

            if(isset($prizeInput['search'])):
                $prize = Prize::where('title','like','%'.$prizeInput['search'].'%')->whereProjectId($project->id)->first();
                $prize_id = $prize->id;
            endif;

            if(!isset($prizeInput['id']) && !isset($prizeInput['search'])):
                return [
                    'success' => false,
                    'prize' => [
                        'valid' => false,
                        'error' => 'We could not find a prize'
                    ]
                ];               
            endif;

            // validation scenarios
            $participation = isset($prizeInput['rules']) ? $prizeInput['rules'] : array();
            
            // a unique email can only participate once during whole campaign
            if(in_array('unique_email',$participation) && in_array('win_once',$participation) && in_array('lifetime',$participation)):
                if(Leaderboard::whereProjectId($project->id)->whereNotNull('prize_id')->whereEmail($this->request->input('email'))->first()):
                    return [
                        'success' => true,
                        'prize' => [
                            'valid' => false,
                            'error' => 'You already won a prize'
                        ]
                    ];
                endif;
            endif;

            // a unique email can only win once during active period
            if(in_array('unique_email',$participation) && in_array('win_once',$participation) && in_array('period',$participation)):
            endif;

            // we ran out of prizes
            if($prize && ($prize->count >= $prize->limit)):
                return [
                    'success' => true,
                    'prize' => [
                        'valid' => false,
                        'error' => 'We are out of prizes'
                    ]
                ];
            endif;

            // email template
            $mailInput = $prizeInput['mail'];

            if($mailInput['send']):
                $tpl = $mailInput['template'];
                Mail::to($this->request->input('email'))->send(new ApiSendMail( $mailInput['from'], $mailInput['subject'], $tpl ));
            endif;

            // increment
            $prize->increment('count');
                    
            # save leaderboard submission along prize data
		    $lb->prize_id = $prize_id;
            $lb->save();

            return [
                'success' => true,
                'prize' => [
                    'valid' => true
                ]
            ];
        
        else:

            # no prize, so let's save
            $lb->save();

        endif;

        if($this->request->input('limit_entries')):
            return ['success' => true, 'entries' => $count_entries];    
        endif;

        return ['success' => true];
    }

    public function leaderboardGet()
    {
        $project = Project::where('ucode', $this->request->input('project_id'))->first();
        
        $email = $this->request->input('email'); 
        $type = $this->request->input('type');
        $limit = $this->request->input('limit') ?? 5;
        $details = $this->request->input('details');
        $fields = $this->request->input('fields');
        
        $between = $this->request->input('between');
        $tz = $this->request->input('tz') ?? 'UTC';

        if($between):
            $betweenFrom = Carbon::parse($between[0], $tz);
            $betweenTo = Carbon::parse($between[1], $tz);
        endif;

        /* User Score */
        if($email):

            $userScore = Leaderboard::where('project_id', $project->id)->whereEmail($email);
            
            if($details):
                $userScore = $userScore->whereJsonContains('details', $details);
            endif; 
            
            if($between):
                $userScore = $userScore->whereBetween('created_at', [$betweenFrom, $betweenTo]);
            endif;
            
            if($type == 'highest'):
                $userScore = $userScore->orderBy('score','desc')->first()->score;
            else:
                $userScore = $userScore->sum('score');
            endif;

        endif;
                             
        /* Top */
        $top = Leaderboard::where('project_id', $project->id);

        if($type == 'highest'):
            $top = $top->select('email', 'name', 'details', 'user_id', DB::raw('max(score) as score'));
        else:
            $top = $top->select('email', 'name', 'details', 'user_id', DB::raw('sum(score) as score'));
        endif;

        if($between):
            $top = $top->whereBetween('created_at', [$betweenFrom, $betweenTo]);
        endif;

        if($details):
            $top = $top->whereJsonContains('details', $details);
        endif;  

        $top = $top->with(['user.member'])->orderBy('score', 'desc')->groupBy('email')->limit($limit)->get();
        $top = $top->map(function ($item) {
            return [
                'name' => data_get($item, 'name'),
                'email' => data_get($item, 'email'),
                'score' => data_get($item, 'score'),
                'details' => data_get($item, 'user.member.details')
            ];
        });

        /* rank */
        if($email):

            $rank = Leaderboard::where('project_id', $project->id);
            
            if($type == 'highest'):
                $rank = $rank->select('email', 'name', 'score', 'details');
            else:
                $rank = $rank->select('email', 'name', 'details', DB::raw('sum(score) as score'));
            endif;

            if($between):
                $rank = $rank->whereBetween('created_at', [$betweenFrom, $betweenTo]);
            endif;

            if($details):
                $rank = $rank->whereJsonContains('details', $details);
            endif;  

            $rank = $rank->groupBy('email')
                        ->orderBy('score', 'desc')
                        ->having('score', '>=', (int) $userScore)
                        ->get()
                        ->count();

        endif;
        /* return */
        
        return [
            'type' => $type == 'highest' ? 'highest' : 'sum',
            'user' => $email ? [
                'rank' => $rank == 0 ? 1 : $rank,
                'score' => $userScore,
            ] : [],
            'leaderboard' => $top,
            'between' => $between ? [
                $betweenFrom,
                $betweenTo
            ] : null
        ];

    }

    public function articlesView()
    {
        $project = Project::where('ucode', $this->request->input('project_id'))->first();
        $articles = Article::where('project_id', $project->id);
    
        if($this->request->input('filter')):
            foreach($this->request->input('filter') as $key => $value):
                $articles = $articles->where($key, $value);
            endforeach;
        endif;
    
        if($this->request->input('article_id')):
            $articles = $articles->where('id', $this->request->input('article_id'));
        elseif($this->request->input('slug')):
            $articles = $articles->where('slug', $this->request->input('slug'));
        elseif($this->request->input('title')):
            $articles = $articles->where('title', 'like', '%'.$this->request->input('title').'%');
        endif;
    
        $article = $articles->first();
    
        if($article):
            $article->increment('count');
            return ['success' => true, 'article' => $article];
        endif;
    }

    public function articlesAll()
    {
        $rules = [
            'filter.*' => 'in:source_value,source_id,language,country',
            'filter.source_value' => 'string',
            'filter.source_id' => 'string',
            'filter.language' => 'string',
            'filter.country' => 'string',
            'filter' => 'array',
            'tags' => 'string',
            'title' => 'string',
            'limit' => 'integer',
            'order' => 'in:asc,desc',
        ];
        
        $validator = Validator::make($this->request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $project = Project::where('ucode', $this->request->input('project_id'))->first();
        $articles = Article::where('project_id', $project->id);
        
        if($this->request->input('filter')):
            foreach($this->request->input('filter') as $key => $value):
                $articles = $articles->where($key, $value);
            endforeach;
        endif;

        if($this->request->input('limit')):
            $articles = $articles->limit($this->request->input('limit'));
        endif;

        if($this->request->input('title')):
            $articles = $articles->where('title', 'like', '%'.$this->request->input('title').'%');
        endif;

        if($this->request->input('tags')):
            $articles = $articles->where('tags', 'like', '%'.$this->request->input('tags').'%');
        endif;

        if($this->request->input('order') == 'asc' || $this->request->input('order') == 'desc'):
            $articles = $articles->orderBy('created_at', $this->request->input('order'));
        endif;

        if($this->request->input('order') == 'name-asc' || $this->request->input('order') == 'name-desc'):
            $articles = $articles->orderBy('name', $this->request->input('order') == 'name-asc' ? 'asc' : 'desc');
        endif;

        $articles = $articles->get();

        return $articles;
    }

    public function crmContact()
    {
        $project = Project::where('ucode', $this->request->input('project_id'))->first();
        $crm = request('crm');
        $email = $crm['email'];
        $platform = $crm['platform'];
        $name = $crm['name'];

        if($platform == 'salesmanago'){

            $apiUrl = "https://www.salesmanago.pl/api/contact/upsert";
                        
            $data = [
                "clientId" => "iek415ttonig7c9b",
                "apiKey" => "vS5xJtNkrit9dL7X6d7f",
                "sha" => "8ef232dfe52aecbde72a63628388b14fc32810c4",
                "requestTime" => time() * 1000,
                "owner" => "salesmanago@donsimon.com",
                "contact" => [
                    "email" => $email,
                    "name" => $name,
                ],
                "forceOptIn" => true,
                "tags" => [
                    "CONNECTED_EXPERIENCE",
                    "ELOPAK"
                ]
            ];

            return Http::post($apiUrl, $data);

        }

        return ['success' => false];
    }

    public function array2csv ($array) {
        $csv = '';
        foreach ($array as $item) {
            $csv .= implode(';', $item) . "\n";
        }
        return $csv;
    }

    public function qrcodesViews($project) {

        //AlphaNumCode::truncate();
        
        $type = request()->input('type');
        $filter = request()->input('filter');
        $page = request()->input('page');
        $limit = request()->input('limit', 1000);
        
        $query = QR::where('project_id', $project)
                    ->where('is_unique', true)
                    ->when($filter, function ($query, $filter) {
                        return $query->where('title', 'like', '%' . $filter . '%');
                    });
        
        if ($page !== null) {
            $codes = $query->paginate($limit, ['*'], 'page', $page)->items();
        } else {
            $codes = $query->get()->toArray();
        }
        
        $codes = array_map(function ($code) {
            return [
                'url' => route('go.redirect', $code['keyword']),
                'image' => 'https://query.appetitecreative.com/v2/public/qr?url=' . route('go.redirect', $code['keyword'])
            ];
        }, $codes);
        
        if ($type == 'array') {
            return response()->json($codes, 200, [], JSON_UNESCAPED_SLASHES);
        }

        if ($type == 'count') {
            return response()->json(
                [
                    'filter' => $filter,
                    'count' => count($codes)
                ]
            );
        }
        
        if ($type == 'csv') {
            return response()->streamDownload(function () use ($codes) {
                echo $this->array2csv($codes);
            }, 'codes.csv');
        }        

        if ($type == 'txt') {
            // should print url and image
            return response()->streamDownload(function () use ($codes) {
                echo $this->array2csv($codes);
            }, 'codes.txt');
        }
        
    }

    public function alphaNumView($project) {

        //AlphaNumCode::truncate();
        
        $type = request()->input('type');
        $filter = request()->input('filter');
        
        $codes = AlphaNumCode::where('project_id', $project)
            ->where('burned', false)
            ->when($filter, function ($query, $filter) {
                return $query->where('title', 'like', '%' . $filter . '%');
            })
            ->inRandomOrder()
            ->pluck('code')
            ->toArray();

        if ($type == 'array') {
            return response()->json($codes);
        }

        if ($type == 'count') {
            return response()->json(
                [
                    'filter' => $filter,
                    'count' => count($codes)
                ]
            );
        }

        if ($type == 'csv') {
            // Ensure each code is wrapped in ="CODE" to be recognized as a string by Excel
            $formattedCodes = array_map(function ($code) {
                return '="' . $code . '"';
            }, $codes);
        
            $codesString = implode("\n", $formattedCodes);
        
            return response()->streamDownload(function () use ($codesString) {
                echo $codesString;
            }, 'codes.csv');
        }
        

        if ($type == 'txt') {
            $codes = implode("\n", $codes);
            return response()->streamDownload(function () use ($codes) {
                echo $codes;
            }, 'codes.txt');
        }
        
    }
}