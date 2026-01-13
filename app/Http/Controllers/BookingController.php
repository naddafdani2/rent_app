<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        return Booking::all();
    }

    public function show(Request $request, $status = 'all')
    {
        $user = $request->user();
    
        $query = $user->bookings()->with(['apartment', 'apartment.images'])->latest();
    
        if ($status !== 'all') {
            $query->where('status', $status);
        }
    
        $bookings = $query->get();
    
        if ($bookings->isEmpty()) {
            $message = $status === 'all' 
            ? "You don't have any bookings" 
            : "You don't have {$status} bookings";
        
            return response()->json([
            'message' => $message
            ]);
        }
    
        return response()->json([
            'status' => $status,
            'total_bookings' => $bookings->count(),
            'bookings' => $bookings
        ]);
    }

    public function showSpecificBooking(Request $request,$id)
    {
        $user=$request->user();
        $booking=$user->bookings()->with(['apartment','apartment.images'])->find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found or you do not have permission'
            ]);
        }
    
        return response()->json([
            'booking' => $booking
        ]);
    }

    public function create(Request $request)
    {
       $user = $request->user();
       if ($user->is_approved === true)
        {

            $fields=$request->validate([
                    'apartment_id' => 'required|exists:apartments,id',
                    'start_date'=>'required|date',
                    'end_date'=>'required|date'
            ]);

            $isAvailable = $this->checkDateAvailability(
            $fields['apartment_id'], 
            $fields['start_date'], 
            $fields['end_date']);

            if (!$isAvailable) {
                return response()->json([
                    'message' => 'The apartment is already booked for these dates , please choose different dates'
                ]);
            }

            $booking=Booking::create([
                'start_date'=>$fields['start_date'],
                'end_date'=>$fields['end_date'],
                'user_id'=>$user->id,
                'apartment_id'=>$fields['apartment_id'] 
            ]);

            return response()->json([
                    'booking' => $booking
                ]);
        }
        else
        return response()->json([
                'message'=>'Your account has not been approved yet'
            ]);
        }

    public function update(Request $request, $id)
    {
        $user = $request->user();
    
        $booking = $user->bookings()->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found']);
        }
    
        $fields = $request->validate([
            'start_date' => 'sometimes|date|before:end_date',
            'end_date' => 'sometimes|date|after:start_date'
        ]);
    
        if (empty($fields)) {
            return response()->json(['message' => 'No data to update']);
        }

        //check if the apartment is available in the new dates

        $new_start_date = $fields['start_date'] ?? $booking->start_date;
        $new_end_date = $fields['end_date'] ?? $booking->end_date;
    
        $isAvailable = $this->checkDateAvailability($booking->apartment_id, $new_start_date, $new_end_date, $id);
    
        if (!$isAvailable) {
            return response()->json([
                'message' => 'These dates are already booked , please choose different dates'
            ]);
        }
    
        $booking->update($fields);
    
        return response()->json([
            'message' => 'Booking updated successfully',
            'booking' => $booking->load(['apartment', 'apartment.images'])
        ]);
    }

    //check if the apartment is available in the new dates
    
    private function checkDateAvailability($apartment_id, $start_date, $end_date, $booking_id = null)
    {
        $query = Booking::where('apartment_id', $apartment_id)
            ->where('start_date', '<', $end_date)
            ->where('end_date', '>', $start_date)
            ->whereIn('status', ['modified', 'accepted']);
    
        if ($booking_id) {
            $query->where('id', '!=', $booking_id);
        }
        
        return !$query->exists();
    }


    public function cancel(Request $request,$id)
    {
        $user=$request->user();

        $booking=$user->bookings()->find($id);
        if (!$booking) {
            return response()->json(['message' => 'Booking not found']);
        }

        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'Already cancelled']);
        }

        $booking->update(['status'=>'cancelled']);
        
        return response()->json(['message'=>'The booking cancelled successfully','booking'=>$booking]);
    }
}
