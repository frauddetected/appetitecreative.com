<?php

namespace App\Http\Controllers\Go;

use Inertia\Inertia;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\Analytics;
use App\Models\QR;
use App\Models\LogAction;
use App\Models\Source;

class MainController extends Controller
{
    public function redirect($id)
    {    
        $code = QR::where('keyword', $id)->first();
        if(!$code) abort(404);

        $code->addScan();
        $domain = $code->project->domain.'?ucode='.$id;

        $logAction = new LogAction;
        $logAction->name = 'scan_qr';
        $logAction->values = json_encode(['ucode' => $id]);
        $logAction->source_id = $code->source_id;
        $logAction->sessid = request()->ip();
        $logAction->source_value = $id;
        $logAction->project_id = $code->project->id;
        $logAction->save();

        $source = $code->project->sources->where('id', 1)->first();
        $source->pivot->increment('count');
        
        return redirect($domain, 301);
    }
}