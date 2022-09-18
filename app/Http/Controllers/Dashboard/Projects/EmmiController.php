<?php

namespace App\Http\Controllers\Dashboard\Projects;

use Inertia\Inertia;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\User;
use App\Models\Prize;

use App\Models\QR;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Cache;
use Rap2hpoutre\FastExcel\FastExcel;

use App\Http\Controllers\Dashboard\MainController;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class EmmiController extends MainController
{
    public function index()
    {
        if(request('period')):
            $period = explode(".", request('period'));
            $startDate = Carbon::createFromTimestamp($period[0]);
            $endDate = Carbon::createFromTimestamp($period[1]);
        else:
            $startDate = Carbon::now()->subWeeks(1);
            $endDate = Carbon::now();
        endif;

        $data['project'] = current_project();
        $data['period'] = $period = [
            'start' => $startDate,
            'end' => $endDate
        ];

        $cachingPeriod = current_project()->id.'-'.$startDate->format('Y-m-d').'-'.$endDate->format('Y-m-d');
        $ttl = 0;

        $data['stats'] = [
            'scans' => [
                'count' => Cache::remember("stats.scans.count|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->logs()->where('name','scan_qr')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
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
                }),
                'top_country' => Cache::remember("stats.scans.top_country|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->qr()->groupBy('country')->orderBy('scans','desc')->whereBetween('created_at', [$period['start'], $period['end']])->get();
                }),
                'countries' => Cache::remember("stats.scans.countries|$cachingPeriod", $ttl, function() use ($period){
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
                        $qrcode = QR::whereKeyword($f->ucode)->first();
                        if($qrcode->country):
                            $arr[$qrcode->country] = isset($arr[$qrcode->country]) ? $arr[$qrcode->country] + $f->views : $f->views;
                        endif;
                    endforeach;
                  
                    return collect($arr)->sortBy('views')->reverse()->toArray();
                })
            ],
            'page_view' => [
                'count' => Cache::remember("stats.page_view.count|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->logs()->where('name','page_view')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                }),
                'top' => Cache::remember("stats.page_view.top|$cachingPeriod", $ttl, function() use ($period){
                  return current_project()->logs()->where('name','page_view')
                        ->whereBetween('created_at', [$period['start'], $period['end']])
                        ->groupBy('values->page_title')
                        ->get(array(
                            'values->page_title as page',
                            DB::raw('COUNT(*) as "views"')
                        ));
                })
            ],
            'users' => [
                'count' => Cache::remember("stats.users.count|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->logs()->whereBetween('created_at', [$period['start'], $period['end']])->where('source_id','1')->groupBy('sessid')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                }),
                'gender' => Cache::remember("stats.users.gender|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->participants()
                           ->whereBetween('created_at', [$period['start'], $period['end']])
                           ->groupBy('profile->gender')
                           ->get(array(
                            'profile->gender as gender',
                            DB::raw('COUNT(*) as "total"')
                        ));
                }),
                'age' => Cache::remember("stats.users.age|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->participants()
                           ->whereBetween('created_at', [$period['start'], $period['end']])
                           ->groupBy('profile->age')
                           ->get(array(
                            'profile->age as age',
                            DB::raw('COUNT(*) as "total"')
                        ));
                })
            ],
            'participants' => [
                'count' => Cache::remember("stats.participants.count|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->logs()->where('name','post_score')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
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
            ],
            'game_start' => [
                'count' => Cache::remember("stats.game_start.count|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->logs()->where('name','level_start')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                }),
                'top' => Cache::remember("stats.game_start.top|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->logs()->where('name','level_start')
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
                    return current_project()->logs()->where('name','level_end')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                }),
                'top' => Cache::remember("stats.game_end.top|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->logs()->where('name','level_end')
                        ->groupBy('values->game_name')
                        ->whereBetween('created_at', [$period['start'], $period['end']])
                        ->orderBy('views','desc')
                        ->get(array(
                            'values->game_name as name',
                            DB::raw('COUNT(*) as "views"')
                        ));
                })
            ],
            'selfie_select' => [
                'count' => Cache::remember("stats.selfie_select.count|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->logs()->whereBetween('created_at', [$period['start'], $period['end']])->where('name','selfie_select')->get()->count();
                }),
                'top' => Cache::remember("stats.selfie_select.top|$cachingPeriod", $ttl, function() use ($period){                
                    return current_project()->logs()->where('name','selfie_select')
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
                'count' => Cache::remember("stats.selfie_take.count|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->logs()->where('name','selfie_take')->whereBetween('created_at', [$period['start'], $period['end']])->get()->count();
                }),
                'top' => Cache::remember("stats.selfie_take.top|$cachingPeriod", $ttl, function() use ($period){
                    return current_project()->logs()->where('name','selfie_take')
                        ->groupBy('values->filter')
                        ->whereBetween('created_at', [$period['start'], $period['end']])
                        ->orderBy('views','desc')
                        ->get(array(
                            'values->filter as filter',
                            DB::raw('COUNT(*) as "views"')
                        ));
                }),
            ],
            'post_score' => Cache::remember("stats.post_score.single|$cachingPeriod", $ttl, function() use ($period){
                return current_project()->logs()->whereBetween('created_at', [$period['start'], $period['end']])->where('name','post_score')->get()->count();
            })
        ];

        /* Leaderboard */
        $leaderboard = Cache::remember("stats.leaderboard.single|$cachingPeriod", $ttl, function() use ($period){
            $lb = current_project()->leaderboard();
            $lb = $lb->select('email', 'name', 'details', DB::raw('sum(score) as score'));
            $lb = $lb->whereBetween('created_at', [$period['start'], $period['end']])->orderBy('score', 'desc')->groupBy('email')->limit(8)->get();
            return $lb;
        });

        $leaderboard_chart = [];
        $leaderboard_index = 0;
        
        foreach($leaderboard as $lb):
            $leaderboard_index++;
            $leaderboard_chart['#'.$leaderboard_index] = $lb->score;
        endforeach;

        /* Scans vs Participants */
        $scans = [ 'name' => 'Scans', 'data' => array() ];
        $participants = [ 'name' => 'Registrations', 'data' => array() ];

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

        $participants_daily = Cache::remember("stats.participants_daily.single|$cachingPeriod", $ttl, function() use ($period){
                return current_project()
                        ->logs()
                        ->where('name','post_score')
                        ->whereBetween('created_at', [$period['start'], $period['end']])
                        ->groupBy('date')
                        ->orderBy('date', 'ASC')
                        ->get(array(
                            DB::raw('Date(created_at) as date'),
                            DB::raw('COUNT(*) as "views"')
                        ));
        });

        /* Country Chart */
        $countries_packages = $data['stats']['scans']['countries'];

        foreach($scans_daily as $sd){
            $scans['data'][ $sd['date'] ] = $sd['views'];
        }      
        foreach($participants_daily as $sd){
            $participants['data'][ $sd['date'] ] = $sd['views'];
        }       

        /* gender */
        $ageArray = [];

        foreach( $data['stats']['users']['age'] as $profile ){
            if($profile->age) $ageArray[$profile->age] = $profile->total;
        }

        $genderArray = [];

        foreach( $data['stats']['users']['gender'] as $profile ){
            if($profile->gender) $genderArray[$profile->gender] = $profile->total;
        }

        $skusArray = [];

        foreach( $data['stats']['scans']['top'] as $sku ){
            $skusArray[$sku->title] = $sku->views;
        }

        $data['charts'] = [
            'scans_scores' => array($scans, $participants),
            'scans_countries' => $countries_packages,
            'leaderboard' => array_reverse($leaderboard_chart),
            'age' => $ageArray,
            'gender' => $genderArray,
            'skus' => $skusArray
        ];

        return Inertia::render('Dashboard/Projects/Emmi/Index', $data);
    }

    public function exports()
    {
        if(request('period') != 'all'):
            $period = explode(".", request('period'));
            $startDate = Carbon::createFromTimestamp($period[0]);
            $endDate = Carbon::createFromTimestamp($period[1]);
            $filename = 'gdw_export_custom_period.xlsx';
        else:
            $startDate = Carbon::now()->subMonth(3);
            $endDate = Carbon::now();
            $filename = 'gdw_export_all.xlsx';
        endif;

        function leaderboardGenerator($startDate, $endDate)
        {
            foreach (current_project()->leaderboard()->whereBetween('created_at', [$startDate, $endDate])->cursor() as $user):
                yield $user;
            endforeach;
        }
        
        return (new FastExcel(leaderboardGenerator($startDate, $endDate)))->download($filename, function ($item) {

            $fields = request('fields');
            $fields = explode(',',$fields);
            $export = array();

            if(in_array('name', $fields)):

                $parts = explode(" ", $item->name);
                if(count($parts) > 1) {
                    $lastname = array_pop($parts);
                    $firstname = implode(" ", $parts);
                }
                else
                {
                    $firstname = $item->name;
                    $lastname = " ";
                }

                $export['First Name'] = $firstname;
                $export['Last Name'] = $lastname;
            endif;

            if(in_array('email', $fields)) $export['Email'] = $item->email;
            if(in_array('score', $fields)) $export['Score'] = $item->score;
            if(in_array('origin', $fields)) $export['Origin'] = $item->origin_value;            
            if(in_array('package', $fields)) $export['Package'] = $item->source ? $item->source->title : null;
            if(in_array('country', $fields)) $export['Country'] = $item->source ? $item->source->country : null;
            if(in_array('language', $fields)) $export['Language'] = $item->source ? $item->source->language : null;
            if(in_array('date', $fields)) $export['Date'] = $item->created_at->format('Y-m-d H:i:s');
            
            return $export;

        });
    }

    public function analytics()
    {
        return parent::analytics();
    }

    public function prizes()
    {
        if(!project_module('prizes')) abort(404);

        $data['prizes'] = current_project()->prizes;
        $data['prize'] = null;

        $data['period'] = [];
        $data['selectedDate'] = null;

        $leaderboard = current_project()->leaderboard();
        
        if(request('date')):
            $data['selectedDate'] = request('date');
            $leaderboard = $leaderboard->whereDate('created_at', Carbon::parse(request('date')));
        endif;

        /*
        if(request('prize_id')):
            $leaderboard = $leaderboard->whereDate('created_at', Carbon::parse(request('date')));
        endif;
        */

        $data['leaderboard'] = $leaderboard->select('email', 'name', 'user_id', 'details', 'origin_value', 'source_value', DB::raw('sum(score) as score'))->groupBy('email')->with('user')->limit(50)->get();

        if(request('prize')):

            $data['prize'] = $prize = Prize::find(request('prize'));
            $periodicity = $prize->periodicity == 'daily' ? '1 days' : '7 days';

            $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', '2021-09-18');
            $endDate = \Carbon\Carbon::now();
            
            $dateInterval = \DateInterval::createFromDateString($periodicity);
            $dateperiod = new \DatePeriod($startDate, $dateInterval, $endDate);

            $dates = [];
            $count = 0;
        
            foreach($dateperiod as $period):
                $count++;
                if($prize->periodicity == 'weekly' && $count > 1 || $prize->periodicity == 'daily'):
                    if($endDate->format('Y-m-d') != $period->format('Y-m-d')):
                        array_push($dates, [
                            'date' => $period_date = $period->format('Y-m-d'),
                            'winner' => $prize->winner()->where('period', $period_date)->with('user')->first() ?? false
                        ]);
                    endif;
                endif;
            endforeach;

            $data['period'] = array_reverse($dates);

        endif;

        return Inertia::render('Dashboard/Projects/Emmi/Prizes', $data);
    }
}