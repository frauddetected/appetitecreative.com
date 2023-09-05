<?php
namespace App\Http\Controllers\Projects;

use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Models\AlphaNumCode;

class AlphaController extends Controller
{
    public function __construct()
    {
        // Only allow superadmins to access this controller, except for the externalAPI method
        $this->middleware('project.role:superadmin')->except('externalAPI');
    }

    public function view()
    {
        if(request('delete') == 'true') {
            AlphaNumCode::where('project_id', current_project()->id)->delete();
            return redirect()->back()->with('status','Codes deleted successfully');
        }

        $id = current_project()->id;    
        
        $data['project_id'] = $id;
        
        $data['codes'] = AlphaNumCode::where('project_id', $id)
            ->selectRaw('title, COUNT(*) as total, SUM(CASE WHEN burned = 1 THEN 1 ELSE 0 END) as burned')
            ->groupBy('title')
            ->orderBy('total', 'desc')
            ->paginate(20);

        $data['totalCodes'] = AlphaNumCode::where('project_id', $id)->count();
        $data['burnedCodes'] = AlphaNumCode::where('project_id', $id)->where('burned', true)->count();

        return Inertia::render('Projects/AlphaNum', $data);
    }

    public function store() {
        $projectId = current_project()->id;
        $title = request()->input('title');
        $digits = request()->input('digits');
        $totalCodes = request()->input('quantity');
    
        $codes = [];
        $insertedCount = 0;  // To track how many codes have been inserted into the database
    
        $currentCode = $this->getLastCodeForProject($projectId) ?: $this->generateStartCode($digits);
    
        while ($insertedCount < $totalCodes) {
            $codes[] = [
                'title' => $title,
                'code' => $currentCode,
                'burned' => false,
                'project_id' => $projectId
            ];
    
            $currentCode = $this->incrementCode($currentCode);
    
            // Batch insert if we have reached a threshold or generated all codes
            if (count($codes) >= 1000 || $insertedCount + count($codes) == $totalCodes) {
                try {
                    AlphaNumCode::insert($codes);
                    $insertedCount += count($codes);  // Update the inserted count after a successful batch insert
                } catch (\Illuminate\Database\QueryException $e) {
                    // Handle the exception. In this case, we'll insert codes one by one.
                    foreach ($codes as $codeRow) {
                        try {
                            AlphaNumCode::create($codeRow);
                            $insertedCount++;  // Update the inserted count for each successful individual insert
                        } catch (\Illuminate\Database\QueryException $e) {
                            // This code was not unique, so we skip it.
                        }
                    }
                }
    
                $codes = [];  // Clear the current batch
            }
        }
    
        return redirect()->back()->with('status', 'Successfully inserted ' . $insertedCount . ' codes.');
    }
    
    private function generateStartCode($digits) {
        return str_repeat('0', $digits);
    }
    
    private function getLastCodeForProject($projectId) {
        // Fetch the last code for the current project from the database
        $lastCode = AlphaNumCode::where('project_id', $projectId)->orderBy('id', 'desc')->first();
        return $lastCode ? $lastCode->code : null;
    }
    
    private function incrementCode($code) {
        $alphabet = range('A', 'Z');
        $numbers = range(0, 9);
        $characters = array_merge($alphabet, $numbers);
        $maxLength = strlen($code);
        
        for ($i = $maxLength - 1; $i >= 0; $i--) {
            $currentIndex = array_search($code[$i], $characters);
            if ($currentIndex < count($characters) - 1) {
                $code[$i] = $characters[$currentIndex + 1];
                return $this->formatCode($code, $maxLength);  // Format the code here
            } else {
                $code[$i] = $characters[0];
            }
        }
        
        return $this->generateStartCode($maxLength);
    }
    
    private function formatCode($code, $length) {
        return str_pad($code, $length, '0', STR_PAD_LEFT);
    }   
       
}