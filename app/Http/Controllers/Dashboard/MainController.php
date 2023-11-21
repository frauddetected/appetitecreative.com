<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use Auth;
use App;
use DB;
use Illuminate\Support\Facades\Cache;

use HiFolks\Statistics\Stat;
use HiFolks\Statistics\Freq;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\AuthenticationLog;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Rap2hpoutre\FastExcel\FastExcel;

use Analytics;
use App\Models\Prize;
use App\Models\PrizeWinner;
use App\Models\Note;
use Carbon\Carbon;
use Spatie\Analytics\Period;
use Schema;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{
    public function __construct()
    {
        $this->middleware('project.access');
    }

    public function index()
    {
        $data['project'] = current_project();
        return Inertia::render('Dashboard/Index', $data);
    }

    public function activity()
    {

        /*
        Schema::table('quiz_questions', function ($table) {
            $table->json('tags')->nullable()->after('is_multi_answer');
        });
        die;
        */

        $data['auth'] = AuthenticationLog::orderBy('id','desc')->with('user')->limit(20)->get();
        return Inertia::render('Dashboard/Activity', $data);
    }

    public function debug()
    {
        return Inertia::render('Dashboard/Debug');
    }

    public function exportEmails()
    {

    }

    public function exportLeaderboard()
    {

    }

    public function exportDaily()
    {

    }

    public function exports()
    {
        
    }

    public function insights()
    {
        $ttl = 0; // for now

        $period = [
            'start' => Carbon::now()->subDays(7),
            'end' => Carbon::now(),
        ];

        $scans_count = current_project()->logs()->where('name','scan_qr')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
        $scans_avg_per_day = $scans_count / Carbon::now()->diffInDays($period['start']);
        
        $previous_period = [
            'start' => Carbon::now()->subDays(14),
            'end' => Carbon::now()->subDays(7),
        ];

        $previous_scans_count = Cache::remember("stats.scans.count", $ttl, function() use ($previous_period){
            return current_project()->logs()->where('name','scan_qr')->whereBetween('created_at', [$previous_period['start'], $previous_period['end']])->get()->count();
        });
        
        if ($previous_scans_count > 0) {
            $percentage_increase = ($scans_count - $previous_scans_count) / $previous_scans_count * 100;
        } else {
            $percentage_increase = 0;
        }

        $message = "Over the past 7 days there was an average of " . number_format($scans_avg_per_day) . " daily scans. ";
        if ($percentage_increase > 0) {
            $message .= "That represents an overall increase of " . number_format($percentage_increase, 2) . "% versus the previous week.";
        } else {
            $message .= "That represents an overall decrease of " . number_format(abs($percentage_increase), 2) . "% versus the previous week.";
        }
        
        $data = [
            'message' => $message,
            'scans_count' => $scans_count,
            'scans_avg_per_day' => $scans_avg_per_day,
            'percentage_increase' => $percentage_increase,
        ];

        return Inertia::render('Dashboard/Insights', ['data' => $data]);
    }

    public function insights2()
    {
        $data = array();
        return Inertia::render('Dashboard/Insights', $data);
    }

    public function prizes()
    {
        if(!project_module('prizes')) abort(404);

        $data['prizes'] = current_project()->prizes;
        $data['prize'] = null;

        $data['period'] = [];
        $data['selectedDate'] = null;

        return Inertia::render('Dashboard/Prizes', $data);
    }

    public function participantUpdate()
    {
        # nothing here
    }

    public function prizeWinner()
    {
        $prize = Prize::find(request('prize'));
        $p = $prize->winner()->where('period', request('date'))->firstOrNew();

        $leaderboard = current_project()
                       ->leaderboard()
                       ->whereDate('created_at', Carbon::parse(request('date')))
                       ->select('email', 'name', 'user_id', 'details', 'origin_value', 'source_value', DB::raw('sum(score) as score'))
                       ->groupBy('email')
                       ->inRandomOrder()
                       ->first();

        if($leaderboard): 

            $count = PrizeWinner::whereProjectId(current_project()->id)->whereUserId($leaderboard->user_id)->get()->count();
            if($count):
                return redirect()->back()->with('status', 'This user already won.');
            endif;

            $p->user_id = $leaderboard->user_id;
            $p->project_id = current_project()->id;
            $p->periodicity = $prize->periodicity;
            $p->period = request('date');

            if($p->save()) return redirect()->back()->with('status', 'Successfully updated prize winner.');

        endif;

        return redirect()->back()->with('status', 'Something went wrong!');
    }

    public function prizeWinnerDelete()
    {
        $wid = request('wid');
        $p = PrizeWinner::find($wid);
        $p->delete();

        return redirect()->back()->with('status', 'Winner deleted. Please pick another.');
    }

    public function analytics()
    {
        if(!project_module('analytics')) abort(404);

        if(request('period')):
            $period = explode("@", request('period'));
            $startDate = Carbon::parse($period[0]);
            $endDate = Carbon::parse($period[1])->endOfDay();
        else:
            $startDate = Carbon::now()->startOfDay()->subWeeks(1);
            $endDate = Carbon::now();
        endif;
        
        $data['period'] = [
            'start' => $startDate,
            'end' => $endDate
        ];

        $analytics = current_project()->analytics;

        if($analytics->platform == 'google'):

            $data['loaded'] = false;

           if(request('loader')):
            
                $analytics = Analytics::setViewId($analytics->details->view_id);

                $data['map'] = current_project()->analytics->details->map ? true : false;
                $data['loaded'] = true;

                $period = Period::create($startDate, $endDate);

                $visitedpages = $analytics->fetchTotalVisitorsAndPageViews($period);

                $pages = [ 'name' => 'Pages', 'data' => array() ];
                $visitors = [ 'name' => 'Visitors',  'data' => array() ];
        
                foreach($visitedpages as $vp){
                    $pages['data'][$vp['date']->format('Y-m-d\TH:i:s.uP')] = $vp['pageViews'];
                    $visitors['data'][$vp['date']->format('Y-m-d\TH:i:s.uP')] = $vp['visitors'];
                }
        
                $data['visitedpages'] = array($pages, $visitors);

                $stats = Analytics::performQuery(
                    $period,
                    'ga:users,ga:sessions,ga:pageviews,ga:avgSessionDuration,ga:bounceRate'
                );

                $cities = Analytics::performQuery(
                    $period,
                    'ga:sessions',
                    [
                        'dimensions' => 'ga:country,ga:city',
                        'sort' => '-ga:sessions'
                    ]
                );

                $countries = Analytics::performQuery(
                    $period,
                    'ga:sessions',
                    [
                        'dimensions' => 'ga:country',
                        'sort' => '-ga:sessions'
                    ]
                );

                $referrals = Analytics::performQuery(
                    $period,
                    'ga:sessions,ga:pageviews,ga:avgSessionDuration,ga:exits',
                    [
                        'dimensions' => 'ga:source',
                        'sort' => '-ga:sessions'
                    ]
                );

                $os = Analytics::performQuery(
                    $period,
                    'ga:sessions',
                    [
                        'dimensions' => 'ga:operatingSystem',
                        'sort' => '-ga:sessions'
                    ]
                );

                $heatmap = Analytics::performQuery(
                    $period,
                    'ga:sessions',
                    [
                        'dimensions' => 'ga:hour,ga:dayOfWeekName',
                        'sort' => 'ga:hour'
                    ]
                );

                $heatmap_array = [];
                foreach($heatmap->rows as $row){
                    $heatmap_array[$row[1]][] = [
                        'hour' => $row[0],
                        'count' => $row[2]
                    ];
                }
                
                $data['analytics'] = [
                    'stats' => $stats->totalsForAllResults,
                    'cities' => [
                        'rows' => $cities->rows,
                        'total' => $cities->totalsForAllResults['ga:sessions']
                    ],
                    'countries' => [
                        'rows' => $countries->rows,
                        'total' => $countries->totalsForAllResults['ga:sessions']
                    ],
                    'heatmap' => $heatmap_array,
                    'referrals' => [
                        'rows' => $referrals->rows,
                        'total' => $referrals->totalsForAllResults['ga:sessions']
                    ],
                    'os' => [
                        'rows' => $os->rows,
                        'total' => $os->totalsForAllResults['ga:sessions']
                    ],
                ];

            endif;

            return Inertia::render('Dashboard/Analytics/Google', $data);

        else:
            
            $data['analytics'] = $analytics;
            return Inertia::render('Dashboard/Analytics/Plausible', $data);
            
        endif;
    }

    function notes(){

        $data['notes'] = current_project()->notes()->whereNotNull('title')->with('user')->get();
        
        if(request()->isMethod('post')):
            $validator = Validator::make(request()->all(), [
                'title' => ['required'],
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator) // Send validation errors to the view
                    ->withInput(); // Preserve the input data in the form
            }

            $note = new Note;
            $note->title = request('title');
            $note->content = request('content');
            $note->project_id = current_project()->id;
            $note->user_id = Auth::user()->id;

            if($note->save()){
                return redirect()->back()->with('status','Note created Successfully.');
            }

        endif;
        
        return Inertia::render('Dashboard/Notes', $data);
        
    }

    function charts($type)
    {
        if(request('period')):
            $periodParam = explode("@", request('period'));
            $startDate = Carbon::parse($periodParam[0]);
            $endDate = Carbon::parse($periodParam[1])->endOfDay();
        else:
            $startDate = Carbon::now()->startOfDay()->subWeeks(1);
            $endDate = Carbon::now();
        endif;
        
        $data['period'] = [
            'start' => $startDate,
            'end' => $endDate
        ];

        switch ($type):
            case 'cities':
                
                $analytics = current_project()->analytics;
                $analytics = Analytics::setViewId($analytics->details->view_id);
                $period = Period::create($startDate, $endDate);

                $cities = Analytics::performQuery(
                    $period,
                    'ga:sessions',
                    [
                        'dimensions' => 'ga:country,ga:city,ga:region',
                        'sort' => '-ga:sessions'
                    ]
                );

                if(request('export')):

                    $filename = "cities_period_$periodParam[0]_$periodParam[1].xlsx";

                    function cityGenerator($cities){
                        foreach ($cities as $c){
                            yield $c;
                        }
                    }

                    return (new FastExcel(cityGenerator($cities)))->download($filename, function ($item) {
            
                        $export['Country'] = $item[0];
                        $export['City'] = $item[1];
                        $export['Region'] = $item[2];
                        $export['Sessions'] = $item[3];
                        
                        return $export;
            
                    });

                endif;

                return ['cities' => $cities];

            break;
        endswitch;
    }

}