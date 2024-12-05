<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // here create all crud logic
    public function loadAllUsers()
    {
        $all_users = User::all();
        return view('users', compact('all_users'));
    }

    public function loadAddUserForm()
    {
        return view('add-user');
    }

    public function AddUser(Request $request)
    {
        // perform form validation here
        $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required',
            'password' => 'required|confirmed|min:4|max:8',
        ]);

        try {
            // register user here
            $new_user = new User;
            $new_user->name = $request->full_name;
            $new_user->email = $request->email;
            $new_user->phone_number = $request->phone_number;
            $new_user->password = Hash::make($request->password);
            $new_user->save();

            // Use redirect with flash message
            return redirect('/users')->with('success', 'User Added Successfully');

            // return response()->with('success', 'User Added Successfully');
            // return response()->json(['success' => 'User Added Successfully']);
        } catch (\Exception $e) {
            // Use redirect with flash message
            return redirect('/add/user')->with('fail', $e->getMessage());

            // return response()->with('fail', $e->getMessage());
        }
    }

    // edit user
    public function EditUser(Request $request)
    {
        // perform form validation here
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $request->user_id,
            'full_name' => 'required|string',
            'phone_number' => 'required',
            'password' => 'nullable|confirmed|min:4|max:8',
        ]);


        try {
            // update user here
            $update_user = User::where('id', $request->user_id)->update([
                'name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ]);

            // Use redirect with flash message
            return redirect('/users')->with('success', 'User Updated Successfully');
        } catch (\Exception $e) {
            // Use redirect with flash message
            return redirect()->route('EditUserForm', ['id' => $request->user_id])->with('fail', $e->getMessage());

            // return redirect('/edit/user')->with('fail', $e->getMessage());
        }
    }

    // load edit user
    public function loadEditForm($id)
    {
        $user = User::find($id);

        return view('edit-user', compact('user'));
    }

    // delete user
    public function deleteUser($id)
    {
        try {
            User::where('id', $id)->delete();
            return redirect('/users')->with('success', 'User Added Successfully');
        } catch (\Exception $e) {
            return redirect('/users')->with('fail', $e->getMessage());
        }
    }
}
