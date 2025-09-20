<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MotorPart;
use App\Models\Motor;
use App\Models\Gerai;
use App\Models\KomstirPricing;
use App\Models\MotorType;
use App\Models\Seal;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil seeder lain secara terpisah agar rapi
        $this->call([
            CategoryAndTypeSeeder::class,
            SuspensionPartSeeder::class,
            MotorSeeder::class,
            KomstirPricingSeeder::class,
            UserAndGeraiSeeder::class,
            SealAndSparepartSeeder::class,
            // Anda bisa membuat SealSeeder::class untuk data seal
        ]);
    }
}

// ======================================================================
// DI BAWAH INI ADALAH CLASS SEEDER YANG DIPANGGIL DARI ATAS
// Anda bisa meletakkan semua class ini di dalam file yang sama
// atau membuat file terpisah untuk masing-masing (lebih direkomendasikan).
// ======================================================================

class CategoryAndTypeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Categories and Motor Types...');

        $catMaintenance = Category::create(['name' => 'MAINTENANCE']);
        $catRebound = Category::create(['name' => 'REBOUND']);
        $catDownsize = Category::create(['name' => 'DOWNSIZE']);
        $catPaketRBDW = Category::create(['name' => 'PAKET REBOUND & DOWNSIZE']);
        Category::create(['name' => 'JASA + PART']);
        Category::create(['name' => 'JASA ONLY']);

        $mtBebekMatic = MotorType::create(['name' => 'BEBEK / MATIC']);
        $mtSport = MotorType::create(['name' => 'SPORT / NACKED / CRUISER']);
        $mtTrail = MotorType::create(['name' => 'TRAIL / ADVENTURE']);
        $mtVesmet = MotorType::create(['name' => 'VESPA MATIC']);
        $mtOhlinsMatic = MotorType::create(['name' => 'OHLINS MATIC']);
        $mtOhlinsSport = MotorType::create(['name' => 'OHLINS SPORT / NAKED / CRUISER']);
        $mtOhlinsTrail = MotorType::create(['name' => 'OHLINS TRAIL / ADVENTURE']);

        $this->command->info('Seeding relationship rules (category_motor_type)...');
        $allMotorTypes = [$mtBebekMatic->id, $mtSport->id, $mtTrail->id, $mtVesmet->id, $mtOhlinsMatic->id, $mtOhlinsSport->id, $mtOhlinsTrail->id];
        $catMaintenance->motorTypes()->attach($allMotorTypes);
        $catRebound->motorTypes()->attach($allMotorTypes);

        $nonOhlinsTypes = [$mtBebekMatic->id, $mtSport->id, $mtTrail->id, $mtVesmet->id];
        $catDownsize->motorTypes()->attach($nonOhlinsTypes);
        $catPaketRBDW->motorTypes()->attach($nonOhlinsTypes);
    }
}

class SuspensionPartSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Suspension Motor Parts and Prices...');

        $catMaintenance = Category::where('name', 'MAINTENANCE')->firstOrFail();
        $catRebound = Category::where('name', 'REBOUND')->firstOrFail();
        $catDownsize = Category::where('name', 'DOWNSIZE')->firstOrFail();
        $catPaketRBDW = Category::where('name', 'PAKET REBOUND & DOWNSIZE')->firstOrFail();

        $mtBebekMatic = MotorType::where('name', 'BEBEK / MATIC')->firstOrFail();
        $mtTrail = MotorType::where('name', 'TRAIL / ADVENTURE')->firstOrFail();
        $mtSport = MotorType::where('name', 'SPORT / NACKED / CRUISER')->firstOrFail();
        $mtOhlinsMatic = MotorType::where('name', 'OHLINS MATIC')->firstOrFail();
        $mtOhlinsSport = MotorType::where('name', 'OHLINS SPORT / NAKED / CRUISER')->firstOrFail();
        $mtOhlinsTrail = MotorType::where('name', 'OHLINS TRAIL / ADVENTURE')->firstOrFail();

        // --- Parts untuk BEBEK / MATIC ---
        $partBM_DepanStd_160 = MotorPart::create(['name' => 'DEPAN STD', 'cc_range' => '110-160CC', 'motor_type_id' => $mtBebekMatic->id]);
        $partBM_DepanStd_250 = MotorPart::create(['name' => 'DEPAN STD', 'cc_range' => '200-250CC', 'motor_type_id' => $mtBebekMatic->id]);
        $partBM_DepanUsd_160 = MotorPart::create(['name' => 'DEPAN USD', 'cc_range' => '110-160CC', 'motor_type_id' => $mtBebekMatic->id]);
        $partBM_DepanUsd_250 = MotorPart::create(['name' => 'DEPAN USD', 'cc_range' => '200-250CC', 'motor_type_id' => $mtBebekMatic->id]);
        $partBM_BelakangSStdAm_160 = MotorPart::create(['name' => 'BELAKANG (S) STD/AM', 'cc_range' => '110-160CC', 'motor_type_id' => $mtBebekMatic->id]);
        $partBM_BelakangDStdAm_160 = MotorPart::create(['name' => 'BELAKANG (D) STD/AM', 'cc_range' => '110-160CC', 'motor_type_id' => $mtBebekMatic->id]);
        $partBM_BelakangDStdAm_250 = MotorPart::create(['name' => 'BELAKANG (D) STD/AM', 'cc_range' => '200-250CC', 'motor_type_id' => $mtBebekMatic->id]);
        $partBM_BelakangDStd_160 = MotorPart::create(['name' => 'BELAKANG (D) STD', 'cc_range' => '110-160CC', 'motor_type_id' => $mtBebekMatic->id]);
        $partBM_BelakangDAm_160 = MotorPart::create(['name' => 'BELAKANG (D) AM', 'cc_range' => '110-160CC', 'motor_type_id' => $mtBebekMatic->id]);
        $partBM_BelakangDStd_250 = MotorPart::create(['name' => 'BELAKANG (D) STD', 'cc_range' => '200-250CC', 'motor_type_id' => $mtBebekMatic->id]);
        $partBM_BelakangDAm_250 = MotorPart::create(['name' => 'BELAKANG (D) AM', 'cc_range' => '200-250CC', 'motor_type_id' => $mtBebekMatic->id]);

        // --- Parts untuk TRAIL ADVENTURE ---
        $partTrail_DepanTeleskopik = MotorPart::create(['name' => 'DEPAN TELESKOPIK', 'cc_range' => '150-250CC', 'motor_type_id' => $mtTrail->id]);
        $partTrail_DepanUsd = MotorPart::create(['name' => 'DEPAN USD', 'cc_range' => '150-250CC', 'motor_type_id' => $mtTrail->id]);
        $partTrail_DepanAmRj = MotorPart::create(['name' => 'DEPAN AM / REAL JUMP', 'cc_range' => '150-250CC', 'motor_type_id' => $mtTrail->id]);
        $partTrail_BelakangStd = MotorPart::create(['name' => 'BELAKANG STD', 'cc_range' => '150-250CC', 'motor_type_id' => $mtTrail->id]);
        $partTrail_BelakangAm = MotorPart::create(['name' => 'BELAKANG AM', 'cc_range' => '150-250CC', 'motor_type_id' => $mtTrail->id]);
        $partTrail_BelakangSStd = MotorPart::create(['name' => 'BELAKANG (S) STD', 'cc_range' => '150-250CC', 'motor_type_id' => $mtTrail->id]);
        $partTrail_BelakangDStdAm = MotorPart::create(['name' => 'BELAKANG (D) STD/AM', 'cc_range' => '150-250CC', 'motor_type_id' => $mtTrail->id]);
        // ... lanjutkan untuk part trail lainnya

        // --- Parts untuk SPORT / NACKED / CRUISER ---
        $partSport_DepanTeleskopik = MotorPart::create(['name' => 'DEPAN TELESKOPIK', 'cc_range' => '150-250CC', 'motor_type_id' => $mtSport->id]);
        $partSport_DepanUsd = MotorPart::create(['name' => 'DEPAN USD', 'cc_range' => '150-250CC', 'motor_type_id' => $mtSport->id]);
        $partSport_DepanAftermarket = MotorPart::create(['name' => 'DEPAN AFTERMARKET', 'cc_range' => '150-250CC', 'motor_type_id' => $mtSport->id]);
        $partSport_BelakangStd = MotorPart::create(['name' => 'BELAKANG STD', 'cc_range' => '150-250CC', 'motor_type_id' => $mtSport->id]);
        $partSport_BelakangDStdAm = MotorPart::create(['name' => 'BELAKANG (D) STD/AM', 'cc_range' => '150-250CC', 'motor_type_id' => $mtSport->id]);
        // ... lanjutkan untuk part sport lainnya

        // --- Parts untuk OHLINS MATIC ---
        $partMatic_Ohlins_Single = MotorPart::create(['name' => 'SINGLE', 'cc_range' => null, 'motor_type_id' => $mtOhlinsMatic->id]);
        $partMatic_Ohlins_Double = MotorPart::create(['name' => 'DOUBLE', 'cc_range' => null, 'motor_type_id' => $mtOhlinsMatic->id]);
        $partSport_Ohlins_Single = MotorPart::create(['name' => 'SINGLE', 'cc_range' => null, 'motor_type_id' => $mtOhlinsSport->id]);
        $partSport_Ohlins_Double = MotorPart::create(['name' => 'DOUBLE', 'cc_range' => null, 'motor_type_id' => $mtOhlinsSport->id]);
        $partTrail_Ohlins_Single = MotorPart::create(['name' => 'SINGLE', 'cc_range' => null, 'motor_type_id' => $mtOhlinsTrail->id]);
        $partTrail_Ohlins_Double = MotorPart::create(['name' => 'DOUBLE', 'cc_range' => null, 'motor_type_id' => $mtOhlinsTrail->id]);


        // 3. Hubungkan Part dengan Kategori beserta Harganya

        // --- Harga untuk BEBEK / MATIC ---
        // DEPAN STD 110-160CC
        $catMaintenance->motorParts()->attach($partBM_DepanStd_160->id, ['price' => 160000]);
        $catRebound->motorParts()->attach($partBM_DepanStd_160->id, ['price' => 300000]);
        $catDownsize->motorParts()->attach($partBM_DepanStd_160->id, ['price' => 250000]);
        $catPaketRBDW->motorParts()->attach($partBM_DepanStd_160->id, ['price' => 400000]);

        // DEPAN STD 200-250CC
        $catMaintenance->motorParts()->attach($partBM_DepanStd_250->id, ['price' => 210000]);
        $catRebound->motorParts()->attach($partBM_DepanStd_250->id, ['price' => 400000]);
        $catDownsize->motorParts()->attach($partBM_DepanStd_250->id, ['price' => 450000]);
        $catPaketRBDW->motorParts()->attach($partBM_DepanStd_250->id, ['price' => 500000]);

        // DEPAN USD 110-160CC
        $catMaintenance->motorParts()->attach($partBM_DepanUsd_160->id, ['price' => 220000]);
        $catRebound->motorParts()->attach($partBM_DepanUsd_160->id, ['price' => 400000]);
        $catDownsize->motorParts()->attach($partBM_DepanUsd_160->id, ['price' => 400000]);
        $catPaketRBDW->motorParts()->attach($partBM_DepanUsd_160->id, ['price' => 500000]);

        // DEPAN USD 200-250CC
        $catMaintenance->motorParts()->attach($partBM_DepanUsd_250->id, ['price' => 270000]);
        $catRebound->motorParts()->attach($partBM_DepanUsd_250->id, ['price' => 500000]);
        $catDownsize->motorParts()->attach($partBM_DepanUsd_250->id, ['price' => 500000]);
        $catPaketRBDW->motorParts()->attach($partBM_DepanUsd_250->id, ['price' => 600000]);

        // BELAKANG (S) STD/AM 110-160CC
        $catMaintenance->motorParts()->attach($partBM_BelakangSStdAm_160->id, ['price' => 160000]);
        $catRebound->motorParts()->attach($partBM_BelakangSStdAm_160->id, ['price' => 300000]);
        $catDownsize->motorParts()->attach($partBM_BelakangSStdAm_160->id, ['price' => 300000]);
        $catPaketRBDW->motorParts()->attach($partBM_BelakangSStdAm_160->id, ['price' => 400000]);

        // BELAKANG (D) STD/AM 110-160CC
        $catRebound->motorParts()->attach($partBM_BelakangDStdAm_160->id, ['price' => 400000]);
        $catDownsize->motorParts()->attach($partBM_BelakangDStdAm_160->id, ['price' => 400000]);
        $catPaketRBDW->motorParts()->attach($partBM_BelakangDStdAm_160->id, ['price' => 500000]);

        // BELAKANG (D) STD/AM 200-250CC
        $catMaintenance->motorParts()->attach($partBM_BelakangDStdAm_250->id, ['price' => 210000]);
        $catRebound->motorParts()->attach($partBM_BelakangDStdAm_250->id, ['price' => 500000]);
        $catDownsize->motorParts()->attach($partBM_BelakangDStdAm_250->id, ['price' => 500000]);
        $catPaketRBDW->motorParts()->attach($partBM_BelakangDStdAm_250->id, ['price' => 600000]);

        // BELAKANG (D) STD 110-160CC + 200-250CC
        $catMaintenance->motorParts()->attach($partBM_BelakangDStd_160->id, ['price' => 200000]);
        $catMaintenance->motorParts()->attach($partBM_BelakangDStd_250->id, ['price' => 250000]);
        // BELAKANG (D) AM 110-160CC + 200-250CC
        $catMaintenance->motorParts()->attach($partBM_BelakangDAm_160->id, ['price' => 250000]);
        $catMaintenance->motorParts()->attach($partBM_BelakangDAm_250->id, ['price' => 300000]);

        // ... Lanjutkan untuk semua kombinasi harga lainnya dengan menggunakan variabel part yang benar ...

        // --- Harga untuk TRAIL ADVENTURE ---
        // DEPAN TELESKOPIK 150-250CC
        $catMaintenance->motorParts()->attach($partTrail_DepanTeleskopik->id, ['price' => 250000]);
        $catRebound->motorParts()->attach($partTrail_DepanTeleskopik->id, ['price' => 400000]);
        $catDownsize->motorParts()->attach($partTrail_DepanTeleskopik->id, ['price' => 450000]);
        $catPaketRBDW->motorParts()->attach($partTrail_DepanTeleskopik->id, ['price' => 500000]);

        // DEPAN USD 150-250CC
        $catMaintenance->motorParts()->attach($partTrail_DepanUsd->id, ['price' => 300000]);
        $catRebound->motorParts()->attach($partTrail_DepanUsd->id, ['price' => 450000]);
        $catDownsize->motorParts()->attach($partTrail_DepanUsd->id, ['price' => 550000]);
        $catPaketRBDW->motorParts()->attach($partTrail_DepanUsd->id, ['price' => 600000]);

        // DEPAN AM/REAL JUMP 150-250CC
        $catMaintenance->motorParts()->attach($partTrail_DepanAmRj->id, ['price' => 400000]);
        $catRebound->motorParts()->attach($partTrail_DepanAmRj->id, ['price' => 500000]);
        $catDownsize->motorParts()->attach($partTrail_DepanAmRj->id, ['price' => 600000]);
        $catPaketRBDW->motorParts()->attach($partTrail_DepanAmRj->id, ['price' => 700000]);

        // BELAKANG STD 150-250CC
        $catRebound->motorParts()->attach($partTrail_BelakangStd->id, ['price' => 400000]);
        $catDownsize->motorParts()->attach($partTrail_BelakangStd->id, ['price' => 450000]);
        $catPaketRBDW->motorParts()->attach($partTrail_BelakangStd->id, ['price' => 550000]);

        // BELAKANG AM 150-250CC
        $catRebound->motorParts()->attach($partTrail_BelakangAm->id, ['price' => 500000]);

        // BELAKANG (S) STD 150-250CC
        $catMaintenance->motorParts()->attach($partTrail_BelakangSStd->id, ['price' => 300000]);

        // BELAKANG (D) STD/AM 150-250CC
        $catMaintenance->motorParts()->attach($partTrail_BelakangDStdAm->id, ['price' => 300000]);
        $catDownsize->motorParts()->attach($partTrail_BelakangDStdAm->id, ['price' => 500000]);
        $catPaketRBDW->motorParts()->attach($partTrail_BelakangDStdAm->id, ['price' => 600000]);
        // ... dst

        // --- Harga untuk SPORT / NACKED / CRUISER ---
        // DEPAN TELESKOPIK 150-250CC
        $catMaintenance->motorParts()->attach($partSport_DepanTeleskopik->id, ['price' => 250000]);
        $catRebound->motorParts()->attach($partSport_DepanTeleskopik->id, ['price' => 450000]);
        $catDownsize->motorParts()->attach($partSport_DepanTeleskopik->id, ['price' => 400000]);
        $catPaketRBDW->motorParts()->attach($partSport_DepanTeleskopik->id, ['price' => 550000]);

        // DEPAN USD 150-250CC
        $catMaintenance->motorParts()->attach($partSport_DepanUsd->id, ['price' => 300000]);
        $catRebound->motorParts()->attach($partSport_DepanUsd->id, ['price' => 500000]);
        $catDownsize->motorParts()->attach($partSport_DepanUsd->id, ['price' => 500000]);
        $catPaketRBDW->motorParts()->attach($partSport_DepanUsd->id, ['price' => 650000]);

        // DEPAN AFTERMARKET 150-250CC
        $catRebound->motorParts()->attach($partSport_DepanAftermarket->id, ['price' => 500000]);

        // BELAKANG STD 150-250CC
        $catMaintenance->motorParts()->attach($partSport_BelakangStd->id, ['price' => 300000]);
        $catRebound->motorParts()->attach($partSport_BelakangStd->id, ['price' => 400000]);
        $catDownsize->motorParts()->attach($partSport_BelakangStd->id, ['price' => 450000]);
        $catPaketRBDW->motorParts()->attach($partSport_BelakangStd->id, ['price' => 550000]);

        // BELAKANG (D) STD/AM 150-250CC
        $catMaintenance->motorParts()->attach($partSport_BelakangDStdAm->id, ['price' => 300000]);
        $catDownsize->motorParts()->attach($partSport_BelakangDStdAm->id, ['price' => 500000]);
        $catPaketRBDW->motorParts()->attach($partSport_BelakangDStdAm->id, ['price' => 700000]);

        // --- Harga untuk OHLINS ---
        // BEBEK/MATIC
        // MAINTENANCE
        $catMaintenance->motorParts()->attach($partMatic_Ohlins_Single->id, ['price' => 300000]);
        $catMaintenance->motorParts()->attach($partMatic_Ohlins_Double->id, ['price' => 400000]);

        // REBOUND
        $catRebound->motorParts()->attach($partMatic_Ohlins_Single->id, ['price' => 500000]);
        $catRebound->motorParts()->attach($partMatic_Ohlins_Double->id, ['price' => 650000]);

        // SPORT / NACKED / CRUISER
        // MAINTENANCE
        $catMaintenance->motorParts()->attach($partSport_Ohlins_Single->id, ['price' => 400000]);
        $catMaintenance->motorParts()->attach($partSport_Ohlins_Double->id, ['price' => 500000]);

        // REBOUND
        $catRebound->motorParts()->attach($partSport_Ohlins_Single->id, ['price' => 600000]);
        $catRebound->motorParts()->attach($partSport_Ohlins_Double->id, ['price' => 750000]);

        // TRAIL / ADVENTURE
        // MAINTENANCE
        $catMaintenance->motorParts()->attach($partTrail_Ohlins_Single->id, ['price' => 450000]);
        $catMaintenance->motorParts()->attach($partTrail_Ohlins_Double->id, ['price' => 550000]);

        // REBOUND
        $catRebound->motorParts()->attach($partTrail_Ohlins_Single->id, ['price' => 650000]);
        $catRebound->motorParts()->attach($partTrail_Ohlins_Double->id, ['price' => 800000]);
    }
}

class MotorSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding specific motors...');

        // Ambil ID jenis motor untuk memastikan sinkronisasi
        $mtBebekMatic = MotorType::where('name', 'BEBEK / MATIC')->first()->id;
        $mtSport = MotorType::where('name', 'SPORT / NACKED / CRUISER')->first()->id;
        $mtTrail = MotorType::where('name', 'TRAIL / ADVENTURE')->first()->id;
        $mtVesmet = MotorType::where('name', 'VESPA MATIC')->first()->id;

        Motor::insert([
            // BEBEK / MATIC
            ['name' => 'Honda Beat FI', 'motor_type_id' => $mtBebekMatic],
            ['name' => 'Honda Beat Street', 'motor_type_id' => $mtBebekMatic],
            ['name' => 'Honda Vario 110', 'motor_type_id' => $mtBebekMatic],
            ['name' => 'Honda Vario 125', 'motor_type_id' => $mtBebekMatic],
            ['name' => 'Yamaha Nmax', 'motor_type_id' => $mtBebekMatic],

            // SPORT
            ['name' => 'Honda CBR150R', 'motor_type_id' => $mtSport],
            ['name' => 'Honda CBR250RR', 'motor_type_id' => $mtSport],
            ['name' => 'Honda CBR250R', 'motor_type_id' => $mtSport],
            ['name' => 'Yamaha YZF-R15', 'motor_type_id' => $mtSport],

            // TRAIL
            ['name' => 'Honda CRF150L', 'motor_type_id' => $mtTrail],
            ['name' => 'Kawasaki KLX150', 'motor_type_id' => $mtTrail],

            // VESPA
            ['name' => 'Vespa LX 125', 'motor_type_id' => $mtVesmet],
        ]);
    }
}

class KomstirPricingSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Komstir Pricings...');

        // Grup 1: Beat, Vario, dll.
        $beatVarioIds = Motor::whereIn('name', ['Honda Beat FI', 'Honda Vario 110', 'Honda Vario 125'])->pluck('id');
        foreach ($beatVarioIds as $motorId) {
            KomstirPricing::create(['motor_id' => $motorId, 'name' => 'JASA + PART', 'part_type' => 'ORI', 'price' => 300000]);
            KomstirPricing::create(['motor_id' => $motorId, 'name' => 'JASA + PART', 'part_type' => 'AM', 'price' => 300000]);
            KomstirPricing::create(['motor_id' => $motorId, 'name' => 'JASA ONLY', 'part_type' => null, 'price' => 150000]);
        }

        // Grup 2: CBR250
        $cbr250Ids = Motor::whereIn('name', ['Honda CBR250RR', 'Honda CBR250R'])->pluck('id');
        foreach ($cbr250Ids as $motorId) {
            KomstirPricing::create(['motor_id' => $motorId, 'name' => 'JASA + PART', 'part_type' => 'ORI', 'price' => 1300000]);
            KomstirPricing::create(['motor_id' => $motorId, 'name' => 'JASA + PART', 'part_type' => 'AM', 'price' => 950000]);
            KomstirPricing::create(['motor_id' => $motorId, 'name' => 'JASA ONLY', 'part_type' => null, 'price' => 300000]);
        }
    }
}

// Add this class at the bottom of your file.

class SealAndSparepartSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Seals and Spareparts...');

        // Insert the Seal data with service_type
        Seal::insert([
            // SEAL DEPAN Category
            [
                "name" => "BK6 / R15",
                "category" => "SEAL DEPAN",
                "qty" => 0,
                "price" => 60000,
                "purchase_price" => 18000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 1,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Dtracker / KLX",
                "category" => "SEAL DEPAN",
                "qty" => 0,
                "price" => 60000,
                "purchase_price" => 7500,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 2,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "3HB",
                "category" => "SEAL DEPAN",
                "qty" => 4,
                "price" => 40000,
                "purchase_price" => 13000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 3,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "3CI / 5BP (33-34-10,5)",
                "category" => "SEAL DEPAN",
                "qty" => 22,
                "price" => 50000,
                "purchase_price" => 13000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 4,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "GN5",
                "category" => "SEAL DEPAN",
                "qty" => 20,
                "price" => 30000,
                "purchase_price" => 13000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 5,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "KC5",
                "category" => "SEAL DEPAN",
                "qty" => 2,
                "price" => 50000,
                "purchase_price" => 18000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 6,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "K84 / KWL",
                "category" => "SEAL DEPAN",
                "qty" => 6,
                "price" => 62500,
                "purchase_price" => 18000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 7,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "KTC USD",
                "category" => "SEAL DEPAN",
                "qty" => 12,
                "price" => 50000,
                "purchase_price" => 50000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 8,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Ninja 250 / K84",
                "category" => "SEAL DEPAN",
                "qty" => 0,
                "price" => 67500,
                "purchase_price" => 18000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 9,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Ninja 2 Tak",
                "category" => "SEAL DEPAN",
                "qty" => 0,
                "price" => 50000,
                "purchase_price" => 20000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 10,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "VESMET (14-34-14)",
                "category" => "SEAL DEPAN",
                "qty" => 9,
                "price" => 30000,
                "purchase_price" => 13000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 11,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "45P",
                "category" => "SEAL DEPAN",
                "qty" => 10,
                "price" => 62500,
                "purchase_price" => 20000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 12,
                "service_type" => "SUSPENSI"
            ],

            // SEAL BELAKANG Category
            [
                "name" => "Seal ADV",
                "category" => "SEAL BELAKANG",
                "qty" => 6,
                "price" => 40000,
                "purchase_price" => 4000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 13,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal Byson 14 41",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 50000,
                "purchase_price" => 18000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 14,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 10 30 12",
                "category" => "SEAL BELAKANG",
                "qty" => 3,
                "price" => 40000,
                "purchase_price" => 8500,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 15,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 10 28 7",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 40000,
                "purchase_price" => 10000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 16,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 10 28 13,4",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 40000,
                "purchase_price" => 3800,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 17,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12 24 5",
                "category" => "SEAL BELAKANG",
                "qty" => 1,
                "price" => 60000,
                "purchase_price" => 5000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 18,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12 26 7",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 40000,
                "purchase_price" => 6000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 19,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12 37 12",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 50000,
                "purchase_price" => 8000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 20,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12,5 36 12",
                "category" => "SEAL BELAKANG",
                "qty" => 2,
                "price" => 50000,
                "purchase_price" => 6500,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 21,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12,5 32 15",
                "category" => "SEAL BELAKANG",
                "qty" => 6,
                "price" => 50000,
                "purchase_price" => 8250,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 22,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12,5 24 5",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 60000,
                "purchase_price" => 9000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 23,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12,5 35 12",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 50000,
                "purchase_price" => 8000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 24,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12.5 37 12",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 50000,
                "purchase_price" => 6800,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 25,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12.20.5",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 40000,
                "purchase_price" => 4500,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 26,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal LBH 12 20",
                "category" => "SEAL BELAKANG",
                "qty" => 5,
                "price" => 40000,
                "purchase_price" => 6000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 27,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal LBH 14",
                "category" => "SEAL BELAKANG",
                "qty" => 3,
                "price" => 40000,
                "purchase_price" => 4500,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 28,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal NMAX",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 40000,
                "purchase_price" => 4000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 29,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal Ninja 12 27 5",
                "category" => "SEAL BELAKANG",
                "qty" => 1,
                "price" => 60000,
                "purchase_price" => 9000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 30,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal OHLINS",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 80000,
                "purchase_price" => 30000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 31,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 14 22 5",
                "category" => "SEAL BELAKANG",
                "qty" => 2,
                "price" => 40000,
                "purchase_price" => 4500,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 32,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 14 24 NOK",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 50000,
                "purchase_price" => 6000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 33,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 14 36 12",
                "category" => "SEAL BELAKANG",
                "qty" => 4,
                "price" => 50000,
                "purchase_price" => 11000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 34,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 14 27 7",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 50000,
                "purchase_price" => 10000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 35,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 14 30 5",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 60000,
                "purchase_price" => 20000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 36,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal Vesmet 10 28 13,4",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 65000,
                "purchase_price" => 3800,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 37,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal YSS 12 31,5 15",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 50000,
                "purchase_price" => 8250,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 38,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal KYB Elite 15 28 10",
                "category" => "SEAL BELAKANG",
                "qty" => 0,
                "price" => 60000,
                "purchase_price" => 28000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 39,
                "service_type" => "SUSPENSI"
            ],

            // AS DEPAN Category
            [
                "name" => "AS Vesmet",
                "category" => "AS DEPAN",
                "qty" => 4,
                "price" => 80000,
                "purchase_price" => 25000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 40,
                "service_type" => "SUSPENSI"
            ],

            // AS BELAKANG Category
            [
                "name" => "AS NMAX",
                "category" => "AS BELAKANG",
                "qty" => 0,
                "price" => 50000,
                "purchase_price" => 7500,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 41,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "AS YSS",
                "category" => "AS BELAKANG",
                "qty" => 9,
                "price" => 80000,
                "purchase_price" => 17500,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 42,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "AS ADV",
                "category" => "AS BELAKANG",
                "qty" => 9,
                "price" => 50000,
                "purchase_price" => 8000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 43,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "AS SHOCK STANDAR VARIO",
                "category" => "AS BELAKANG",
                "qty" => 0,
                "price" => 30000,
                "purchase_price" => 8000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 44,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "AS KTC EXTREME BERONGGA",
                "category" => "AS BELAKANG",
                "qty" => 0,
                "price" => 200000,
                "purchase_price" => 160000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 45,
                "service_type" => "SUSPENSI"
            ],

            // PER DOWNSIZE Category
            [
                "name" => "HONDA",
                "category" => "PER DOWNSIZE",
                "qty" => 17,
                "price" => 15000,
                "purchase_price" => 7000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 46,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "NMAX / PCX",
                "category" => "PER DOWNSIZE",
                "qty" => 0,
                "price" => 25000,
                "purchase_price" => 15000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 47,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "X-MAX",
                "category" => "PER DOWNSIZE",
                "qty" => 16,
                "price" => 25000,
                "purchase_price" => 15000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 48,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "YAMAHA",
                "category" => "PER DOWNSIZE",
                "qty" => 0,
                "price" => 15000,
                "purchase_price" => 7000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 49,
                "service_type" => "SUSPENSI"
            ],

            // OLI TURALIT Category
            [
                "name" => "Oli 200ml",
                "category" => "OLI TURALIT",
                "qty" => 0,
                "price" => 10000,
                "purchase_price" => 6700,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 50,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Oli 500ml",
                "category" => "OLI TURALIT",
                "qty" => 0,
                "price" => 25000,
                "purchase_price" => 16700,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 51,
                "service_type" => "SUSPENSI"
            ],
        ]);

        // Insert the Sparepart data with service_type
        Sparepart::insert([
            // SEAL DEPAN Category (1 SET - price dibagi 2)
            [
                "name" => "BK6 / R15",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 60000, // 120000 / 2
                "purchase_price" => 18000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Dtracker / KLX",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 60000, // 120000 / 2
                "purchase_price" => 7500,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "3HB",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 40000, // 80000 / 2
                "purchase_price" => 13000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "3CI / 5BP (33-34-10,5)",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 50000, // 100000 / 2
                "purchase_price" => 13000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "GN5",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 30000, // 60000 / 2
                "purchase_price" => 13000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "KC5",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 50000, // 100000 / 2
                "purchase_price" => 18000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "K84 / KWL",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 62500, // 125000 / 2
                "purchase_price" => 18000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "KTC USD",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 50000, // 100000 / 2
                "purchase_price" => 50000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Ninja 250 / K84",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 67500, // 135000 / 2
                "purchase_price" => 18000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Ninja 2 Tak",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 50000, // 100000 / 2
                "purchase_price" => 20000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "VESMET (14-34-14)",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 30000, // 60000 / 2
                "purchase_price" => 13000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "45P",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 62500, // 125000 / 2
                "purchase_price" => 20000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],

            // SEAL BELAKANG Category
            [
                "name" => "Seal ADV",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 4000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal Byson 14 41",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 18000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 10 30 12",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 8500,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 10 28 7",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 10000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 10 28 13,4",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 3800,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12 24 5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 60000,
                "purchase_price" => 5000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12 26 7",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 6000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12 37 12",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 8000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12,5 36 12",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 6500,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12,5 32 15",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 8250,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12,5 24 5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 60000,
                "purchase_price" => 9000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12,5 35 12",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 8000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12.5 37 12",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 6800,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 12.20.5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 4500,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal LBH 12 20",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 6000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal LBH 14",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 4500,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal NMAX",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 4000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal Ninja 12 27 5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 60000,
                "purchase_price" => 9000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal OHLINS",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 80000,
                "purchase_price" => 30000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 14 22 5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 4500,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 14 24 NOK",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 6000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 14 36 12",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 11000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 14 27 7",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 10000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal 14 30 5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 60000,
                "purchase_price" => 20000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal Vesmet 10 28 13,4",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 65000,
                "purchase_price" => 3800,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal YSS 12 31,5 15",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 8250,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Seal KYB Elite 15 28 10",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 60000,
                "purchase_price" => 28000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],

            // AS DEPAN Category
            [
                "name" => "AS Vesmet",
                "category" => "AS DEPAN",
                "qty" => 100,
                "price" => 80000,
                "purchase_price" => 25000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],

            // AS BELAKANG Category
            [
                "name" => "AS NMAX",
                "category" => "AS BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 7500,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "AS YSS",
                "category" => "AS BELAKANG",
                "qty" => 100,
                "price" => 80000,
                "purchase_price" => 17500,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "AS ADV",
                "category" => "AS BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 8000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "AS SHOCK STANDAR VARIO",
                "category" => "AS BELAKANG",
                "qty" => 100,
                "price" => 30000,
                "purchase_price" => 8000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "AS KTC EXTREME BERONGGA",
                "category" => "AS BELAKANG",
                "qty" => 100,
                "price" => 200000,
                "purchase_price" => 160000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],

            // PER DOWNSIZE Category
            [
                "name" => "HONDA",
                "category" => "PER DOWNSIZE",
                "qty" => 100,
                "price" => 15000,
                "purchase_price" => 7000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "NMAX / PCX",
                "category" => "PER DOWNSIZE",
                "qty" => 100,
                "price" => 25000,
                "purchase_price" => 15000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "X-MAX",
                "category" => "PER DOWNSIZE",
                "qty" => 100,
                "price" => 25000,
                "purchase_price" => 15000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "YAMAHA",
                "category" => "PER DOWNSIZE",
                "qty" => 100,
                "price" => 15000,
                "purchase_price" => 7000,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],

            // OLI TURALIT Category
            [
                "name" => "Oli 200ml",
                "category" => "OLI TURALIT",
                "qty" => 100,
                "price" => 10000,
                "purchase_price" => 6700,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
            [
                "name" => "Oli 500ml",
                "category" => "OLI TURALIT",
                "qty" => 100,
                "price" => 25000,
                "purchase_price" => 16700,
                "motor_id" => null,
                "service_type" => "SUSPENSI"
            ],
        ]);
    }
}

class UserAndGeraiSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Gerai and Users...');
        Gerai::insert([
            ['name' => 'Bekasi', 'location' => 'Bekasi'],
            ['name' => 'Depok', 'location' => 'Depok'],
            ['name' => 'Cikarang', 'location' => 'Cikarang'],
        ]);

        User::insert([
            ["username" => "adminsosmedggsuspension", "password" => Hash::make("adminsosmedggsuspension123"), "gerai_id" => null, "role" => "CS"],
            ["username" => "geraipusatggsuspension", "password" => Hash::make("geraipusatggsuspension123"), "role" => "ADMIN", "gerai_id" => 1],
            ["username" => "ceo", "password" => Hash::make("12345"), "gerai_id" => null, "role" => "CEO"]
        ]);
    }
}
