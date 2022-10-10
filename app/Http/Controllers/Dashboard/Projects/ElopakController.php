<?php

namespace App\Http\Controllers\Dashboard\Projects;

use Inertia\Inertia;
use Auth;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use HiFolks\Statistics\Stat;
use HiFolks\Statistics\Freq;

use App\Models\Project;
use App\Models\User;
use App\Models\Leaderboard;
use App\Models\Quiz;
use App\Models\QuizResult;

use App\Models\QR;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Cache;
use Rap2hpoutre\FastExcel\FastExcel;

use App\Http\Controllers\Dashboard\MainController;
use Symfony\Component\VarDumper\VarDumper;

class ElopakController extends MainController
{
    public function index()
    {
        if(request('period')):
            $period = explode("@", request('period'));
            $startDate = Carbon::parse($period[0]);
            $endDate = Carbon::parse($period[1])->endOfDay();
        else:
            $startDate = Carbon::now()->startOfDay()->subWeeks(1);
            $endDate = Carbon::now();
        endif;

        $data['project'] = current_project();
        $data['period'] = $period = [
            'start' => $startDate,
            'end' => $endDate
        ];
        $data['qr'] = current_project()->qr;

        /* logic here */
        $cachingPeriod = current_project()->id.'-'.$startDate->format('Y-m-d').'-'.$endDate->format('Y-m-d');
        $ttl = 60 * 5; // 5 minutes

        /* grid: scans */
        if(request('grid') == 'scans'):

            $numberOfDays = $period['end']->diffInDays($period['start']);
            $scans = current_project()->logs()->where('name','scan_qr')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
            $users = current_project()
            ->participants()
            ->where('profile','!=',null)
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->get(array(
                DB::raw('COUNT(DISTINCT( JSON_EXTRACT(`profile` , \'$."email"\') )) as "views"')
            ))
            ->sum('views');
                        
            $sun=$scans/$users/$numberOfDays;
            $data['freqTable'] = ($sun > 5 ? '2.2' : ($sun < 1 ? '1.3' : number_format($sun,1)));
            
            $data['stats'] = [
                'scans' => [
                    'count' => Cache::remember("stats.scans.count|$cachingPeriod", $ttl, function() use ($period){
                        return current_project()->logs()->where('name','scan_qr')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                    }),
                    'avg_per_day' => Cache::remember("stats.per_day.count|$cachingPeriod", $ttl, function() use ($period){
                        $fetch = current_project()
                        ->logs()
                        ->where('name','scan_qr')
                        ->where('sessid','!=',null)
                        ->groupBy('sessid')
                        ->whereBetween('created_at', [$period['start'], $period['end']])
                        ->orderBy('views','desc')
                        ->get(array(
                            'sessid',
                            DB::raw('COUNT(*) as "views"')
                        ))->pluck('views')->toArray();
                        
                        return Stat::mean($fetch);
                    }),
                    'top' => Cache::remember("stats.scans.top|$cachingPeriod", $ttl, function() use ($period){
                        
                        $fetch = current_project()
                                ->logs()
                                ->where('name','scan_qr')
                                ->groupBy('values->ucode')
                                ->whereBetween('created_at', [$period['start'], $period['end']])
                                ->get(array(
                                    'values->ucode as ucode',
                                    DB::raw('COUNT(*) as "views"')
                                ));
                        
                        $arr = [];
                        foreach ($fetch as $f):
                            $qr = QR::whereKeyword($f->ucode)->first();
                            $qr['views'] = $f->views;
                            array_push($arr, $qr);
                        endforeach;

                        return collect($arr)->sortBy('views')->reverse()->values();
                    })
                ],
                'registrations' => [
                    'count' => Cache::remember("stats.registrations.count|$cachingPeriod", $ttl, function() use ($period){
                        return current_project()
                                ->participants()
                                ->where('profile','!=',null)
                                ->whereBetween('created_at', [$period['start'], $period['end']])
                                ->get(array(
                                    DB::raw('COUNT(DISTINCT( JSON_EXTRACT(`profile` , \'$."email"\') )) as "views"')
                                ))
                                ->sum('views');
                    })
                ]
            ];

            $packageChart = [];

            foreach($data['stats']['scans']['top'] as $c){
                $packageChart[$c->title] = $c->views;
            }  

            /* Scans vs Visitors vs Registrations */
            $scans = [ 'name' => 'Scans', 'data' => array() ];
            $registrations = [ 'name' => 'Registers', 'data' => array() ];

            $scans_daily = Cache::remember("stats.scans_daily.single|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()
                            ->logs()
                            ->where('name','scan_qr')
                            ->whereBetween('created_at', [$period['start'], $period['end']])
                            ->groupBy('date')
                            ->orderBy('date', 'ASC')
                            ->get(array(
                                DB::raw('Date(created_at) as date'),
                                DB::raw('COUNT(*) as "views"')
                            ));
            });

            /*
            $visitors_daily = Cache::remember("stats.visitors_daily.single|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()
                    ->participants()
                    ->whereBetween('created_at', [$period['start'], $period['end']])
                    ->groupBy('date')
                    ->orderBy('date', 'ASC')
                    ->get(array(
                        DB::raw('Date(created_at) as date'),
                        DB::raw('COUNT(*) as "views"')
                    ));
                });
            */

            $registrations_daily = Cache::remember("stats.registrations_daily.single|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()
                            ->participants()
                            ->where('profile->email','!=',null)
                            ->whereBetween('created_at', [$period['start'], $period['end']])
                            ->groupBy('date')
                            ->orderBy('date', 'ASC')
                            ->get(array(
                                DB::raw('Date(created_at) as date'),
                                DB::raw('COUNT(DISTINCT( JSON_EXTRACT(`profile` , \'$."email"\') )) as "views"')
                            ));
            });

            foreach($scans_daily as $sd){
                $scans['data'][ $sd['date'] ] = $sd['views'];
            }  
            /*
            foreach($visitors_daily as $sd){
                $visitors['data'][ $sd['date'] ] = $sd['views'];
            } 
            */     
            foreach($registrations_daily as $sd){
                $registrations['data'][ $sd['date'] ] = $sd['views'];
            } 

            $data['charts'] = [
                'skus' => $packageChart,
                'scans_scores' => array($scans, $registrations),
            ];
            
            return $data;

        endif;

        if(request('grid') == 'games'):
            $data['stats'] = [
                'game_start' => [
                    'count' => Cache::remember("stats.game_start.count|$cachingPeriod", $ttl, function() use ($period){
                        return current_project()->logs()->where('name','game_start')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                    }),
                    'top' => Cache::remember("stats.game_start.top|$cachingPeriod", $ttl, function() use ($period){
                        return current_project()->logs()->where('name','game_start')
                            ->groupBy('values->game_name')
                            ->whereBetween('created_at', [$period['start'], $period['end']])
                            ->orderBy('views','desc')
                            ->get(array(
                                'values->game_name as name',
                                DB::raw('COUNT(*) as "views"')
                            ));
                    })
                ],
                'game_end' => [
                    'count' => Cache::remember("stats.game_end.count|$cachingPeriod", $ttl, function() use ($period){
                        return current_project()->logs()->where('name','game_end')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                    }),
                    'top' => Cache::remember("stats.game_end.top|$cachingPeriod", $ttl, function() use ($period){
                        return current_project()->logs()->where('name','game_end')
                            ->groupBy('values->game_name')
                            ->whereBetween('created_at', [$period['start'], $period['end']])
                            ->orderBy('views','desc')
                            ->get(array(
                                'values->game_name as name',
                                DB::raw('COUNT(*) as "views"')
                            ));
                    })
                ],
            ];
            return $data;
        endif;

        if(request('grid') == 'levels'):
            $data['stats'] = [
                'level_start' => [
                    'count' => Cache::remember("stats.level_start.count|$cachingPeriod", $ttl, function() use ($period){
                        return current_project()->logs()->where('name','level_start')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                    }),
                    'top' => Cache::remember("stats.level_start.top|$cachingPeriod", $ttl, function() use ($period){
                        return current_project()->logs()->where('name','level_start')
                            ->groupBy('values->level_name')
                            ->whereBetween('created_at', [$period['start'], $period['end']])
                            ->orderBy('views','desc')
                            ->get(array(
                                'values->level_name as name',
                                DB::raw('COUNT(*) as "views"')
                            ));
                    })
                ],
                'level_end' => [
                    'count' => Cache::remember("stats.level_end.count|$cachingPeriod", $ttl, function() use ($period){
                        return current_project()->logs()->where('name','level_end')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                    }),
                    'top' => Cache::remember("stats.level_end.top|$cachingPeriod", $ttl, function() use ($period){
                        return current_project()->logs()->where('name','level_end')
                            ->groupBy('values->level_name')
                            ->whereBetween('created_at', [$period['start'], $period['end']])
                            ->orderBy('views','desc')
                            ->get(array(
                                'values->level_name as name',
                                DB::raw('COUNT(*) as "views"')
                            ));
                    })
                ],
            ];
            return $data;
        endif;

        if(request('grid') == 'profile'):

            $uniques = current_project()
                        ->members()
                        ->groupBy('email')
                        ->whereBetween('created_at', [$period['start'], $period['end']])
                        ->get(array(
                            'name',
                            'details->age as agerange',
                            'details->gender as gender'
                        ));

            $data['stats'] = [
                'users' => [
                    'gender' => Cache::remember("stats.users.gender|$cachingPeriod", $ttl, function() use ($uniques){
                        $gender = [];
                        foreach($uniques as $t):
                            array_push($gender, $t->gender);
                        endforeach;
                        return array_count_values($gender);
                    }),
                    'age' => Cache::remember("stats.users.age|$cachingPeriod", $ttl, function() use ($uniques){
                            $agerange = [];
                            foreach($uniques as $t):
                                array_push($agerange, $t->agerange);
                            endforeach;
                            return array_count_values($agerange);
                    }),
                    'top_coins' => Cache::remember("stats.users.coins|$cachingPeriod", $ttl, function() use ($period){
                        
                        return current_project()->members()
                            ->groupBy('email')
                            ->whereBetween('created_at', [$period['start'], $period['end']])
                            ->orderBy('details->coins','desc')
                            ->get(array(
                                'name',
                                'email',
                                'details->coins as coins'
                            ));

                    }),
                    'avg_plays' => Cache::remember("stats.users.avg_plays|$cachingPeriod", $ttl, function() use ($period){
                        
                        $gamesPlayed = current_project()->logs()
                                ->where('name','game_start')
                                ->whereBetween('created_at', [$period['start'], $period['end']])
                                ->get()
                                ->count();
                        $numberOfDays = $period['end']->diffInDays($period['start']);
                        $participants = current_project()->participants()->where('profile','!=',null)->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
    
                        return [
                            'gamesParticipants' => $participants ? $gamesPlayed / $participants : 0,
                            'gamesDays' => $gamesPlayed / ($numberOfDays+1),
                        ];
    
                    })
                ]
            ];

            /*
            
            $ageArray = [];

            foreach( $data['stats']['users']['age'] as $profile ){
                if($profile->age) $ageArray[$profile->age] = $profile->total;
            }
            
          
            $genderArray = [];

            foreach( $data['stats']['users']['gender'] as $profile ){
                if($profile->gender) $genderArray[$profile->gender] = $profile->total;
            }

            $data['charts'] = [
                'age' => $ageArray,
                'gender' => $genderArray,
            ];
            */

            return $data;

        endif;

        if(request('grid') == 'share'):
            $data['stats'] = [
                'share' => [
                    'count' => Cache::remember("stats.share.count|$cachingPeriod", $ttl, function() use ($period){
                        return current_project()->logs()->whereBetween('created_at', [$period['start'], $period['end']])->where('name','share')->get()->count();
                    }),
                    'top' => Cache::remember("stats.share.top|$cachingPeriod", $ttl, function() use ($period){
                        return current_project()->logs()->where('name','share')
                                ->groupBy('values->method')
                                ->orderBy('views','desc')
                                ->whereBetween('created_at', [$period['start'], $period['end']])
                                ->get(array(
                                    'values->method as method',
                                    DB::raw('COUNT(*) as "views"')
                                ));
                    }),
                ]
            ];
            return $data;
        endif;

        if(request('grid') == 'quiz'):
            $data['stats'] = [
                'quiz' => [
                    'count' => Cache::remember("stats.users.quiz_count|$cachingPeriod", $ttl, function() use ($period){
                        
                        $total = current_project()->logs()
                                ->where('name','quiz_answer')
                                ->whereBetween('created_at', [$period['start'], $period['end']])
                                ->get()
                                ->count();

                        return [
                            'total' => $total,
                        ];
    
                    }),
                    'top' => Cache::remember("stats.users.quiz_top|$cachingPeriod", $ttl, function() use ($period){
                        
                        $top = current_project()->logs()
                                ->where('name','quiz_answer')
                                ->whereBetween('created_at', [$period['start'], $period['end']])
                                ->groupBy('values->question')
                                ->orderBy('views','desc')
                                ->get(array(
                                    'values->question as name',
                                    DB::raw('COUNT(*) as "views"')
                                ));

                        return $top;
    
                    }),
                    'top_answers' => Cache::remember("stats.users.quiz_top_answers|$cachingPeriod", $ttl, function() use ($period){
                        
                       // show top answers for each question
                       // include total number of answers for each question
                       // include question text

                       $top = current_project()->logs()
                                ->where('name','quiz_answer')
                                ->whereBetween('created_at', [$period['start'], $period['end']])
                                ->groupBy('values->question')
                                ->orderBy('views','desc')
                                ->get(array(
                                    'values->question as name',
                                    DB::raw('COUNT(*) as "views"')
                                ));

                        $topAnswers = [];

                        foreach($top as $t):
                            $answers = current_project()->logs()
                                ->where('name','quiz_answer')
                                ->whereBetween('created_at', [$period['start'], $period['end']])
                                ->where('values->question',$t->name)
                                ->groupBy('values->answer')
                                ->orderBy('count','desc')
                                ->get(array(
                                    'values->answer as name',
                                    DB::raw('COUNT(*) as "count"')
                                ));

                                // append 
                            array_push($topAnswers, [
                                'question' => $t->name,
                                'count' => $t->views,
                                'answers' => $answers
                            ]);
                        endforeach;

                        return $topAnswers;
    
                    }),
                ]
            ];
            return $data;
        endif;

        /* end logic here */

        $data['stats'] = [];
        $data['charts'] = [];

        return Inertia::render('Dashboard/Projects/Elopak/Index', $data);
    }
}