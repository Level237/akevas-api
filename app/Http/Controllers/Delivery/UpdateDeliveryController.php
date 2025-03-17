<?php

namespace App\Http\Controllers\Delivery;

use App\Models\Vehicle;
use App\Models\FeedBack;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UpdateDeliveryController extends Controller
{
    public function updateDocuments(Request $request)
    {
        try {
            $delivery=Auth::guard("api")->user();
            $vehicle = Vehicle::where('user_id', $delivery->id)->firstOrFail();
            $feedBack=FeedBack::where('user_id', $delivery->id)->firstOrFail();
            
            
            if ($request->hasFile('vehicle_image')) {
                if ($vehicle->vehicle_image) {
                    Storage::disk('public')->delete($vehicle->vehicle_image);
                }
                $vehicle->vehicle_image = $request->file('vehicle_image')->store('vehicle/image', 'public');
                $vehicle->save();
            }
            if ($request->hasFile('identity_card_in_front')) {
                // Delete old shop profile if exists
                if ($delivery->identity_card_in_front) {
                    Storage::disk('public')->delete($delivery->identity_card_in_front);
                }
                $delivery->identity_card_in_front = $request->file('identity_card_in_front')->store('cni/front', 'public');
               
            }

            // Update identity documents if provided
            if ($request->hasFile('identity_card_with_the_person')) {
                if ($delivery->identity_card_with_the_person) {
                    Storage::disk('public')->delete($delivery->identity_card_with_the_person);
                }
                $delivery->identity_card_with_the_person = $request->file('identity_card_with_the_person')->store('cni/person', 'public');
            }


            if ($request->hasFile('drivers_license')) {
                if ($delivery->drivers_license) {
                    Storage::disk('public')->delete($delivery->drivers_license);
                }
                $delivery->drivers_license = $request->file('drivers_license')->store('drivers_license', 'public');
            }
            $feedBack->status=1;
            $feedBack->save();
            $delivery->save();

            return response()->json([
                'message' => 'Documents updated successfully',
                'success' => true
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
