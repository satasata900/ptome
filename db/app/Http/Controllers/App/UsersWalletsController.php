<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\UserWallet;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UsersWalletsController extends Controller
{

    public function index() {
        $userWallets = DB::connection('mysql2')->table('users_wallets')
              ->join('users','users_wallets.user_id','=','users.id')
             ->join('wallets','users_wallets.wallet_id','=','wallets.id')
            ->select('users_wallets.id', 'users.full_name','users.user_name', 'wallets.wallet_name','wallets.price')
            ->paginate(50);
        return view('app.users_wallets.index',compact('userWallets'));
    }

    public function add() {
        $wallets = Wallet::get();
        $users = User::get();
        return view('app.users_wallets.add',compact('wallets','users'));
    }

    public function store(Request $request) {
        $data = $request->all();
        $validatedData = $request->validate([
            'wallet_name' => ['required'],
            'owner_name' => ['required'],
            'price' => ['required','numeric'],
        ],[
            'wallet_name.required' => "Wallet is required",
            'owner_name.required' => "Owner is required",
            'price.required' => "Price is required",
            'price.numeric' => "Balance must be numeric",
        ]);

        $targetUserWallets = User::find($data['owner_name'])->wallets;
        if(count($targetUserWallets) > 0){
            $targetWallet = Wallet::find($data['wallet_name']);
            $result = $targetUserWallets->where('id',$targetWallet->id);
            if(count($result) > 0){
                alert()->error('Invalid Wallet','Selected user already has this wallet');
                return redirect()->route('users_wallets_add');
            }
        }
        $userWallet = new UserWallet();
        $userWallet->user_id = $data['owner_name'];
        $userWallet->wallet_id = $data['wallet_name'];
        $userWallet->price = $data['price'];

        if(isset($data['hidden_wallet'])){
            $userShownWallets = UserWallet::where('user_id',$data['owner_name'])
                ->where('hidden',0)->get();
            if(count($userShownWallets) > 0){
                $userWallet->hidden = 1;
            }
            else{
                alert()->error('Invalid Operation','Selected user should have at least 1 active wallet');
                return redirect()->route('users_wallets_add');
            }
        }

        if($userWallet->save()){
            toast('Wallet added successfully','success');
            return redirect()->route('users_wallets');
        }
    }

    public function edit($id) {
        $wallets = Wallet::get();
        $users = User::get();
        $userWallet = UserWallet::find($id);
        if(is_null($userWallet)){
            return abort(404);
        }
        return view('app.users_wallets.edit',compact('userWallet','wallets','users'));
    }

    public function update(Request $request , $id) {

        $userWallet = UserWallet::find($id);
        if(is_null($userWallet)){
            return abort(404);
        }

        $data = $request->all();
        $validatedData = $request->validate([
            'wallet_name' => ['required'],
            'owner_name' => ['required'],
            'price' => ['required','numeric'],
        ],[
            'wallet_name.required' => "Wallet is required",
            'owner_name.required' => "Owner is required",
            'price.required' => "Price is required",
            'price.numeric' => "Balance must be numeric",
        ]);

        $targetUserWallets = User::find($data['owner_name'])->wallets
            ->where('id','!=',$data['wallet_name']);
        if(count($targetUserWallets) > 0){
            $targetWallet = Wallet::find($data['wallet_name']);
            $result = $targetUserWallets->where('id',$targetWallet->id);
            if(count($result) > 0){
                alert()->error('Invalid Wallet','Selected user already has this wallet');
                return redirect()->route('users_wallets_edit',$id);
            }
        }
        $userWallet->user_id = $data['owner_name'];
        $userWallet->wallet_id = $data['wallet_name'];
        $userWallet->price = $data['price'];

        if(isset($data['hidden_wallet'])){
            $userShownWallets = UserWallet::where('id','!=',$id)
                ->where('user_id',$data['owner_name'])
                ->where('hidden',0)->get();
            if(count($userShownWallets) > 0){
                $userWallet->hidden = 1;
            }
            else{
                alert()->error('Invalid Operation','Selected user should have at least 1 active wallet');
                return redirect()->back();
            }
        }
        else{
            $userWallet->hidden = 0;
        }

        if($userWallet->save()){
            toast('Wallet updated successfully','success');
            return redirect()->route('users_wallets_edit',$id);
        }

    }

    public function delete($id) {
        $wallets = Wallet::find($id);
        if(is_null($wallets)){
            return abort(404);
        }
        if($wallets->delete()){
            toast('Wallet deleted successfully','success');
        }
        return redirect()->route('wallets');
    }

    public function deactivate($id) {
        $user = User::find($id);
        if(is_null($user)){
            return abort(404);
        }
        $user->active = 0;
        if($user->save()){
            toast('User deactivated successfully','success');
        }
        return redirect()->route('app_users');
    }

    public function activate($id) {
        $user = User::find($id);
        if(is_null($user)){
            return abort(404);
        }
        $user->active = 1;
        if($user->save()){
            toast('User activated successfully','success');
        }
        return redirect()->route('app_users');
    }

    public function deactivateSelected(Request $request) {
        $ids = $request->ids;
        $targetUsers = User::whereIn('id',$ids)->where('active',1);
        if(is_null($ids)){
            return abort(404);
        }
        if($targetUsers->update(['active'=>0])){
            toast('Users deactivated successfully','success');
        }
        return redirect()->route('app_users');
    }

    public function deleteSelected(Request $request) {
        $ids = $request->ids;
        $targetUsers = User::whereIn('id',$ids);
        if(is_null($ids)){
            return abort(404);
        }
        if($targetUsers->delete()){
            toast('Users deleted successfully','success');
        }
        return redirect()->route('app_users');
    }

    public function search(Request $request){
        $userWallets = DB::connection('mysql2')->table('users_wallets')
            ->join('users','users_wallets.user_id','=','users.id')
            ->join('wallets','users_wallets.wallet_id','=','wallets.id')
            ->select('users_wallets.id', 'users.full_name','users.user_name', 'wallets.wallet_name','wallets.price');
        if(isset($request->id)){
            $userWallets = $userWallets->where('users_wallets.id', $request->id);
        }
        if(isset($request->username)){
            $userWallets = $userWallets->where('users.user_name' , 'LIKE', '%' . $request->username . '%');
        }
        if(isset($request->name)){
            $userWallets = $userWallets->where('users.full_name','LIKE','%' .$request->name. '%');
        }
        if(isset($request->wallet_name)){
            $userWallets = $userWallets->where('wallets.wallet_name','LIKE','%' .$request->wallet_name. '%');
        }
        $userWallets = $userWallets->paginate(50);
        return view('app.users_wallets.index',compact('userWallets'));
    }

}
