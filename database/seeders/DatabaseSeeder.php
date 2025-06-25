<?php

namespace Database\Seeders;

use App\Enums\ShipmentStatus;
use App\Models\Company;
use App\Models\DriverProfile;
use App\Models\Route;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create main demo company
        $mainCompany = Company::factory()->carrier()->verified()->create([
            'name' => 'EcoLogix Demo',
            'legal_name' => 'EcoLogix Demo Sp. z o.o.',
            'tax_id' => '1234567890',
            'email' => 'kontakt@ecologix-demo.pl',
            'city' => 'Warszawa',
        ]);

        // Create admin user
        $admin = User::factory()->admin()->create([
            'company_id' => $mainCompany->id,
            'first_name' => 'Admin',
            'last_name' => 'EcoLogix',
            'email' => 'admin@ecologix.pl',
            'password' => Hash::make('password'),
        ]);

        // Create dispatcher
        $dispatcher = User::factory()->dispatcher()->create([
            'company_id' => $mainCompany->id,
            'first_name' => 'Dyspozytor',
            'last_name' => 'Główny',
            'email' => 'dyspozytor@ecologix.pl',
        ]);

        // Create warehouses
        $warehouses = collect([
            ['name' => 'Hub Centralny Warszawa', 'city' => 'Warszawa', 'type' => 'hub', 'code' => 'WAW-HUB'],
            ['name' => 'Depot Kraków', 'city' => 'Kraków', 'type' => 'depot', 'code' => 'KRK-01'],
            ['name' => 'Depot Wrocław', 'city' => 'Wrocław', 'type' => 'depot', 'code' => 'WRO-01'],
            ['name' => 'Sortownia Łódź', 'city' => 'Łódź', 'type' => 'sortation', 'code' => 'LOD-SRT'],
            ['name' => 'Depot Poznań', 'city' => 'Poznań', 'type' => 'depot', 'code' => 'POZ-01'],
            ['name' => 'Depot Gdańsk', 'city' => 'Gdańsk', 'type' => 'depot', 'code' => 'GDA-01'],
        ])->map(fn($data) => Warehouse::factory()->create([
                ...$data,
                'company_id' => $mainCompany->id,
            ]));

        // Create vehicles
        $vehicles = Vehicle::factory(15)->create([
            'company_id' => $mainCompany->id,
            'home_warehouse_id' => $warehouses->random()->id,
        ]);

        // Create drivers with profiles
        $drivers = collect();
        for ($i = 0; $i < 10; $i++) {
            $driver = User::factory()->driver()->create([
                'company_id' => $mainCompany->id,
            ]);

            DriverProfile::factory()->create([
                'user_id' => $driver->id,
                'current_vehicle_id' => $vehicles->random()->id,
            ]);

            $drivers->push($driver);
        }

        // Create shipments in various states
        $shipmentStates = [
            ShipmentStatus::PENDING => 50,
            ShipmentStatus::CONFIRMED => 30,
            ShipmentStatus::PICKED_UP => 20,
            ShipmentStatus::WAREHOUSE_SORTING => 40,
            ShipmentStatus::IN_TRANSIT => 60,
            ShipmentStatus::OUT_FOR_DELIVERY => 25,
            ShipmentStatus::DELIVERED => 200,
            ShipmentStatus::FAILED_DELIVERY => 15,
        ];

        foreach ($shipmentStates as $status => $count) {
            Shipment::factory($count)->create([
                'company_id' => $mainCompany->id,
                'created_by' => $dispatcher->id,
                'status' => $status,
                'current_warehouse_id' => $warehouses->random()->id,
                'assigned_driver_id' => in_array($status, [ShipmentStatus::IN_TRANSIT, ShipmentStatus::OUT_FOR_DELIVERY, ShipmentStatus::DELIVERED])
                    ? $drivers->random()->id
                    : null,
            ]);
        }

        // Create some shipment history
        Shipment::where('status', '!=', ShipmentStatus::PENDING)
            ->get()
            ->each(function ($shipment) use ($dispatcher) {
                ShipmentHistory::create([
                    'shipment_id' => $shipment->id,
                    'user_id' => $dispatcher->id,
                    'to_status' => ShipmentStatus::PENDING,
                    'event_type' => 'status_change',
                    'title' => 'Przesyłka zarejestrowana',
                    'source' => 'system',
                    'is_public' => true,
                    'created_at' => $shipment->created_at,
                ]);

                if (!in_array($shipment->status, [ShipmentStatus::PENDING, ShipmentStatus::CONFIRMED])) {
                    ShipmentHistory::create([
                        'shipment_id' => $shipment->id,
                        'user_id' => $dispatcher->id,
                        'from_status' => ShipmentStatus::PENDING,
                        'to_status' => $shipment->status,
                        'event_type' => 'status_change',
                        'title' => $shipment->status->label(),
                        'source' => 'system',
                        'is_public' => true,
                        'created_at' => $shipment->created_at->addHours(rand(1, 48)),
                    ]);
                }
            });

        // Create additional shipper companies
        Company::factory(5)->shipper()->create();

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin login: admin@ecologix.pl / password');
    }
}

// update 70 

// update 98 

// update 107 

// update 192 

// update 254 

// update 305 

// aegsxdsbj