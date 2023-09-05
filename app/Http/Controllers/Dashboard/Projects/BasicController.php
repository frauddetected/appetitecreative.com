<?php

namespace App\Http\Controllers\Dashboard\Projects;

use Inertia\Inertia;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\User;
use App\Models\QR;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Cache;

use App\Http\Controllers\Dashboard\MainController;
use App\Models\Participant;
use Rap2hpoutre\FastExcel\FastExcel;

class BasicController extends MainController
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
                })
            ]
        ];

        /* Scans vs Participants */
        $scans = [ 'name' => 'Scans', 'data' => array() ];

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

        foreach($scans_daily as $sd){
            $scans['data'][ $sd['date'] ] = $sd['views'];
        }         

        $data['charts'] = [
            'scans' => array($scans)
        ];

        return Inertia::render('Dashboard/Projects/Default/Basic', $data);
    }
}