<?php

namespace App\Http\Controllers\Programmings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Programmings\Programming;
use App\Programmings\ProgrammingItem;
use App\Programmings\ProfessionalHour;
use App\Programmings\MinisterialProgram;
use App\Programmings\ProgrammingDay;
use App\Programmings\ActivityItem;
use App\Programmings\ActionType;
use App\Establishment;
use App\Commune;

class ProgrammingItemController extends Controller
{
    public function index(Request $request)
    {
        $programmingitems = ProgrammingItem::where('programming_id',$request->programming_id)->OrderBy('id')->get();
        return view('programmings/programmingItems/index')->withProgrammingItems($programmingitems);
    }

    public function create(Request $request)
    {
        if($request->activity_search_id)
        {
           
            $activityItemsSelect = ActivityItem::where('id',(int)$request->activity_search_id)->first();
             //dd($activityItemsSelect);

        }
        else{
            $activityItemsSelect = null;
        }
        $establishments = Establishment::where('type','CESFAM')->OrderBy('name')->get();
        $communes = Commune::All()->SortBy('name');
        //$professionalHours = ProfessionalHour::where('programming_id',$request->programming_id)->OrderBy('id')->get();
        $ministerialPrograms = MinisterialProgram::All()->SortBy('name');
        $actionTypes = ActionType::All()->SortBy('name');
        $activityItems = ActivityItem::All()->SortBy('name');
        $programmingDay = ProgrammingDay::where('programming_id',$request->programming_id)->first();
        


        $professionalHours = ProfessionalHour::select(
                 'pro_professional_hours.id'
                ,'pro_professional_hours.professional_id'
                ,'pro_professional_hours.programming_id'
                ,'pro_professional_hours.value'
                ,'T1.alias')
        ->leftjoin('pro_professionals AS T1', 'pro_professional_hours.professional_id', '=', 'T1.id')
        ->Where('programming_id',$request->programming_id)
        ->orderBy('T1.alias','ASC')
        ->get();


        return view('programmings/programmingItems/create')->withEstablishments($establishments)
                                                          ->withActivityItems($activityItems)
                                                          ->withProfessionalHours($professionalHours)
                                                          ->withMinisterialPrograms($ministerialPrograms)
                                                          ->withActionTypes($actionTypes)
                                                          ->withProgrammingDays($programmingDay)
                                                          ->with('activityItemsSelect', $activityItemsSelect);;
    }

    public function show(Request $request)
    {
        
        $establishments = Establishment::where('type','CESFAM')->OrderBy('name')->get();
        $communes = Commune::All()->SortBy('name');
        //$professionalHours = ProfessionalHour::where('programming_id',$request->programming_id)->OrderBy('id')->get();
        $ministerialPrograms = MinisterialProgram::All()->SortBy('name');
        $actionTypes = ActionType::All()->SortBy('name');
        $activityItems = ActivityItem::All()->SortBy('name');


        $professionalHours = ProfessionalHour::select(
                 'pro_professional_hours.id'
                ,'pro_professional_hours.professional_id'
                ,'pro_professional_hours.programming_id'
                ,'pro_professional_hours.value'
                ,'T1.alias')
        ->leftjoin('pro_professionals AS T1', 'pro_professional_hours.professional_id', '=', 'T1.id')
        ->Where('programming_id',$request->programming_id)
        ->orderBy('id','ASC')
        ->get();


        return view('programmings/programmingItems/create')->withEstablishments($establishments)->withActivityItems($activityItems)->withProfessionalHours($professionalHours)->withMinisterialPrograms($ministerialPrograms)->withActionTypes($actionTypes);
    }

    public function store(Request $request)
    {
        //dd($request->All());
        $programmingItems = new ProgrammingItem($request->All());
        //$programming->year = date('Y', strtotime($request->date));
        //$programming->description = $request->description;
        //$programming->establishment_id = $request->establishment;
        $programmingItems->programming_id = 1;
       
        $programmingItems->save();

        session()->flash('info', 'Se ha creado una nueva actividad de Programación Operativa');

        return redirect()->back();
        //return redirect()->route('programmingitems', ['programming_id' => 1]);
    }

}
