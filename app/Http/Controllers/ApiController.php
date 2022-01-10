<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use DB;  

class ApiController extends Controller
{
    public function register(Request $request)
    {
    	//Validate data
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50' 
        ]);
		 		
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        } 
		
		//request images 
		if ($request->file('file')) {
            $imagePath = $request->file('file');
            $imageName = $imagePath->getClientOriginalName(); 
            $request->file('file')->storeAs('uploads', $imageName, 'public'); 
        }
		 
		
        //Request is valid, create new user
        $user = User::create([
        	'name' => $request->name,
        	'email' => $request->email,
        	'password' => bcrypt($request->password),
			'image' =>  $imageName
        ]);
		 
        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }
 
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                	'success' => false,
                	'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
    	return $credentials;
            return response()->json([
                	'success' => false,
                	'message' => 'Could not create token.',
                ], 500);
        }
 	
 		//Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }
 
    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

		//Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);
 
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
 
    public function get_user(Request $request)
    { 
		JWTAuth::parseToken()->authenticate();
		 
		$draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page
		
		$columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
		
		$totalRecords = User::select('count(*) as allcount')->count();
        $totalRecordswithFilter = User::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();
		
		
        $records = DB::table('users')
						->orWhere('users.name', 'like', '%' . $searchValue . '%')
						->orWhere('users.email', 'like', '%' . $searchValue . '%')
						->skip($start)
						->take($rowperpage)
						->get();
						
		$data_arr = array();

        foreach ($records as $key => $record) {  
            $data_arr[$key] = array(
                "id" => $record->id,
                "name" => $record->name,
                "email" => $record->email 
            );
			 
			$actions = '<a href="#" class="btn btn-sm btn-success" onclick="tambah('.$record->id.')" data-target="#mediumModal" data-attr="/newuser">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger" onclick="deletes('.$record->id.')">Delete</a>';
			$data_arr[$key]['image'] = '<img src="'. asset('storage/uploads/'.$record->image) .'" alt="'.$record->name.'" style="width:200px;">';		
			$data_arr[$key]['action'] = $actions;
        } 
		
		$response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
        ); 
       return response()->json($response);
    } 
	
	public function show($id)
    { 
		$usr = DB::table('users')->find($id); 
        if (!$usr) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user not found.'
            ], 400);
        }
    
        return $usr; 
    }
	
	
	public function update(Request $request)
    {
        //Validate data
        $data = $request->only('name');
        $validator = Validator::make($data, [
            'name' => 'required|string'  
        ]);
		 		
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        } 
		
		$updateArray = [
        	'name' => $request->name 
        ];
		
        //request images  
		if ($request->file('file') != null) {
            $imagePath = $request->file('file');
            $imageName = $imagePath->getClientOriginalName(); 
            $request->file('file')->storeAs('uploads', $imageName, 'public');
			$updateArray['image'] = $imageName; 
        }
		 
		 
		if($request->password != ''){
			$updateArray['password'] = bcrypt($request->password) ;
		}
		 
        $user = DB::table('users')
				->where('id', $request->user_id)
				->update( $updateArray );

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }
	
	
	public function hapusdata(Request $request)
    {
        $user = User::find($request->id);
		$user->delete(); 
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ], Response::HTTP_OK); 
    }
}