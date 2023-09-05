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
use GeoIp2\Database\Reader;

class MainController extends Controller
{
    public function redirect($id)
    {    
        $code = QR::where('keyword', $id)->first();
        if(!$code) abort(404);

        $code->addScan();
        $domain = $code->project->domain.'?ucode='.$id;

        /* check for ?utm appends */
        if(request()->has('utm_source')) {
            $domain .= '&utm_source='.request()->get('utm_source');
        }
        if(request()->has('utm_medium')) {
            $domain .= '&utm_medium='.request()->get('utm_medium');
        }
        if(request()->has('utm_campaign')) {
            $domain .= '&utm_campaign='.request()->get('utm_campaign');
        }
        if(request()->has('utm_term')) {
            $domain .= '&utm_term='.request()->get('utm_term');
        }
        
        if(request()->has('utm_content')) {
            $domain .= '&utm_content='.request()->get('utm_content');
        } else {
            $urlEncodedName = urlencode($code->title);
            $domain .= '&utm_content=QR:'.$id.':'.$urlEncodedName;
        }
        /* --- */

        /* try pick geo */
        $reader = new Reader(storage_path('app/geolite/GeoLite2-City.mmdb'));
        $record = $reader->city(request()->ip()) ?? null;
        $country = $record ? $record->country : null;
        $city = $record ? $record->city : null;

        /* --- */
        $logAction = new LogAction;
        $logAction->name = 'scan_qr';
        $logAction->values = json_encode(['ucode' => $id]);
        $logAction->details = json_encode(['country' => $country ? $country->name : null, 'city' => $city ? $city->name : null]);
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