<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Apartment;
use App\Models\Apt_image;
use App\Models\Booking;
use App\Models\Favorites;
use App\Models\Rating;
use Illuminate\Database\Seeder;
use Faker\Factory;

class DatabaseSeeder extends Seeder
{
    private $existingBookings = [];
    
    public function run(): void
    {
        $faker = Factory::create();

        // Create admin user
        $admin = User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone' => '00000000',
            'email' => null,
            'password' => bcrypt('00000000'),
            'role' => 'admin',
            'is_approved' => true,
        ]);

        echo "Created admin user with phone: {$admin->phone} (role: {$admin->role})\n";

        // Create regular users
        $users = User::factory()
            ->count(10)
            ->approved()
            ->create();

        echo "Created " . $users->count() . " regular users\n";

        // Create apartments
        $apartments = collect();
        $apartmentOwners = $users->take(5);
        
        foreach ($apartmentOwners as $owner) {
            $apartmentCount = rand(2, 4);
            for ($i = 0; $i < $apartmentCount; $i++) {
                $apartment = Apartment::factory()->create([
                    'owner_id' => $owner->id,
                ]);
                $apartments->push($apartment);
            }
        }

        echo "Created " . $apartments->count() . " apartments\n";

        // Add images
        foreach ($apartments as $apartment) {
            $imageCount = rand(3, 5);
            $hasPrimary = false;
            
            for ($j = 0; $j < $imageCount; $j++) {
                $isPrimary = (!$hasPrimary && $j === 0);
                Apt_image::factory()->create([
                    'apartment_id' => $apartment->id,
                    'is_primary' => $isPrimary,
                ]);
                if ($isPrimary) $hasPrimary = true;
            }
        }

        echo "Added images to apartments\n";

        // Create bookings with PROPER availability check
        $totalBookingsCreated = 0;
        
        foreach ($users as $user) {
            $bookingCount = rand(1, 3);
            $userApartments = $apartments
                ->where('owner_id', '!=', $user->id)
                ->values();
            
            $createdBookings = 0;
            
            foreach ($userApartments as $apartment) {
                if ($createdBookings >= $bookingCount) break;
                
                // Try up to 15 times to find available dates
                for ($attempt = 0; $attempt < 15; $attempt++) {
                    // Generate valid random dates
                    $startDate = $this->generateRandomDate(1, 180);
                    $stayDays = rand(2, 14);
                    $endDate = (clone $startDate)->add(new \DateInterval("P{$stayDays}D"));
                    
                    // Get status (but pending/cancelled can't overlap with accepted/modified either!)
                    $status = $this->getRandomStatus();
                    
                    // Check if dates are available (NO overlaps with accepted/modified)
                    if ($this->checkDates($apartment->id, $startDate, $endDate)) {
                        $booking = Booking::factory()->create([
                            'user_id' => $user->id,
                            'apartments_id' => $apartment->id,
                            'start_date' => $startDate->format('Y-m-d'),
                            'end_date' => $endDate->format('Y-m-d'),
                            'status' => $status,
                        ]);
                        
                        // Save booking for future checks
                        $this->existingBookings[] = [
                            'apartment_id' => $apartment->id,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'status' => $status,
                        ];
                        
                        $createdBookings++;
                        $totalBookingsCreated++;
                        break;
                    }
                }
            }
        }

        echo "Created {$totalBookingsCreated} bookings with NO overlapping accepted/modified dates\n";

        // Create favorites
        foreach ($users as $user) {
            $favoriteCount = rand(2, 5);
            $userApartments = $apartments
                ->where('owner_id', '!=', $user->id)
                ->values();
            
            $shuffledApartments = $userApartments->shuffle();
            
            for ($l = 0; $l < min($favoriteCount, count($shuffledApartments)); $l++) {
                Favorites::firstOrCreate([
                    'user_id' => $user->id,
                    'apartment_id' => $shuffledApartments[$l]->id,
                ]);
            }
        }

        echo "Created favorites\n";

        // Create ratings only for accepted/modified bookings
        foreach ($users as $user) {
            $userBookings = Booking::where('user_id', $user->id)
                ->whereIn('status', ['accepted', 'modified'])
                ->get();
                
            $ratingCount = rand(1, min(3, $userBookings->count()));
            
            foreach ($userBookings->take($ratingCount) as $booking) {
                Rating::firstOrCreate([
                    'user_id' => $user->id,
                    'apartment_id' => $booking->apartments_id,
                ], [
                    'rating' => rand(1, 5),
                    'comment' => $faker->optional(0.8)->sentence(),
                ]);
            }
        }

        echo "Created ratings\n";
        echo "Database seeding completed successfully!\n";
    }
    
    private function generateRandomDate($minDays = 1, $maxDays = 180): \DateTime
    {
        $date = new \DateTime();
        $randomDays = rand($minDays, $maxDays);
        $date->add(new \DateInterval("P{$randomDays}D"));
        return $date;
    }
    
    /**
     * CORRECTED: No booking can overlap with existing accepted/modified bookings
     * This matches your controller's checkDateAvailability() logic
     */
    private function checkDates($apartmentId, \DateTime $startDate, \DateTime $endDate): bool
    {
        foreach ($this->existingBookings as $booking) {
            if ($booking['apartment_id'] != $apartmentId) {
                continue; // Different apartment
            }
            
            // Only check against accepted and modified bookings
            if (!in_array($booking['status'], ['accepted', 'modified'])) {
                continue; // Skip pending/cancelled
            }
            
            // Check for overlap: start_date < $end_date && end_date > $start_date
            if ($startDate < $booking['end_date'] && $endDate > $booking['start_date']) {
                return false; // OVERLAP - not available
            }
        }
        
        return true; // No overlap found
    }
    
    private function getRandomStatus()
    {
        // Your database doesn't allow 'pending' - remove it
        $statuses = ['accepted', 'modified', 'cancelled'];
        $weights = [70, 10, 20]; // 70% accepted, 10% modified, 20% cancelled
        
        $random = rand(1, 100);
        $total = 0;
        
        foreach ($weights as $index => $weight) {
            $total += $weight;
            if ($random <= $total) {
                return $statuses[$index];
            }
        }
        
        return 'accepted';
    }
}