<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use App\Mail\SubcriptionPlanMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index(Request $request){
        $data = $request->all();
        if(!isset($data['plan_type'])){
            $data['plan_type'] = null;
        }
        $data['email'] = Auth::user()->email;
        $data['token'] = session('token');
        return Inertia::render('Contact/Show', $data);
    }

    public function store(Request $request){
        $data = $request->all();

        $plan_type = null;
        if(isset($data['plan_type']) && $data['plan_type']){
            $plan_type = base64_decode($data['plan_type']);
        }
        $user = Auth::user();

        $contact = new Contact;
        $contact->name = $data['name'];
        $contact->phone = $data['phone'];
        $contact->plan_type = $plan_type;
        $contact->subject = $data['subject'];
        $contact->message = $data['message'];
        $contact->user_id = Auth::user()->id;
        
        if($contact->save()){
            Mail::to(env('CONTACT_US_EMAIL'))->send(new SubcriptionPlanMail($user, $contact, null));
            return redirect()->back()->with('status', 'Successfully submitted request for plan '.$plan_type.'.');
        }
        else{
            return redirect()->back()->with('status', 'Something went wrong.');
        }
    }

    public function list(){
        if(Auth::user()->is_admin != 1){
            return redirect()->to('/')->with("status","You don't have permission to access this page.");
        }
        return Inertia::render('Contact/List');
    }

    public function manageList(){
        $start = request()->input('start', 0); // Get the 'start' parameter from the request, default to 0
        $length = request()->input('length', 10); // Get the 'length' parameter from the request, default to 10
        
        $contacts = Contact::select('contacts.id','users.email', 'contacts.name', 'contacts.phone', 'contacts.plan_type', 'contacts.subject')->leftJoin('users', function ($join){
            $join->on('users.id','=','contacts.user_id');
        });
        $filter = request()->get('search');
        $search = (isset($filter['value'])) ? $filter['value'] : false;
        if($search){
            $contacts = $contacts->where(function ($query) use($search){
                $query->where('contacts.name','like','%'.$search.'%')
                        ->orWhere('users.email','like','%'.$search.'%')
                        ->orWhere('contacts.phone','like','%'.$search.'%')
                        ->orWhere('contacts.plan_type','like','%'.$search.'%')
                        ->orWhere('contacts.subject','like','%'.$search.'%');
            });
        }

        $recordsTotal = $contacts->count();
        $contacts = $contacts->skip($start) // Apply the 'start' offset
                            ->take($length) // Apply the 'length' limit
                            ->get(); // Retrieve the data

        $data = [];
        foreach ($contacts as $contact) {
            // Add the data for the row, including the "View" button
            $data[] = [
                'id' => $contact->id,
                'email' => $contact->email,
                'name' => $contact->name,
                'phone' => $contact->phone,
                'plan_type' => $contact->plan_type,
                'subject' => $contact->subject,
            ];
        }
                            
        return response()->json([
            'data' => $data,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
        ]);
    }

    public function view(){
        $getId = request()->id;
        $getContactDetail = Contact::whereId($getId)->first();
        return response()->json($getContactDetail);
    }
}
