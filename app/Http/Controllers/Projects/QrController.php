<?php

namespace App\Http\Controllers\Projects;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\User;
use App\Models\QR;
use App\Models\QRParam;
use Illuminate\Support\Str;
use DB;

class QrController extends Controller
{
    public function view()
    {
        $id = current_project()->id;
        
        $data['project'] = Project::with([
            'i18n',
            'sources',
            'qr_params'
        ])->find($id);
        
        $data['project_id'] = $id;
        $data['codes'] = QR::where('project_id', $id)
            ->selectRaw('id, title, keyword, scans, is_unique, COUNT(*) as total, SUM(CASE WHEN is_burn = 1 THEN 1 ELSE 0 END) as burned, qr_link')
            ->groupBy('title')
            ->orderBy('total', 'desc')
            ->paginate(20);
        
        $data['qrCodePermission'] = request()->attributes->get('qrCodePermission');
        $data['codestats'] = [
            'total' => QR::where('project_id', $id)->count(),
            'unique' => QR::where('project_id', $id)->where('is_unique', true)->count(),
            'burned' => QR::where('project_id', $id)->where('is_burn', true)->count(),
        ];

        return Inertia::render('Projects/QRcodes', $data);
    }

    public function store()
    {
        $qrCodePermission = request()->attributes->get('qrCodePermission');
        $msg = 'QR code added';
        if(!$qrCodePermission){
            
            $msg = 'You have exceeded QR code generation limit.';
            return redirect()->back()->with('status', $msg);
        }
        $remainingQrCode = request()->attributes->get('remainingQrCode');
        if($remainingQrCode > 0){
            $msg = 'you have '.$remainingQrCode.' qr code left to generate.';
        }
        $id = current_project()->id;

        $qr = new QR;

        $qr->title = request('title');
        $qr->keyword = request('keyword') ?? Str::random(8);
        $qr->parent_id = request('parent_id') ? request('parent_id')['id'] : null;
        $qr->country = request('country') ? request('country')['code'] : null;
        $qr->language = request('language') ? request('language')['code'] : null;
        $qr->source_id = request('source') ? request('source')['id'] : null;
        $qr->created_by = Auth::user()->id;
        $qr->project_id = $id;

        if($qr->save()){
            return redirect()->back()->with('status',$msg);
        }
    }

    public function bulkStore()
    {
        $id = current_project()->id;

        $title = request()->input('title');
        $totalCodes = request()->input('quantity');
        $isUnique = request()->input('unique');

        $qrCodePermission = request()->attributes->get('qrCodePermission');
        if(isset(Auth::user()->role['name']) && Auth::user()->role['name'] == 'editor'){
            if(!$qrCodePermission){
                return redirect()->back()->with('status', 'You have limited permission to generate QR Code.');
            }
        }

        $existingCodes = QR::pluck('keyword')->toArray();

        $i = 0;
        while ($i < $totalCodes) {

            $code = Str::random(10);

           // If the code is unique, add it to the database
           if (!in_array($code, $existingCodes)) {

                $qr = new QR;
                $qr->title = $title;
                $qr->keyword = $code;
                $qr->is_unique = $isUnique;
                $qr->parent_id = request('parent_id') ? request('parent_id')['id'] : null;
                $qr->country = request('country') ? request('country')['code'] : null;
                $qr->language = request('language') ? request('language')['code'] : null;
                $qr->source_id = request('source') ? request('source')['id'] : null;
                $qr->project_id = $id;
                $qr->save();

                $existingCodes[] = $code;
                $i++;
            }
        }

        return redirect()->back()->with('status','Codes generated successfully');
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

        return redirect()->back()->with('status','QR param added successfully.');
    }

    public function detailsSave()
    {
        $id = request('code');

        QR::find($id)->forceFill([
            'details' => request('params')
        ])->save();

        return redirect()->back()->with('status','Details saved successfully.');
    }

    public function checkLimit()
    {
		$qrCodePermission = request()->attributes->get('qrCodePermission');
        if(!$qrCodePermission){
            $msg = 'You have exceeded QR code generation limit.';
            return redirect()->back()->with('status', $msg);
        }
        $json = array(
            'success' => true
        );
        return response()->json($json);    
    }

    public function updateQr(){
        // Find the QR code by keyword
        $keyword = request()->input('qr_code_id');
        $link = request()->input('new_url');
        // $lastSegment = substr(strrchr($link, '/'), 1);
        // if ($lastSegment === false || empty($lastSegment)) {
        //     $lastSegment = $link;
        // }
        $qrCode = QR::where('keyword', $keyword)->first();
        if ($qrCode) {
            $qrCode->update(['qr_link' => $link]);
            return redirect()->back()->with('status','QR code updated successfully.');
        } 
        return true;
    }

    public function actions()
   {
        if(request('delCode')):
            QR::find(request('delCode'))->delete();
            return redirect()->back()->with('status','QR code deleted successfully.');
        else:
            return redirect()->back()->with('status','Failed to delete QR code!');
        endif;
    } 
}