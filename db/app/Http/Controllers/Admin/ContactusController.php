<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ContactusController extends Controller
{

    public function index(){
        $messages = ContactUs::whereState('opened')->orderBy('id','DESC')->paginate(50);
        $readMessages = ContactUs::where('state','opened')->orderBy('id','DESC')->get();
        $unreadMessages = ContactUs::where('state','closed')->orderBy('id','DESC')->get();
        return view('admin.contact_us.index',compact('messages','readMessages','unreadMessages'));
    }

    public function viewMessage($id){
        $message = ContactUs::find($id);
        if(is_null($message)){
            return abort(404);
        }
        $allMessages = \DB::connection('mysql2')->table('user_tickets_reply')->where('ticket_id',$message->id)->get();
        return view('admin.contact_us.view',compact('message','allMessages'));
    }

    public function delete($id){
        $message = ContactUs::find($id);
        if(is_null($message)){
            return abort(404);
        }
        $message->delete();
        toast('Message deleted successfully','success');
        return redirect()->route('contact_us');
    }

    public function search(Request $request){
        $readMessages = ContactUs::where('state','opened')->orderBy('id','DESC')->get();
        $unreadMessages = ContactUs::where('state','closed')->orderBy('id','DESC')->get();
        $messages = ContactUs::where('state','opened')->orderBy('id','DESC');
        if(isset($request->username)){
            $user = User::where('user_name',$request->username)->first();
            if(!is_null($user)){
                $messages = $messages->where('user_id', $user->id);
            }
        }
        
        if(isset($request->state)){
            $messages = $messages->where('state',$request->state);
        }
        $messages = $messages->paginate(50);
        return view('admin.contact_us.index',compact('messages','readMessages','unreadMessages'));
    }

    // public function delete_all(){
    //     $messages = ContactUs::get();
    //     $messages->delete();
    //     toast('Messages deleted successfully','success');
    //     return redirect()->back();
    // }

    // public function change_state_selected(Request $request){
    //     $ids = $request->get('ids');
    //     $messages = ContactUs::whereIn('id',$ids);
    //     $messages->update(['state' => "read"]);
    //     toast('Messages deleted successfully','success');
    //     return redirect()->back();
    // }

    // public function delete_selected(Request $request){
    //     $ids = $request->get('ids');
    //     $messages = ContactUs::whereIn('id',$ids);
    //     $messages->delete();
    //     toast('Messages deleted successfully','success');
    //     return redirect()->back();
    // }

    public function changeState($id){
        $message = ContactUs::find($id);
        if(is_null($message)){
            return abort(404);
        }
        $message->state = "closed";
        $message->save();
        toast('Message marked as read','success');
        return redirect()->route('contact_us');
    }

    public function reply(Request $request ){
        
        $data = [
            'user_id' => null,
            'ticket_id' => $request->ticket_id,
            'sender' => 1,
            'message' => $request->message,
            'creationTime' => \Carbon\Carbon::now()->timestamp,
            'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
        ];

        \DB::connection('mysql2')->table('user_tickets_reply')->insert($data);
        toast('Message replied successfully','success');
        return redirect()->back();
    }

}
