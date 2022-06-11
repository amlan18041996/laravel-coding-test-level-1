<?php

namespace App\Http\Controllers;

use DB;
use Validator;
use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Http\Resources\Event as EventResource;
use App\Http\Controllers\BaseController;

class EventController extends BaseController
{
    private $paginate = 5;

    public function index(){
        $event = Event::orderBy('createdAt', 'desc')->get(); 
        return $this->sendResponse(new EventResource($event), 'All events fetched successfully.');
    }

    public function active_events(Request $request){
        if ($request->startAt && $request->endAt) {
            $startAt = Carbon::parse($request->startAt)->toDateTimeString();
            $endAt = Carbon::parse($request->endAt)->toDateTimeString();
            $data_result = Event::whereBetween('createdAt', [$startAt, $endAt])->get();
        } else {
            $data_result = [];
        }
        return $this->sendResponse(new EventResource($data_result), 'Active events fetched successfully.');
    }

    public function fetch_event($id){
        $event = Event::find($id);
        if(empty($event))
            return $this->sendError(new EventResource($event), 'No event found.');
        else
            return $this->sendResponse(new EventResource($event), 'Event fetched successfully.');
    }

    public function generate_event(Request $request){
        $validator = Validator::make($request->all(), [
            'event_name' => 'required|unique:events,name',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $event = Event::create([
            "name" => $request->event_name, 
            "slug" => Str::slug($request->event_name)
        ]);
        return $this->sendResponse(new EventResource($event), 'Event generated successfully.');
    }

    public function update_event(Request $request, $id) {
        $event = Event::find($id);
        if($event){
            $validator = Validator::make($request->all(), [
                "event_name" => "required|unique:events,name,{$id}",
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'event_name'   => 'required|unique:events,name',
            ]);
        }

        if($validator->fails()){
            return $this->sendError("Validation Error.", $validator->errors());
        }

        $new_event = Event::updateOrCreate(
            ["id" => $id], 
            ["name" => $request->event_name, "slug" => Str::slug($request->event_name)]
        );
        $message = "Event " . (empty($event) ? 'created' : 'updated') . " successfully.";
        
        return $this->sendResponse(new EventResource($new_event), $message);
    }
    
    public function delete_event($id)
    {
        $event = Event::find($id);
        $data_response['id'] = $id;
        if(!empty($event)){
            $delete = $event->delete();
            return $this->sendResponse($data_response, "Event soft-deleted successfully.");
        }else{
            return $this->sendError($data_response, "This event does not exist.");
        }
    }

    public function event_list_web()
    {
        $event = Event::orderBy('createdAt', 'desc')->paginate($this->pagination_limit);
        return view('event.event_list', [
            'events' => $event,
            'search' => '0',
        ]);
    }
    
    public function event_show_web($id)
    {        
        $event = Event::find($id); 
        return view('event.event_show', compact('event'));
    }
    
    public function event_edit_web($id)
    {
        if(Auth::check()) {
            $event = Event::findOrFail($id);
            return view('event.event_edit', compact('event'));
        }else{
            return redirect()->route('login');
        }   
    }

    public function event_name_exist(Request $request)
    {
        $data_all               =           $request->all();  
        if(isset($data_all['event_id'])){
            if(isset($data_all['event_name'])){
                $exist_value                        =       DB::table('events')->where('events.name', $data_all['event_name'])->whereNotIn('events.id',[$data_all['event_id']])->get()->count();          
            }elseif(isset($data_all['event_slug'])){
                $exist_value                        =       DB::table('events')->where('events.slug', $data_all['event_slug'])->whereNotIn('events.id',[$data_all['event_id']])->get()->count();
            }
        }else{
            if(isset($data_all['event_name'])){
                $exist_value                        =       DB::table('events')->where('events.name', $data_all['event_name'])->get()->count();          
            }
        }
        if($exist_value > '0'){
            $count_status                       =       '0';
        }else{
            $count_status                       =       'true';
        }
        echo $count_status;
    }
    
    public function event_create_web()
    {       
        if(Auth::check()) {
            return view('event.event_create');
        }else{
            return redirect()->route('login');
        } 
    }

    public function delete_event_web(Request $request)
    {
        $data_all = $request->all();  
        $event    = Event::find($data_all['event_id']);
        if($event){
            $delete = $event->delete();
            if($delete==true){
                $data_response['id'] = $event->id;
                $message             = 'event made soft deleted successfully.'; 
            }else{
                $data_response['id'] = $event->id;
                $message             = 'something went wrong.';     
            }
        }else{
            $data_response['id'] = $id;
            $message             = 'this id not exist.';   
        }
        return $this->sendResponse($data_response, $message);        
    }

    public function search_event_web(Request $request)
    {
        $data_all = $request->all();     
        $event = Event::where('events.id', 'like', '%' . $data_all['event_search_val'] . '%')->orWhere('events.name', 'like', '%' . $data_all['event_search_val'] . '%')->orWhere('events.slug', 'like', '%' . $data_all['event_search_val'] . '%')->orderBy('createdAt', 'desc')->paginate($this->pagination_limit);
        return view('event.event_list', [
            'events' => $event,
            'search' => '1',
        ]);    
    }

    public function event_list_cache()
    {
        $cachedBlog = Redis::get('event');
        if(isset($cachedBlog)) {
            $event = json_decode($cachedBlog, FALSE);      
            return response()->json([
                'status_code' => 201,
                'message'     => 'Fetched from redis',
                'data'        => $event,
            ]);
        }else {
            $event = Event::orderBy('createdAt', 'desc')->paginate($this->pagination_limit);
            Redis::set('event', $event);
      
            return response()->json([
                'status_code' => 201,
                'message'     => 'Fetched from database',
                'data'        => $event,
            ]);
        }        
    }

    public function event_list_remote()
    {
        return view('event.remote_list');
    }
}
