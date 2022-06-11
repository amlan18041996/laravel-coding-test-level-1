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
        $event = Event::orderBy('createdAt', 'desc')->paginate($this->paginate);
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
    
    public function delete_event($id){
        $event = Event::find($id);
        $data_response['id'] = $id;
        if(!empty($event)){
            $delete = $event->delete();
            return $this->sendResponse($data_response, "Event soft-deleted successfully.");
        }else{
            return $this->sendError($data_response, "This event does not exist.");
        }
    }

    public function search_event(Request $request){
        $data_all = $request->all();     
        $event = Event::where('events.id', 'like', '%' . $request->event_search_val . '%')
            ->orWhere('events.name', 'like', '%' . $request->event_search_val . '%')
            ->orWhere('events.slug', 'like', '%' . $request->event_search_val . '%')
            ->orderBy('createdAt', 'desc')
            ->paginate($this->paginate);

        return view('event.event_list', [ 'events' => $event, 'search' => '1' ]);
    }

    public function list_cache(){
        $cachedBlog = Redis::get('event');
        if(isset($cachedBlog)) {
            $event = json_decode($cachedBlog, FALSE);      
            return response()->json([
                'status_code' => 201,
                'message'     => 'Fetched from redis',
                'data'        => $event,
            ]);
        }else {
            $event = Event::orderBy('createdAt', 'desc')->paginate($this->paginate);
            Redis::set('event', $event);
      
            return response()->json([
                'status_code' => 201,
                'message'     => 'Fetched from database',
                'data'        => $event,
            ]);
        }        
    }
}
