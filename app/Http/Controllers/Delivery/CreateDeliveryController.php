<?php

namespace App\Http\Controllers\Delivery;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class CreateDeliveryController extends Controller
{
    public function create(Request $request){
        
         try{
        $delivery=new User;
        $delivery->firstName=$request->firstName;
        $delivery->lastName=$request->lastName;
        $delivery->email=$request->email;
        $delivery->phone_number=$request->phone_number;
        $delivery->birthDate=$request->birthDate;
        $delivery->role_id=4;
        $delivery->nationality=$request->nationality;
        $delivery->residence=$request->residence;
        $delivery->card_number=$request->card_number;

        $file_cni_front = $request->file('identity_card_in_front');
        $image_path_cni_front = $file_cni_front->store('cni/front', 'public');
        $delivery->identity_card_in_front=$image_path_cni_front;

        $file_drivers_license = $request->file('drivers_license');
        $image_path_drivers_license = $file_drivers_license->store('drivers_license', 'public');
        $delivery->drivers_license=$image_path_drivers_license;

        $file_cni_with_the_person = $request->file('identity_card_with_the_person');
        $image_path_cni_with_the_person = $file_cni_with_the_person->store('cni/person', 'public');
        $delivery->identity_card_with_the_person=$image_path_cni_with_the_person;

        $delivery->password=Hash::make($request->password);
        if( $delivery->save()){
            $vehicle=new Vehicle;
            $vehicle->vehicle_model=$request->vehicle_model;
            $vehicle->vehicle_number=$request->vehicle_number;
            $vehicle->vehicle_state=$request->vehicle_state;
            $vehicle->vehicle_type=$request->vehicle_type;
            $vehicle->user_id=$delivery->id;


            $vehicle_image = $request->file('vehicle_image');
            $vehicle->vehicle_image=$vehicle_image->store('vehicle/image','public');
            
            }
            if($vehicle->save()){
                foreach($request->quarters as $quarter){
                $vehicle->quarters()->attach($quarter);
            }
            
           
            return response()->json(['message'=>"delivery created successfully",'success'=>true],201);
        }
        

        
       }catch(\Exception $e){
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'errors' => $e
          ], 500);
    }
            
    }
    }

