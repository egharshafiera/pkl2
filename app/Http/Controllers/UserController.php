<?php


namespace App\Http\Controllers;


use App\Jabatan;
use App\UnitJabatan;
use App\UnitKerja;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use App\Pegawai;


class UserController extends Controller
{
    public function profile($id)
    {
        $data_unitjab = UnitJabatan::all();
        $data_role = Role::all();
        $data_jabatan = Jabatan::all();
        $data_unit = UnitKerja::all();
        $peg = Pegawai::find($id);
        return view('profile',['peg'=>$peg,'data_unitjab'=>$data_unitjab,'data_jabatan'=>$data_jabatan,'data_unit'=>$data_unit]);
    }

    public function index(Request $request)
    {
        $data = User::orderBy('id','DESC')->paginate(5);
        return view('users.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('users.create',compact('roles'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);


        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['status'] = true;


        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        $request->request->add(['user_id' => $user->id]);
        $request->request->add(['status' =>'NON AKTIF']);
        $request->request->add(['nama_lengkap'=>$user->name]);
        $request->request->add(['role'=>$user->getRoleNames()]);
        Pegawai::create($request->all());

        return redirect()->route('users.index')
                        ->with('success','User created successfully');
    }

    public function show($id)
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();


        return view('users.edit',compact('user','roles','userRole'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);


        $input = $request->all();
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = array_except($input,array('password'));
        }


        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();


        $user->assignRole($request->input('roles'));


        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
    }

//    public function delete($id)
//    {
//        User::find($id)->delete();
//        return redirect()->route('users.index')
//                        ->with('success','User deleted successfully');
//    }
    public function delete($id)
    {
        DB::table('users')->where([
            'id'=>$id,
            'status'=>true,
        ])->update([
            'status' => false,
        ]);
        return redirect('/users')->with('sukses','Data berhasil dinonaktifkan');

    }
    public function deletee($id)
    {
        DB::table('users')->where([
            'id'=>$id,
            'status'=>false,
        ])->update([
            'status' => true,
        ]);
        return redirect('/users')->with('sukses','Data berhasil diaktifkan');

    }
}
