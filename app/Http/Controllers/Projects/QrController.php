<?php

namespace App\Http\Controllers\Projects;

use Inertia\Inertia;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\User;
use App\Models\QR;
use App\Models\QRParam;
use Illuminate\Support\Str;

class QrController extends Controller
{
    public function view()
    {
        $id = current_project()->id;    
        $data['project'] = Project::with(['i18n','qr','sources','qr_params'])->find($id);

        return Inertia::render('Projects/QRcodes', $data);
    }

    public function store()
    {
        $id = current_project()->id;

        $qr = new QR;

        $qr->title = request('title');
        $qr->keyword = request('keyword') ?? Str::random(8);
        $qr->parent_id = request('parent_id') ? request('parent_id')['id'] : null;
        $qr->country = request('country') ? request('country')['code'] : null;
        $qr->language = request('language') ? request('language')['code'] : null;
        $qr->source_id = request('source') ? request('source')['id'] : null;
        $qr->project_id = $id;

        if($qr->save()){
            return redirect()->back()->with('status','QR code added');
        }
    }

    public function detailsUpload()
    {
		$folder = request('folder') ? request('folder') : 'qr__details';
		$url = request('file')->store($folder, 'static');

        $whitelist = array(
            '127.0.0.1',
            '::1'
        );
		
		if($url){
			$json = array(
				'uploaded' => true,
				'fileName' => basename($url),
				'fileNameFolder' => $folder.'/'.basename($url),
				'location' => !in_array($_SERVER['REMOTE_ADDR'], $whitelist) ? 'https://app.appetite.link/media/static/'.$url : 'http://app.appetitelink:8000/media/static/'.$url
			);

			return response()->json($json);
		}   
    }

    public function detailsAdd()
    {
        $id = current_project()->id;
        $title = request('value');

        $QRParam = new QRParam();
        $QRParam->title = $title;
        $QRParam->project_id = $id;
        $QRParam->save();

        return redirect()->back()->with('status','QR param added');
    }

    public function detailsSave()
    {
        $id = request('code');

        QR::find($id)->forceFill([
            'details' => request('params')
        ])->save();

        return redirect()->back()->with('status','Details saved');
    }
}