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

class TetrapakController extends MainController
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

        //adults
        if(current_project()->id == 3):

            if(request('lbperiod') == 'jan-feb'):
                $data['between'] = $between = [
                    'start' => Carbon::parse('jan 17, 2022'),
                    'end' => Carbon::parse('mar 3, 2022')
                ];
            elseif(request('lbperiod') == 'mar-apr'):
                $data['between'] = $between = [
                    'start' => Carbon::parse('mar 4, 2022'),
                    'end' => Carbon::parse('apr 18, 2022')
                ];
            elseif(request('lbperiod') == 'apr-jun'):
                $data['between'] = $between = [
                    'start' => Carbon::parse('apr 18, 2022'),
                    'end' => Carbon::parse('jun 8, 2022')
                ];
            else:
                $data['between'] = $between = [
                    'start' => Carbon::parse('jun 8, 2022'),
                    'end' => Carbon::parse('jul 27, 2022')
                ];
            endif;
        
        //kids
        elseif(current_project()->id == 11):

            if(request('lbperiod') == 'jan-feb'):
                $data['between'] = $between = [
                    'start' => Carbon::parse('jan 13, 2022'),
                    'end' => Carbon::parse('feb 28, 2022')
                ];
            elseif(request('lbperiod') == 'mar-apr'):
                $data['between'] = $between = [
                    'start' => Carbon::parse('mar 1, 2022'),
                    'end' => Carbon::parse('apr 15, 2022')
                ];
            elseif(request('lbperiod') == 'apr-may'):
                $data['between'] = $between = [
                    'start' => Carbon::parse('apr 15, 2022'),
                    'end' => Carbon::parse('may 20, 2022')
                ];
            elseif(request('lbperiod') == 'may-jul'):
                $data['between'] = $between = [
                    'start' => Carbon::parse('may 21, 2022'),
                    'end' => Carbon::parse('jul 4, 2022')
                ];
            elseif(request('lbperiod') == 'jul-jul'):
                $data['between'] = $between = [
                    'start' => Carbon::parse('jul 5, 2022'),
                    'end' => Carbon::parse('jul 31, 2022')
                ];
            else:
                $data['between'] = $between = [
                    'start' => Carbon::parse('jul 5, 2022'),
                    'end' => Carbon::parse('jul 31, 2022')
                ];
            endif;

        endif;

        if(request('feed')):

            $get = current_project()->leaderboard()->where('origin_value','quiz game')->where('score','>',40)->get();
            foreach($get as $a):
                $a->score = 40;
                $a->save();
            endforeach;

            return;

        endif;

        /*
        DATA FEED

        if(request('feed')):
            echo '<div class="p-8 flex flex-col justify-center bg-red-100">';
            echo '
            <form method="post" action="/?feed=1">
                '.csrf_field().'
                <input type="text" name="countStart" placeholder="Start"> 
                <input type="text" name="countFinished" placeholder="Finish"> 
                <input type="text" name="from" placeholder="jan 1, 2022" value="'.$startDate.'"> 
                <button class="bg-primary p-3 text-white" type="submit">Submit</button>
            </form>
            ';
            if(request()->isMethod('post')):

                $countStart = request('countStart');
                $countFinished = request('countFinished');

                $count = ($countFinished * 3) - ($countFinished-$countStart) * 1;    
                $project = current_project();
    
                for ($i=1; $i <= $count; $i++) { 
                    
                    $startDate  = Carbon::parse(request('from'))->startOfDay();
                    $endDate    = Carbon::parse(request('from'))->endOfDay();
                    $randomDate = Carbon::createFromTimestamp(rand($endDate->timestamp, $startDate->timestamp))->format('Y-m-d H:i:s');        
    
                    $randomQuestion = Arr::random($questions);
                    $randomAnswer = 'ch'.rand(1,3);
                    $randomIsCorrect = $randomAnswer == $randomQuestion['answer'] ? "true" : "false";
    
                    $log = new \App\Models\LogAction;
                    $log->name = 'quiz_answer';
                    $log->values = json_encode(array(
                        'question' => $randomQuestion['q'],
                        'answer' => $randomQuestion[$randomAnswer],
                        'is_correct' => $randomIsCorrect
                    ));
                    $log->project_id = $project->id;
                    $log->source_id = 1;
                    $log->source_value = 'i9Fk1lZM';
                    $log->source_campaign = null;
                    $log->sessid = null;
                    $log->user_id = null;
                    $log->created_at = $randomDate;
                    $log->updated_at = $randomDate;
                    $log->save();
                }    
                echo 'A total of '.$count.' records have been faked on '.request('from');
            endif;
            echo '</div>';
        endif;
        */

        if(request('action') == 'delEntryLeaderboard'):
            $cp = current_project()->leaderboard()->whereId(request('id'))->delete();
            return ['success' => true];
        endif;

        if(request('action') == 'delAllEntries'):
            $cp = current_project()->leaderboard()->whereEmail(request('email'))->delete();
            return ['success' => true];
        endif;

        if(request('action') == 'delInvalidSource'):
            $cp = current_project()->leaderboard()->where('source_id', 4)->delete();
            return ['success' => true];
        endif;

        if(request()->isMethod('post')):
            if(request('type') == 'filter'):
                
                $email = request('email');

                $lb = current_project()->leaderboard()
                        ->where('origin_value','!=','Age')
                        ->whereBetween('created_at', [$between['start'], $between['end']])
                        ->where('email', $email)
                        ->orderBy('created_at','desc');
                
                if(request('incdeleted')):
                    $lb->withTrashed();
                endif;

                $lb = $lb->get();

                return [
                    'records' => $lb,
                    'score' => $lb->sum('score'),
                    'name' => $lb[0]->name
                ];
                
            endif;
        endif;

        $data['qr'] = current_project()->qr;

        if(isset(request('filter')['brand'])):
            $data['brand'] = $brand = QR::find( request('filter')['brand'] );
        else:
            $data['brand'] = $brand = "All";
        endif;
        
        $cachingPeriod = current_project()->id.'-'.$startDate->format('Y-m-d').'-'.$endDate->format('Y-m-d');
        $ttl = 60 * 5; // 5 minutes

        /* grid: scans */
        if(request('grid') == 'scans'):

            /*
            $freqTable = current_project()->logs()
                        //->where('name','scan_qr')
                        //->where('sessid','!=',null)
                        ->brand($brand)
                        ->groupBy('sessid')
                        ->whereDate('created_at', $startDate)->orderBy('views','desc')
                        ->get(array(
                            'sessid',
                            DB::raw('COUNT(*) as "views"')
                        ))
                        ->pluck('views')
                        ->toArray();
            */
            $numberOfDays = $period['end']->diffInDays($period['start']);
            $scans = current_project()->logs()->brand($brand)->where('name','scan_qr')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
            $users = current_project()
            ->participants()
            ->brand($brand)
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
                    'count' => Cache::remember("stats.scans.count|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        return current_project()->logs()->brand($brand)->where('name','scan_qr')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                    }),
                    'avg_per_day' => Cache::remember("stats.per_day.count|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        $fetch = current_project()
                        ->logs()
                        ->brand($brand)
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
                    'top' => Cache::remember("stats.scans.top|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        
                        $fetch = current_project()
                                ->logs()
                                ->brand($brand)
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
                    'count' => Cache::remember("stats.registrations.count|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        return current_project()
                                ->participants()
                                ->brand($brand)
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

            $scans_daily = Cache::remember("stats.scans_daily.single|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                    return current_project()
                            ->logs()
                            ->brand($brand)
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

            $registrations_daily = Cache::remember("stats.registrations_daily.single|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                    return current_project()
                            ->participants()
                            ->brand($brand)
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

        /* grid: quiz */
        if(request('grid') == 'quiz'):
        
            $data = [
                'questionsRes' => Cache::remember("grid.quiz.results|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                    return current_project()->quiz()->with('answers')->get()->where('total_answers_count','>',0);
                }),
                'users' => [
                    'uniques' => current_project()->quizResults()->distinct('user_id')->count(),
                    'repeat' => Cache::remember("grid.quiz.users.uniques|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        $test = current_project()->quizResults()
                                ->groupBy('user_id')
                                ->get();
                        $arr = [];
                        foreach ($test as $t):
                            $tt = current_project()
                                ->quizResults()
                                ->where('user_id', $t->user_id)
                                ->groupBy('question_id')
                                ->get(array(
                                    'question_id',
                                    DB::raw('count(*) as count'),
                                ))
                                ->pluck('count');
                            array_push($arr, $tt);
                        endforeach;
                        return Stat::mean(Arr::flatten($arr));
                    }),
                ],
                'answers' => [
                    'total' => QuizResult::count(),
                    'correct' => QuizResult::whereHas('answer', function ($query){
                                    $query->where('is_correct', true);
                                })->count(),
                    'incorrect' => QuizResult::whereHas('answer', function ($query){
                                    $query->where('is_correct', false);
                                })->count()
                ],
            ];
            return $data;
        endif;

        if(request('grid') == 'profile'):

            $uniques = current_project()
                        ->participants()
                        ->brand($brand)
                        ->where('profile','!=',null)
                        ->groupBy('profile->email')
                        ->whereBetween('created_at', [$period['start'], $period['end']])
                        ->get(array(
                            'profile->email as email',
                            'profile->agerange as agerange',
                            'profile->gender as gender'
                        ));

            $data['stats'] = [
                'users' => [
                    'gender' => Cache::remember("stats.users.gender|$cachingPeriod|$brand", $ttl, function() use ($uniques){
                        $gender = [];
                        foreach($uniques as $t):
                            if($t->email && $t->gender):
                                array_push($gender, $t->gender);
                            endif;
                        endforeach;
                        return array_count_values($gender);
                    }),
                    'age' => Cache::remember("stats.users.age|$cachingPeriod|$brand", $ttl, function() use ($uniques){
                            $agerange = [];
                            foreach($uniques as $t):
                                if($t->email && $t->agerange):
                                    array_push($agerange, $t->agerange);
                                endif;
                            endforeach;
                            return array_count_values($agerange);
                    }),
                    'avg_plays' => Cache::remember("stats.users.avg_plays|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        
                        $gamesPlayed = current_project()->logs()
                                ->brand($brand)
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

        /*
        $data['stats'] = [
            'users' => [
                'count' => Cache::remember("stats.users.count|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->logs()->whereBetween('created_at', [$period['start'], $period['end']])->where('source_id','1')->groupBy('sessid')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                }),
            ],
            'participants' => [
                'count' => Cache::remember("stats.participants.count|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->participants()->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                }),
                'uniques' => Cache::remember("stats.participants.uniques|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->participants()->where('profile','!=',null)->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                }),    
                'origin' => Cache::remember("stats.participants.origin|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->leaderboard()
                        ->whereBetween('created_at', [$period['start'], $period['end']])
                        ->groupBy('origin_value')
                        ->orderBy('views','desc')
                        ->get(array(
                            'origin',
                            'origin_value',
                            DB::raw('COUNT(*) as "views"')
                    ));
                })
            ],
        ];
        */

        if(request('grid') == 'share'):
            $data['stats'] = [
                'share' => [
                    'count' => Cache::remember("stats.share.count|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        return current_project()->logs()->brand($brand)->whereBetween('created_at', [$period['start'], $period['end']])->where('name','share')->get()->count();
                    }),
                    'top' => Cache::remember("stats.share.top|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        return current_project()->logs()->where('name','share')
                                ->brand($brand)
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

        if(request('grid') == 'games'):

            $data['stats'] = [
                'game_start' => [
                    'count' => Cache::remember("stats.game_start.count|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        return current_project()->logs()->brand($brand)->where('name','game_start')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                    }),
                    'top' => Cache::remember("stats.game_start.top|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        return current_project()->logs()->brand($brand)->where('name','game_start')
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
                    'count' => Cache::remember("stats.game_end.count|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        return current_project()->logs()->brand($brand)->where('name','game_end')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                    }),
                    'top' => Cache::remember("stats.game_end.top|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        return current_project()->logs()->brand($brand)->where('name','game_end')
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

        if(request('grid') == 'selfie'):
            
            $data['stats'] = [
                'selfie_select' => [
                    'count' => Cache::remember("stats.selfie_select.count|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        return current_project()->logs()->brand($brand)->whereBetween('created_at', [$period['start'], $period['end']])->where('name','selfie_select')->get()->count();
                    }),
                    'top' => Cache::remember("stats.selfie_select.top|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){                
                        return current_project()->logs()->brand($brand)->where('name','selfie_select')
                        ->groupBy('values->filter')
                        ->orderBy('views','desc')
                        ->whereBetween('created_at', [$period['start'], $period['end']])
                        ->get(array(
                            'values->filter as filter',
                            DB::raw('COUNT(*) as "views"')
                        ));
                    })
                ],
                'selfie_take' => [
                    'count' => Cache::remember("stats.selfie_take.count|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        return current_project()->logs()->brand($brand)->where('name','selfie_take')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                    }),
                    'top' => Cache::remember("stats.selfie_take.top|$cachingPeriod|$brand", $ttl, function() use ($period, $brand){
                        return current_project()->logs()->brand($brand)->where('name','selfie_take')
                            ->groupBy('values->filter')
                            ->whereBetween('created_at', [$period['start'], $period['end']])
                            ->orderBy('views','desc')
                            ->get(array(
                                'values->filter as filter',
                                DB::raw('COUNT(*) as "views"')
                            ));
                    }),
                ]
            ];

            return $data;

        endif;

        if(request('grid') == 'leaderboard'):

            $data['stats'] = [
                'leaderboard' => Cache::remember("stats.leaderboard.table|$cachingPeriod", 0, function() use ($between){
                
                    $project = current_project();
                    
                    $top = Leaderboard::where('project_id', $project->id);
                    $top = $top->select('email', 'name', 'details', DB::raw('sum(score) as score'));
                    $top = $top->whereBetween('created_at', [$between['start'], $between['end']]);
                    $top = $top->orderBy('score', 'desc')->groupBy('email')->limit(10)->get();
    
                    return $top;
    
                })
            ];

            /* Leaderboard */
            $leaderboard = Cache::remember("stats.leaderboard.single|$cachingPeriod", 0, function() use ($between){
                $lb = current_project()->leaderboard();
                $lb = $lb->select('email', 'name', 'details', DB::raw('sum(score) as score'));
                $lb = $lb->whereBetween('created_at', [$between['start'], $between['end']])->orderBy('score', 'desc')->groupBy('email')->limit(8)->get();
                return $lb;
            });

            $leaderboard_chart = [];
            $leaderboard_index = 0;
            
            foreach($leaderboard as $lb):
                $leaderboard_index++;
                $leaderboard_chart['#'.$leaderboard_index] = $lb->score;
            endforeach;

            $data['charts'] = [
                'leaderboard' => array_reverse($leaderboard_chart)
            ];

            //adults
            if(current_project()->id == 3):

                if(request('lbperiod') == 'jan-feb'):
                    $data['between'] = $between = [
                        'start' => Carbon::parse('jan 17, 2022'),
                        'end' => Carbon::parse('mar 3, 2022')
                    ];
                elseif(request('lbperiod') == 'mar-apr'):
                    $data['between'] = $between = [
                        'start' => Carbon::parse('mar 4, 2022'),
                        'end' => Carbon::parse('apr 18, 2022')
                    ];
                elseif(request('lbperiod') == 'apr-jun'):
                    $data['between'] = $between = [
                        'start' => Carbon::parse('apr 18, 2022'),
                        'end' => Carbon::parse('jun 8, 2022')
                    ];
                else:
                    $data['between'] = $between = [
                        'start' => Carbon::parse('jun 8, 2022'),
                        'end' => Carbon::parse('jul 27, 2022')
                    ];
                endif;
            
            //kids
            elseif(current_project()->id == 11):

                if(request('lbperiod') == 'jan-feb'):
                    $data['between'] = $between = [
                        'start' => Carbon::parse('jan 13, 2022'),
                        'end' => Carbon::parse('feb 28, 2022')
                    ];
                elseif(request('lbperiod') == 'mar-apr'):
                    $data['between'] = $between = [
                        'start' => Carbon::parse('mar 1, 2022'),
                        'end' => Carbon::parse('apr 15, 2022')
                    ];
                elseif(request('lbperiod') == 'apr-may'):
                    $data['between'] = $between = [
                        'start' => Carbon::parse('apr 16, 2022'),
                        'end' => Carbon::parse('may 20, 2022')
                    ];
                elseif(request('lbperiod') == 'may-jul'):
                    $data['between'] = $between = [
                        'start' => Carbon::parse('may 21, 2022'),
                        'end' => Carbon::parse('jul 4, 2022')
                    ];
                elseif(request('lbperiod') == 'jul-jul'):
                    $data['between'] = $between = [
                        'start' => Carbon::parse('jul 5, 2022'),
                        'end' => Carbon::parse('jul 31, 2022')
                    ];
                else:
                    $data['between'] = $between = [
                        'start' => Carbon::parse('jul 5, 2022'),
                        'end' => Carbon::parse('jul 31, 2022')
                    ];
                endif;

            endif;


            return $data;

        endif;

        $data['stats'] = [];
        $data['charts'] = [];

        return Inertia::render('Dashboard/Projects/Tetrapak/Index', $data);
    }

    public function exportEmails()
    {
        if(request('period') != 'all'):
            $period = explode("@", request('period'));
            $startDate = Carbon::parse($period[0]);
            $endDate = Carbon::parse($period[1])->endOfDay();
            $filename = "emails_period_$period[0]_$period[1].xlsx";
        else:
            $startDate = Carbon::now()->subYear(1);
            $endDate = Carbon::now();
            $filename = "emails_all.xlsx";
        endif;

        function emailsGenerator($startDate, $endDate)
        {
            $uniques = current_project()
                        ->participants()
                        ->where('profile','!=',null)
                        ->groupBy('profile->email')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->get();
            
            foreach($uniques as $participant):
                yield $participant;
            endforeach;
        }

        return (new FastExcel(emailsGenerator($startDate, $endDate)))->download($filename, function ($item) {

            $export['Name'] = isset($item->profile->name) ? $item->profile->name : null;
            $export['E-mail'] = isset($item->profile->email) ? $item->profile->email : null;
            $export['Age'] = isset($item->profile->agerange) ? $item->profile->agerange : null;
            $export['Gender'] = isset($item->profile->gender) ? $item->profile->gender : null;
            $export['Consent'] = isset($item->profile->consent) ? ($item->profile->consent == false ? "no" : "yes") : null;
	        $export['Date'] = $item->created_at->format('Y-m-d');
            $export['Full Date'] = $item->created_at->format('Y-m-d H:i:s');
        
            return $export;
        });

    }

    public function exportDaily()
    {
        if(request('period') != 'all'):
            $period = explode("@", request('period'));
            $startDate = Carbon::parse($period[0]);
            $endDate = Carbon::parse($period[1])->endOfDay();
            $filename = "daily_period_$period[0]_$period[1].xlsx";
        else:
            $startDate = Carbon::now()->subYear(1);
            $endDate = Carbon::now();
            $filename = "daily_all.xlsx";
        endif;

        $scans_daily = current_project()
            ->logs()
            ->where('name','scan_qr')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get(array(
                DB::raw('Date(created_at) as date'),
                DB::raw('COUNT(*) as "views"')
            ));

        $visitors_daily = current_project()
            ->participants()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get(array(
                DB::raw('Date(created_at) as date'),
                DB::raw('COUNT(*) as "views"')
            ));

        $registrations_daily = current_project()
            ->participants()
            ->where('profile->email','!=',null)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get(array(
                DB::raw('Date(created_at) as date'),
                DB::raw('COUNT(DISTINCT( JSON_EXTRACT(`profile` , \'$."email"\') )) as "views"')
            ));

        $results = [];

        foreach($scans_daily as $sd){
            $results[$sd['date']]['date'] = $sd['date'];
            $results[$sd['date']]['scans'] = $sd['views'];
        }  

        /*
        foreach($visitors_daily as $sd){
            $results[$sd['date']]['date'] = $sd['date'];
            $results[$sd['date']]['visitors'] = $sd['views'];
        }  
        */

        foreach($registrations_daily as $sd){
            $results[$sd['date']]['date'] = $sd['date'];
            $results[$sd['date']]['registrations'] = $sd['views'];
        } 

        $collect = collect($results)->sortByDesc('date');

        return (new FastExcel($collect))->download($filename);

    }

    public function exportLeaderboard()
    {
        //adults
        if(current_project()->id == 3):
            $startDate = Carbon::parse('mar 4, 2022');
            $endDate = Carbon::parse('apr 18, 2022');
            $filename = "leaderboard_mar4_apr18.xlsx";
        //kids
        elseif(current_project()->id == 11):
            $startDate = Carbon::parse('mar 1, 2022');
            $endDate = Carbon::parse('apr 15, 2022');
            $filename = "leaderboard_mar1_apr15.xlsx";
        endif;

        function leaderboardGenerator($startDate, $endDate)
        {
            $top = current_project()->leaderboard()
                    ->select('email', 'name', 'details', DB::raw('sum(score) as score'))
                    ->orderBy('score', 'desc')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('email')
                    ->cursor();

            foreach ($top as $user):
                yield $user;
            endforeach;
        }     

        return (new FastExcel(leaderboardGenerator($startDate, $endDate)))->download($filename, function ($item) {
            
            $export['Name'] = $item->name;
            $export['Email'] = $item->email;
            $export['Score'] = $item->score;
            
            return $export;

        });
    }

    public function exports()
    {
        if(request('period') != 'all'):
            $period = explode("@", request('period'));
            $startDate = Carbon::parse($period[0]);
            $endDate = Carbon::parse($period[1])->endOfDay();
            $filename = "export_period_$period[0]_$period[1].xlsx";
        else:
            $startDate = Carbon::now()->subYear(1);
            $endDate = Carbon::now();
            $filename = "export_all.xlsx";
        endif;

        function leaderboardGenerator($startDate, $endDate)
        {
            $fields = request('fields');
            $fields = explode(',',$fields);

            $lb = current_project()->leaderboard()
                  ->where('origin_value','!=','Age')
                  ->whereBetween('created_at', [$startDate, $endDate]);

            if(in_array('deleted_entries', $fields)):
                $lb = $lb->withTrashed();
            endif;

            foreach ($lb->cursor() as $user):
                yield $user;
            endforeach;
        }
        
        return (new FastExcel(leaderboardGenerator($startDate, $endDate)))->download($filename, function ($item) {

            $fields = request('fields');
            $fields = explode(',',$fields);
            $export = array();

            if(in_array('name', $fields)) $export['Name'] = $item->name;
            if(in_array('email', $fields)) $export['Email'] = $item->email;
            if(in_array('gender', $fields)) $export['Gender'] = isset(json_decode($item->participant)->gender) ? json_decode($item->participant)->gender : null;
            if(in_array('age', $fields)) $export['Age Group'] = isset(json_decode($item->participant)->agerange) ? json_decode($item->participant)->agerange : null;
            if(in_array('score', $fields)) $export['Score'] = $item->score;
            if(in_array('origin', $fields)) $export['Origin'] = $item->origin_value;            
            if(in_array('package', $fields)) $export['Package'] = $item->source ? $item->source->title : null;
            if(in_array('country', $fields)) $export['Country'] = $item->source ? $item->source->country : null;
            if(in_array('language', $fields)) $export['Language'] = $item->source ? $item->source->language : null;
            if(in_array('date', $fields)) $export['Date'] = $item->created_at->format('Y-m-d H:i:s');
            if(in_array('deleted_entries', $fields)) $export['Deleted Date'] = $item->deleted_at ? $item->deleted_at->format('Y-m-d H:i:s') : null;
            
            return $export;

        });        
    }

    public function analytics()
    {
        return parent::analytics();
    }

    public function prizes()
    {
        //if(!project_module('prizes')) abort(404);

        $data['leaderboard'] = $leaderboard = current_project()->leaderboard()->with(['user','prize'])->get();
        $data['prizes'] = current_project()->prizes;

        return Inertia::render('Dashboard/Projects/Tetrapak/Prizes', $data);
    }

    public function participantUpdate()
    {
        $lb = Leaderboard::find(request('id'));

        if(request('action') == 'update'):
            $lb->score = request('score');
            $lb->origin_value = request('origin_value');
            $lb->save();
            return redirect()->back()->with('status','Updated');
        elseif(request('action') == 'date'):
            $lb->created_at = request('created_at');
            $lb->save();
            return redirect()->back()->with('status','Updated');
        elseif(request('action') == 'delete'):
            $lb->delete();
            return redirect()->back()->with('status','Deleted');
        endif;
    }
}
