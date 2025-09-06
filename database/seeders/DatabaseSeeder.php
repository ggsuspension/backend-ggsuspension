<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\MotorPart;
use App\Models\Motor;
use App\Models\Gerai;
use App\Models\Seal;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Categories
        $categoryMaintenance = Category::create([
            'name' => 'MAINTENANCE',
            'img_path' => './layanan/maintenance.png',
        ]);
        $categoryRebound = Category::create(['name' => 'REBOUND', 'img_path' => './layanan/rebound.png']);
        $categoryPaketRBDW = Category::create(['name' => 'PAKET REBOUND & DOWNSIZE', 'img_path' => './layanan/paket.png']);
        $categoryDownsize = Category::create(['name' => 'DOWNSIZE', 'img_path' => './layanan/downsize.png']);

        $subcategoryBebekMaticMain = Subcategory::create([
            'name' => 'BEBEK / MATIC',
            "img_path" => "./jenis-motor/bebek-matic.webp",
            'category_id' => $categoryMaintenance->id,
        ]);
        $subcategoryBebekMaticReb = Subcategory::create([
            'name' => 'BEBEK / MATIC',
            "img_path" => "./jenis-motor/bebek-matic.webp",
            'category_id' => $categoryRebound->id,
        ]);
        $subcategoryBebekMaticDown = Subcategory::create([
            'name' => 'BEBEK / MATIC',
            'category_id' => $categoryDownsize->id,
            "img_path" => "./jenis-motor/bebek-matic.webp",
        ]);
        $subcategoryBebekMaticPRD = Subcategory::create([
            'name' => 'BEBEK / MATIC',
            'category_id' => $categoryPaketRBDW->id,
            "img_path" => "./jenis-motor/bebek-matic.webp",
        ]);
        $subcategoryOhlinsMaticReb = Subcategory::create([
            'name' => 'OHLINS MATIC',
            'category_id' => $categoryRebound->id,
            "img_path" => "./jenis-motor/ohlins-matic.jpg"
        ]);
        $subcategoryOhlinsMaticMain = Subcategory::create([
            'name' => 'OHLINS MATIC',
            'category_id' => $categoryMaintenance->id,
            "img_path" => "./jenis-motor/ohlins-matic.jpg"
        ]);
        $subcategoryOhlinsSNCReb = Subcategory::create([
            'name' => 'OHLINS SPORT / NAKED / CRUISER',
            'category_id' => $categoryRebound->id,
            "img_path" => "./jenis-motor/ohlins-sport.jpg"
        ]);
        $subcategoryOhlinsSNCMain = Subcategory::create([
            'name' => 'OHLINS SPORT / NAKED / CRUISER',
            'category_id' => $categoryMaintenance->id,
            "img_path" => "./jenis-motor/ohlins-sport.jpg"
        ]);
        $subcategoryOhlinsTAReb = Subcategory::create([
            'name' => 'OHLINS TRAIL / ADVENTURE',
            'category_id' => $categoryRebound->id,
            "img_path" => "./jenis-motor/ohlins-adventure.jpg"
        ]);
        $subcategoryOhlinsTAMain = Subcategory::create([
            'name' => 'OHLINS TRAIL / ADVENTURE',
            'category_id' => $categoryMaintenance->id,
            "img_path" => "./jenis-motor/ohlins-adventure.jpg"
        ]);
        $subcategoryTAReb = Subcategory::create([
            'name' => 'TRAIL / ADVENTURE',
            "img_path" => "./jenis-motor/trail-adventure.jpg",
            'category_id' => $categoryRebound->id,
        ]);
        $subcategoryTADown = Subcategory::create([
            'name' => 'TRAIL / ADVENTURE',
            'category_id' => $categoryDownsize->id,
            "img_path" => "./jenis-motor/trail-adventure.jpg",
        ]);
        $subcategoryTAPRD = Subcategory::create([
            'name' => 'TRAIL / ADVENTURE',
            'category_id' => $categoryPaketRBDW->id,
            "img_path" => "./jenis-motor/trail-adventure.jpg",
        ]);
        $subcategoryTAMain = Subcategory::create([
            'name' => 'TRAIL / ADVENTURE',
            'category_id' => $categoryMaintenance->id,
            "img_path" => "./jenis-motor/trail-adventure.jpg",
        ]);
        $subcategorySNCMain = Subcategory::create([
            'name' => 'SPORT / NAKED / CRUISER',
            'category_id' => $categoryMaintenance->id,
            "img_path" => "./jenis-motor/sport-naked-cruiser.jpg",
        ]);
        $subcategorySNCReb = Subcategory::create([
            'name' => 'SPORT / NAKED / CRUISER',
            'category_id' => $categoryRebound->id,
            "img_path" => "./jenis-motor/sport-naked-cruiser.jpg",
        ]);
        $subcategorySNCDown = Subcategory::create([
            'name' => 'SPORT / NAKED / CRUISER',
            'category_id' => $categoryDownsize->id,
            "img_path" => "./jenis-motor/sport-naked-cruiser.jpg",
        ]);
        $subcategorySNCPRD = Subcategory::create([
            'name' => 'SPORT / NAKED / CRUISER',
            'category_id' => $categoryPaketRBDW->id,
            "img_path" => "./jenis-motor/sport-naked-cruiser.jpg",
        ]);

        // 3. Seed Motors
        // BEBEK / MATIC - Motor populer di Indonesia
        $motorBeatFI = Motor::create(['name' => 'Honda Beat FI', 'img_path' => './motor/Honda-BeAT-FI.jpg', "subcategory" => "BEBEK / MATIC"]);
        $motorBeatStreet = Motor::create(['name' => 'Honda Beat Street', 'img_path' => './motor/honda-beat-street.jpg', "subcategory" => "BEBEK / MATIC"]);
        $motorScoopy = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Honda Scoopy', 'img_path' => './motor/honda.scoopy.jpg.webp']);
        $motorPCX = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Honda PCX', 'img_path' => './motor/honda-pcx.jpg']);
        $motorADV = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Honda ADV', 'img_path' => './motor/Honda-ADV.png']);
        $motorVario110 = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Honda Vario 110', 'img_path' => './motor/honda-vario-110.jpg']);
        $motorVario125 = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Honda Vario 125', 'img_path' => './motor/honda-vario-125.jpg']);
        $motorVario150 = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Honda Vario 150', 'img_path' => './motor/honda-Vario-150.jpg']);
        $motorVario160 = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Honda Vario 160', 'img_path' => './motor/Honda-Vario-160.png']);
        $motorMio = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Yamaha Mio', 'img_path' => './motor/yamaha-mio.jpg.webp']);
        $motorNmax = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Yamaha Nmax', 'img_path' => './motor/yamaha-nmax-.jpg.webp']);
        $motorXmax = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Yamaha Xmax', 'img_path' => './motor/yamaha-xmax.jpg']);
        $motorAerox = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Yamaha Aerox', 'img_path' => './motor/yamaha-aerox.jpg.webp']);
        $motorFino = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Yamaha Fino', 'img_path' => './motor/yamaha-fino.jpg.webp']);
        $motorGear = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Yamaha Gear', 'img_path' => './motor/yamaha-gear.jpg']);
        $motorNex = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Suzuki Nex', 'img_path' => './motor/suzuki-nex.webp']);
        $motorLX125 = Motor::create(["subcategory" => "BEBEK / MATIC", 'name' => 'Vespa LX 125', 'img_path' => './motor/vespa-lx-125.jpeg.webp']);

        // TRAIL / ADVENTURE - Motor populer di Indonesia
        $motorCRF150L = Motor::create(["subcategory" => "TRAIL / ADVENTURE", 'name' => 'Honda CRF150L', 'img_path' => './motor/honda-crf-150l.jpg']);
        $motorCRF250L = Motor::create(["subcategory" => "TRAIL / ADVENTURE", 'name' => 'Honda CRF250L', 'img_path' => './motor/honda-crf-250l.jpg']);
        $motorWR155R = Motor::create(["subcategory" => "TRAIL / ADVENTURE", 'name' => 'Yamaha WR155R', 'img_path' => './motor/yamaha-wr155r.jpeg']);
        $motorKLX150 = Motor::create(["subcategory" => "TRAIL / ADVENTURE", 'name' => 'Kawasaki KLX150', 'img_path' => './motor/kawasaki-KLX-150.jpg']);
        $motorKLX250 = Motor::create(["subcategory" => "TRAIL / ADVENTURE", 'name' => 'Kawasaki KLX250', 'img_path' => './motor/kawasaki-KLX-250.jpg']);

        $motorCBR150R = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Honda CBR150R', 'img_path' => './motor/honda-cbr150r.jpg']);
        $motorForza250 = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Honda Forza', 'img_path' => './motor/honda-forza-250.jpg']);
        $motorCBR250RR = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Honda CBR250RR', 'img_path' => './motor/Honda-New-CBR-250RR.png']);
        $motorCB150R = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Honda CB150R Streetfire', 'img_path' => './motor/honda-cb150r-streetfire.jpg']);
        $motorR15 = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Yamaha YZF-R15', 'img_path' => './motor/yamaha-r15.jpg']);
        $motorR25 = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Yamaha YZF-R25', 'img_path' => './motor/yamaha-r25.webp']);
        $motorMT15 = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Yamaha MT-15', 'img_path' => './motor/yamaha-mt15.webp']);
        $motorXSR155 = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Yamaha XSR155', 'img_path' => './motor/yamaha-xsr155.webp']);
        $motorVixion = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Yamaha Vixion', 'img_path' => './motor/yamaha-vixion.jpg']);
        $motorMXKing = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Yamaha MX King', 'img_path' => './motor/yamaha-mx-king.jpg']);
        $motorNinja250 = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Kawasaki Ninja 250', 'img_path' => './motor/kawasaki-ninja-250.jpg']);
        $motorZ250 = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Kawasaki Z250', 'img_path' => './motor/kawasaki-z250.jpg']);
        $motorW175 = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Kawasaki W175', 'img_path' => './motor/kawasaki-w175.jpg']);
        $motorGSXR150 = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Suzuki GSX-R150', 'img_path' => './motor/suzuki-gsx-r150.png']);
        $motorGSXS150 = Motor::create(["subcategory" => "SPORT / NAKED / CRUISER", 'name' => 'Suzuki GSX-S150', 'img_path' => './motor/suzuki-gsx-s150.webp']);
        // Bebek Matic 110-160CC (MAINTENANCE)
        $motorPartBMMainDSTD110 = MotorPart::create([
            'name' => 'DEPAN STD 110-160CC',
            'price' => 160000,
            'subcategory_id' => $subcategoryBebekMaticMain->id,
        ]);
        $motorPartBMMainDUSD110 = MotorPart::create([
            'name' => 'DEPAN USD 110-160CC',
            'price' => 220000,
            'subcategory_id' => $subcategoryBebekMaticMain->id,
        ]);
        $motorPartBMMainBSSTDAM110 = MotorPart::create([
            'name' => 'BELAKANG (S) STD / AM 110-160CC',
            'price' => 160000,
            'subcategory_id' => $subcategoryBebekMaticMain->id,
        ]);
        $motorPartBMMainBDSTD110 = MotorPart::create([
            'name' => 'BELAKANG (D) STD 110-160CC',
            'price' => 200000,
            'subcategory_id' => $subcategoryBebekMaticMain->id,
        ]);
        $motorPartBMMainBDAM110 = MotorPart::create([
            'name' => 'BELAKANG (D) AM 110-160CC',
            'price' => 250000,
            'subcategory_id' => $subcategoryBebekMaticMain->id,
        ]);

        // Bebek Matic 200-250CC (MAINTENANCE)
        $motorPartBMMainDSTD200 = MotorPart::create([
            'name' => 'DEPAN STD 200-250CC',
            'price' => 210000,
            'subcategory_id' => $subcategoryBebekMaticMain->id,
        ]);
        $motorPartBMMainDUSD200 = MotorPart::create([
            'name' => 'DEPAN USD 200-250CC',
            'price' => 270000,
            'subcategory_id' => $subcategoryBebekMaticMain->id,
        ]);
        $motorPartBMMainBDSTDAM200 = MotorPart::create([
            'name' => 'BELAKANG (D) STD / AM 200-250CC',
            'price' => 210000,
            'subcategory_id' => $subcategoryBebekMaticMain->id,
        ]);
        $motorPartBMMainBDSTD200 = MotorPart::create([
            'name' => 'BELAKANG (D) STD 200-250CC',
            'price' => 250000,
            'subcategory_id' => $subcategoryBebekMaticMain->id,
        ]);
        $motorPartBMMainBDAM200 = MotorPart::create([
            'name' => 'BELAKANG (D) AM 200-250CC',
            'price' => 300000,
            'subcategory_id' => $subcategoryBebekMaticMain->id,
        ]);
        // Bebek Matic 110-160CC (REBOUND)
        $motorPartBMRebDSTD110 = MotorPart::create([
            'name' => 'DEPAN STD 110-160CC',
            'price' => 300000,
            'subcategory_id' => $subcategoryBebekMaticReb->id,
        ]);
        $motorPartBMRebDUSD110 = MotorPart::create([
            'name' => 'DEPAN USD 110-160CC',
            'price' => 400000,
            'subcategory_id' => $subcategoryBebekMaticReb->id,
        ]);
        $motorPartBMRebBSSTDAM110 = MotorPart::create([
            'name' => 'BELAKANG (S) STD / AM 110-160CC',
            'price' => 300000,
            'subcategory_id' => $subcategoryBebekMaticReb->id,
        ]);
        $motorPartBMRebBDSTDAM110 = MotorPart::create([
            'name' => 'BELAKANG (D) STD / AM 110-160CC',
            'price' => 400000,
            'subcategory_id' => $subcategoryBebekMaticReb->id,
        ]);
        // Bebek Matic 200-250CC (REBOUND)
        $motorPartBMRebDSTD200 = MotorPart::create([
            'name' => 'DEPAN STD 200-250CC',
            'price' => 400000,
            'subcategory_id' => $subcategoryBebekMaticReb->id,
        ]);
        $motorPartBMRebDUSD200 = MotorPart::create([
            'name' => 'DEPAN USD 200-250CC',
            'price' => 500000,
            'subcategory_id' => $subcategoryBebekMaticReb->id,
        ]);
        $motorPartBMRebBDSTDAM200 = MotorPart::create([
            'name' => 'BELAKANG (D) STD / AM 200-250CC',
            'price' => 500000,
            'subcategory_id' => $subcategoryBebekMaticReb->id,
        ]);
        // Bebek Matic 110-160CC (DOWNSIZE)
        $motorPartBMDownDSTD110 = MotorPart::create([
            'name' => 'DEPAN STD 110-160CC',
            'price' => 250000,
            'subcategory_id' => $subcategoryBebekMaticDown->id,
        ]);
        $motorPartBMDownDUSD110 = MotorPart::create([
            'name' => 'DEPAN USD 110-160CC',
            'price' => 400000,
            'subcategory_id' => $subcategoryBebekMaticDown->id,
        ]);
        $motorPartBMDownBSSTDAM110 = MotorPart::create([
            'name' => 'BELAKANG (S) STD / AM 110-160CC',
            'price' => 300000,
            'subcategory_id' => $subcategoryBebekMaticDown->id,
        ]);
        $motorPartBMDownBDSTDAM110 = MotorPart::create([
            'name' => 'BELAKANG (D) STD / AM 110-160CC',
            'price' => 400000,
            'subcategory_id' => $subcategoryBebekMaticDown->id,
        ]);
        // Bebek Matic 200-250CC (DOWNSIZE)
        $motorPartBMDownDSTD200 = MotorPart::create([
            'name' => 'DEPAN STD 200-250CC',
            'price' => 450000,
            'subcategory_id' => $subcategoryBebekMaticDown->id,
        ]);
        $motorPartBMDownDUSD200 = MotorPart::create([
            'name' => 'DEPAN USD 200-250CC',
            'price' => 500000,
            'subcategory_id' => $subcategoryBebekMaticDown->id,
        ]);
        $motorPartBMDownBDSTDAM200 = MotorPart::create([
            'name' => 'BELAKANG (D) STD / AM 200-250CC',
            'price' => 500000,
            'subcategory_id' => $subcategoryBebekMaticDown->id,
        ]);
        // Bebek Matic 110-160CC (PAKET RB & DZ)
        $motorPartBMPaketRDDSTD110 = MotorPart::create([
            'name' => 'DEPAN STD 110-160CC',
            'price' => 400000,
            'subcategory_id' => $subcategoryBebekMaticPRD->id,
        ]);
        $motorPartBMPaketRDDUSD110 = MotorPart::create([
            'name' => 'DEPAN USD 110-160CC',
            'price' => 500000,
            'subcategory_id' => $subcategoryBebekMaticPRD->id,
        ]);
        $motorPartBMPaketRDBSSTDAM110 = MotorPart::create([
            'name' => 'BELAKANG (S) STD / AM 110-160CC',
            'price' => 400000,
            'subcategory_id' => $subcategoryBebekMaticPRD->id,
        ]);
        $motorPartBMPaketRDBDSTDAM110 = MotorPart::create([
            'name' => 'BELAKANG (D) STD / AM 110-160CC',
            'price' => 500000,
            'subcategory_id' => $subcategoryBebekMaticPRD->id,
        ]);
        // Bebek Matic 200-250CC (PAKET RB & DZ)
        $motorPartBMPaketRDDSTD200 = MotorPart::create([
            'name' => 'DEPAN STD 200-250CC',
            'price' => 500000,
            'subcategory_id' => $subcategoryBebekMaticPRD->id,
        ]);
        $motorPartBMPaketRDDUSD200 = MotorPart::create([
            'name' => 'DEPAN USD 200-250CC',
            'price' => 600000,
            'subcategory_id' => $subcategoryBebekMaticPRD->id,
        ]);
        $motorPartBMPaketRDBDSTDAM200 = MotorPart::create([
            'name' => 'BELAKANG (D) STD / AM 200-250CC',
            'price' => 600000,
            'subcategory_id' => $subcategoryBebekMaticPRD->id,
        ]);

        // OHLINS MATIC (MAINTENANCE)
        $motorPartOMMainSingle = MotorPart::create([
            'name' => 'SINGLE',
            'price' => 300000,
            'subcategory_id' => $subcategoryOhlinsMaticMain->id,
        ]);
        $motorPartOMMainDouble = MotorPart::create([
            'name' => 'DOUBLE',
            'price' => 400000,
            'subcategory_id' => $subcategoryOhlinsMaticMain->id,
        ]);
        // OHLINS MATIC (REBOUND)
        $motorPartOMReboundSingle = MotorPart::create([
            'name' => 'SINGLE',
            'price' => 500000,
            'subcategory_id' => $subcategoryOhlinsMaticReb->id,
        ]);
        $motorPartOMReboundDouble = MotorPart::create([
            'name' => 'DOUBLE',
            'price' => 650000,
            'subcategory_id' => $subcategoryOhlinsMaticReb->id,
        ]);

        // OHLINS SPORT/NAKED/CRUISER (MAINTENANCE)
        $motorPartOSNCMainSingle = MotorPart::create([
            'name' => 'SINGLE',
            'price' => 400000,
            'subcategory_id' => $subcategoryOhlinsSNCMain->id,
        ]);
        $motorPartOSNCMainDouble = MotorPart::create([
            'name' => 'DOUBLE',
            'price' => 500000,
            'subcategory_id' => $subcategoryOhlinsSNCMain->id,
        ]);
        // OHLINS SPORT/NAKED/CRUISER (REBOUND)
        $motorPartOSNCRebSingle = MotorPart::create([
            'name' => 'SINGLE',
            'price' => 600000,
            'subcategory_id' => $subcategoryOhlinsSNCReb->id,
        ]);
        $motorPartOSNCRebDouble = MotorPart::create([
            'name' => 'DOUBLE',
            'price' => 750000,
            'subcategory_id' => $subcategoryOhlinsSNCReb->id,
        ]);

        // OHLINS TRAIL / ADVENTURE (MAINTENANCE)
        $motorPartOTAMainSingle = MotorPart::create([
            'name' => 'SINGLE',
            'price' => 450000,
            'subcategory_id' => $subcategoryOhlinsTAMain->id,
        ]);
        $motorPartOTAMainDouble = MotorPart::create([
            'name' => 'DOUBLE',
            'price' => 550000,
            'subcategory_id' => $subcategoryOhlinsTAMain->id,
        ]);
        // OHLINS TRAIL / ADVENTURE (REBOUND)
        $motorPartOTARebSingle = MotorPart::create([
            'name' => 'SINGLE',
            'price' => 650000,
            'subcategory_id' => $subcategoryOhlinsTAReb->id,
        ]);
        $motorPartOTARebDouble = MotorPart::create([
            'name' => 'DOUBLE',
            'price' => 800000,
            'subcategory_id' => $subcategoryOhlinsTAReb->id,
        ]);

        // TRAIL / ADVENTURE 150-250CC (MAINTENANCE)
        $motorPartTAMainDT = MotorPart::create([
            'name' => 'DEPAN TELESKOPIK',
            'price' => 250000,
            'subcategory_id' => $subcategoryTAMain->id,
        ]);
        $motorPartTAMainDUSD = MotorPart::create([
            'name' => 'DEPAN USD',
            'price' => 300000,
            'subcategory_id' => $subcategoryTAMain->id,
        ]);
        $motorPartTAMainDAMRJ = MotorPart::create([
            'name' => 'DEPAN AM / REAL JUMP',
            'price' => 400000,
            'subcategory_id' => $subcategoryTAMain->id,
        ]);
        $motorPartTAMainBSSTD = MotorPart::create([
            'name' => 'BELAKANG (S) STD',
            'price' => 300000,
            'subcategory_id' => $subcategoryTAMain->id,
        ]);
        $motorPartTAMainBDSTDAM = MotorPart::create([
            'name' => 'BELAKANG (D) STD / AM',
            'price' => 300000,
            'subcategory_id' => $subcategoryTAMain->id,
        ]);

        // TRAIL / ADVENTURE 150-250CC (REBOUNB)
        $motorPartTARebDT = MotorPart::create([
            'name' => 'DEPAN TELESKOPIK',
            'price' => 400000,
            'subcategory_id' => $subcategoryTAReb->id,
        ]);
        $motorPartTARebDUSD = MotorPart::create([
            'name' => 'DEPAN USD',
            'price' => 450000,
            'subcategory_id' => $subcategoryTAReb->id,
        ]);
        $motorPartTARebDAMRJ = MotorPart::create([
            'name' => 'DEPAN AM / REAL JUMP',
            'price' => 500000,
            'subcategory_id' => $subcategoryTAReb->id,
        ]);
        $motorPartTARebBSTD = MotorPart::create([
            'name' => 'BELAKANG STD',
            'price' => 400000,
            'subcategory_id' => $subcategoryTAReb->id,
        ]);
        $motorPartTARebBAM = MotorPart::create([
            'name' => 'BELAKANG AM',
            'price' => 500000,
            'subcategory_id' => $subcategoryTAReb->id,
        ]);

        // TRAIL / ADVENTURE 150-250CC (DOWNSIZE)
        $motorPartTADownDT = MotorPart::create([
            'name' => 'DEPAN TELESKOPIK',
            'price' => 450000,
            'subcategory_id' => $subcategoryTADown->id,
        ]);
        $motorPartTADownDUSD = MotorPart::create([
            'name' => 'DEPAN USD',
            'price' => 550000,
            'subcategory_id' => $subcategoryTADown->id,
        ]);
        $motorPartTADownDAMRJ = MotorPart::create([
            'name' => 'DEPAN AM / REAL JUMP',
            'price' => 600000,
            'subcategory_id' => $subcategoryTADown->id,
        ]);
        $motorPartTADownBSTD = MotorPart::create([
            'name' => 'BELAKANG STD',
            'price' => 450000,
            'subcategory_id' => $subcategoryTADown->id,
        ]);
        $motorPartTADownBDSTDAM = MotorPart::create([
            'name' => 'BELAKANG (D) STD / AM',
            'price' => 500000,
            'subcategory_id' => $subcategoryTADown->id,
        ]);

        // TRAIL / ADVENTURE 150-250CC (PAKET RB & DZ)
        $motorPartTAPRDDT = MotorPart::create([
            'name' => 'DEPAN TELESKOPIK',
            'price' => 500000,
            'subcategory_id' => $subcategoryTAPRD->id,
        ]);
        $motorPartTAPRDDUSD = MotorPart::create([
            'name' => 'DEPAN USD',
            'price' => 600000,
            'subcategory_id' => $subcategoryTAPRD->id,
        ]);
        $motorPartTAPRDDAMRJ = MotorPart::create([
            'name' => 'DEPAN AM / REAL JUMP',
            'price' => 700000,
            'subcategory_id' => $subcategoryTAPRD->id,
        ]);
        $motorPartTAPRDBSTD = MotorPart::create([
            'name' => 'BELAKANG STD',
            'price' => 550000,
            'subcategory_id' => $subcategoryTAPRD->id,
        ]);
        $motorPartTAPRDBDSTDAM = MotorPart::create([
            'name' => 'BELAKANG (D) STD / AM',
            'price' => 600000,
            'subcategory_id' => $subcategoryTAPRD->id,
        ]);

        // SPORT / NAKED / CRUISER 150-250CC (MAINTENANCE)
        $motorPartSNCMainDT = MotorPart::create([
            'name' => 'DEPAN TELESKOPIK',
            'price' => 250000,
            'subcategory_id' => $subcategorySNCMain->id,
        ]);
        $motorPartSNCMainDUSD = MotorPart::create([
            'name' => 'DEPAN USD',
            'price' => 300000,
            'subcategory_id' => $subcategorySNCMain->id,
        ]);
        $motorPartSNCMainBSTD = MotorPart::create([
            'name' => 'BELAKANG STD',
            'price' => 300000,
            'subcategory_id' => $subcategorySNCMain->id,
        ]);
        $motorPartSNCMainBDSTDAM = MotorPart::create([
            'name' => 'BELAKANG (D) STD / AM',
            'price' => 300000,
            'subcategory_id' => $subcategorySNCMain->id,
        ]);

        // SPORT / NAKED / CRUISER 150-250CC (REBOUND)
        $motorPartSNCRebDT = MotorPart::create([
            'name' => 'DEPAN TELESKOPIK',
            'price' => 450000,
            'subcategory_id' => $subcategorySNCReb->id,
        ]);
        $motorPartSNCRebDUSD = MotorPart::create([
            'name' => 'DEPAN USD',
            'price' => 500000,
            'subcategory_id' => $subcategorySNCReb->id,
        ]);
        $motorPartSNCRebBSTD = MotorPart::create([
            'name' => 'BELAKANG STD',
            'price' => 400000,
            'subcategory_id' => $subcategorySNCReb->id,
        ]);
        $motorPartSNCRebDAfter = MotorPart::create([
            'name' => 'DEPAN AFTERMARKET',
            'price' => 500000,
            'subcategory_id' => $subcategorySNCReb->id,
        ]);

        // SPORT / NAKED / CRUISER 150-250CC (DOWNSIZE)
        $motorPartSNCDownDT = MotorPart::create([
            'name' => 'DEPAN TELESKOPIK',
            'price' => 400000,
            'subcategory_id' => $subcategorySNCDown->id,
        ]);
        $motorPartSNCDownDUSD = MotorPart::create([
            'name' => 'DEPAN USD',
            'price' => 500000,
            'subcategory_id' => $subcategorySNCDown->id,
        ]);
        $motorPartSNCDownBSTD = MotorPart::create([
            'name' => 'BELAKANG STD',
            'price' => 450000,
            'subcategory_id' => $subcategorySNCDown->id,
        ]);
        $motorPartSNCDownBDSTDAM = MotorPart::create([
            'name' => 'BELAKANG (D) STD / AM',
            'price' => 500000,
            'subcategory_id' => $subcategorySNCDown->id,
        ]);

        // SPORT / NAKED / CRUISER 150-250CC (PAKET RB & DZ)
        $motorPartSNCPRDDT = MotorPart::create([
            'name' => 'DEPAN TELESKOPIK',
            'price' => 550000,
            'subcategory_id' => $subcategorySNCPRD->id,
        ]);
        $motorPartSNCPRDDUSD = MotorPart::create([
            'name' => 'DEPAN USD',
            'price' => 650000,
            'subcategory_id' => $subcategorySNCPRD->id,
        ]);
        $motorPartSNCPRDBSTD = MotorPart::create([
            'name' => 'BELAKANG STD',
            'price' => 550000,
            'subcategory_id' => $subcategorySNCPRD->id,
        ]);
        $motorPartSNCPRDBDSTDAM = MotorPart::create([
            'name' => 'BELAKANG (D) STD / AM',
            'price' => 700000,
            'subcategory_id' => $subcategorySNCPRD->id,
        ]);

        // MAINTENANCE - BEBEK MATIC
        $motorPartBMMainDSTD110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMMainDSTD200->motors()->attach([$motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartBMMainDUSD110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMMainDUSD200->motors()->attach([$motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartBMMainBSSTDAM110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMMainBDSTD110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMMainBDAM110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMMainBDSTDAM200->motors()->attach([$motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartBMMainBDSTD200->motors()->attach([$motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartBMMainBDAM200->motors()->attach([$motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        // REBOUND - BEBEK MATIC
        $motorPartBMRebDSTD110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMRebDUSD110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMRebBSSTDAM110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMRebBDSTDAM110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMRebDSTD200->motors()->attach([$motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartBMRebDUSD200->motors()->attach([$motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartBMRebBDSTDAM200->motors()->attach([$motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        // DOWNSIZE - BEBEK MATIC
        $motorPartBMDownDSTD110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMDownDUSD110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMDownBSSTDAM110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMDownBDSTDAM110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMDownDSTD200->motors()->attach([$motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartBMDownDUSD200->motors()->attach([$motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartBMDownBDSTDAM200->motors()->attach([$motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        // PAKET RB & DZ - BEBEK MATIC
        $motorPartBMPaketRDDSTD110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMPaketRDDUSD110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMPaketRDBSSTDAM110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMPaketRDBDSTDAM110->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartBMPaketRDDSTD200->motors()->attach([$motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartBMPaketRDDUSD200->motors()->attach([$motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartBMPaketRDBDSTDAM200->motors()->attach([$motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);

        // MAINTENANCE - SPORT / NAKED / CRUISER
        $motorPartSNCMainDT->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartSNCMainDUSD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartSNCMainBSTD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartSNCMainBDSTDAM->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        // REBOUND
        $motorPartSNCRebDT->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartSNCRebDUSD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartSNCRebBSTD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartSNCRebDAfter->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        // DOWNSIZE
        $motorPartSNCDownDT->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartSNCDownDUSD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartSNCDownBSTD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartSNCDownBDSTDAM->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        // PAKET RB & DZ
        $motorPartSNCPRDDT->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartSNCPRDDUSD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartSNCPRDBSTD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartSNCPRDBDSTDAM->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);

        // TRAIL ADVENTURE - MAINTENANCE
        $motorPartTAMainDT->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTAMainDUSD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTAMainDAMRJ->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTAMainBSSTD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTAMainBDSTDAM->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        // REBOUND TRAIL ADVENTURE
        $motorPartTARebDT->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTARebDUSD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTARebDAMRJ->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTARebBSTD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTARebBAM->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        // DOWNSIZE TA
        $motorPartTADownDT->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTADownDUSD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTADownDAMRJ->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTADownBSTD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTADownBDSTDAM->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        // PAKET RB & DZ TA
        $motorPartTAPRDDT->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTAPRDDUSD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTAPRDDAMRJ->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTAPRDBSTD->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);
        $motorPartTAPRDBDSTDAM->motors()->attach([$motorVario150->id, $motorVario160->id, $motorNmax->id, $motorAerox->id, $motorForza250->id, $motorCBR250RR->id, $motorCRF250L->id, $motorWR155R->id, $motorR25->id, $motorKLX250->id, $motorNinja250->id, $motorZ250->id]);

        // OHLINS MATIC - MAINTENANCE
        $motorPartOMMainSingle->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartOMMainDouble->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        // OHLINS MATIC - REBOUND
        $motorPartOMReboundSingle->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartOMReboundDouble->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);

        // OHLINS SPORT/NAKED/CRUISER - MAINTENANCE
        $motorPartOSNCMainSingle->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartOSNCMainDouble->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        // OHLINS SPORT/NAKED/CRUISER - REBOUND
        $motorPartOSNCRebSingle->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartOSNCRebDouble->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);

        // OHLINS TRAIL/ADVENTURE - MAINTENANCE
        $motorPartOTAMainSingle->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartOTAMainDouble->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        // OHLINS TRAIL/ADVENTURE - REBOUND
        $motorPartOTARebSingle->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);
        $motorPartOTARebDouble->motors()->attach([$motorBeatFI->id, $motorBeatStreet->id, $motorScoopy->id, $motorVario110->id, $motorVario125->id, $motorVario150->id, $motorVario160->id, $motorMio->id, $motorNmax->id, $motorAerox->id]);

        // 5. Seed Gerais
        $geraiBekasi = Gerai::create([
            'name' => 'Bekasi',
            'location' => 'Bekasi',
        ]);
        $geraiDepok = Gerai::create([
            'name' => 'Depok',
            'location' => 'Depok',
        ]);
        $geraiBogor = Gerai::create([
            'name' => 'Bogor',
            'location' => 'Bogor',
        ]);
        $geraiCikarang = Gerai::create([
            'name' => 'Cikarang',
            'location' => 'Cikarang',
        ]);
        $geraiJakartaTimur = Gerai::create([
            'name' => 'Jaktim',
            'location' => 'Jakarta Timur',
        ]);
        $geraiJakartaSelatan = Gerai::create([
            'name' => 'Jaksel',
            'location' => 'Jakarta Selatan',
        ]);
        $geraiJakartaBarat = Gerai::create([
            'name' => 'Jakbar',
            'location' => 'Jakarta Barat',
        ]);
        $geraiTangerang = Gerai::create([
            'name' => 'Tangerang',
            'location' => 'Tangerang',
        ]);
        User::insert([["username" => "admin pusat", "password" => Hash::make("12345"), "gerai_id" => 1, "role" => "ADMIN"], ["username" => "admin depok", "password" => Hash::make("12345"), "role" => "ADMIN", "gerai_id" => 2], ["username" => "admin bogor", "password" => Hash::make("12345"), "role" => "ADMIN", "gerai_id" => 3], ["username" => "admin cikarang", "password" => Hash::make("12345"), "role" => "ADMIN", "gerai_id" => 4], ["username" => "admin jaktim", "password" => Hash::make("12345"), "role" => "ADMIN", "gerai_id" => 5], ["username" => "admin jaksel", "password" => Hash::make("12345"), "role" => "ADMIN", "gerai_id" => 6], ["username" => "admin jakbar", "password" => Hash::make("12345"), "role" => "ADMIN", "gerai_id" => 7], ["username" => "admin tangerang", "password" => Hash::make("12345"), "role" => "ADMIN", "gerai_id" => 8],  ["username" => "purchasing", "password" => Hash::make("12345"), "gerai_id" => null, "role" => "GUDANG"], ["username" => "ceo", "password" => Hash::make("12345"), "gerai_id" => null, "role" => "CEO"]]);

        Sparepart::insert([
            // SEAL DEPAN Category (1 SET - price dibagi 2)
            [
                "name" => "BK6 / R15",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 60000, // 120000 / 2
                "purchase_price" => 18000,
                "motor_id" => null,
            ],
            [
                "name" => "Dtracker / KLX",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 60000, // 120000 / 2
                "purchase_price" => 7500,
                "motor_id" => null,
            ],
            [
                "name" => "3HB",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 40000, // 80000 / 2
                "purchase_price" => 13000,
                "motor_id" => null,
            ],
            [
                "name" => "3CI / 5BP (33-34-10,5)",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 50000, // 100000 / 2
                "purchase_price" => 13000,
                "motor_id" => null,
            ],
            [
                "name" => "GN5",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 30000, // 60000 / 2
                "purchase_price" => 13000,
                "motor_id" => null,
            ],
            [
                "name" => "KC5",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 50000, // 100000 / 2
                "purchase_price" => 18000,
                "motor_id" => null,
            ],
            [
                "name" => "K84 / KWL",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 62500, // 125000 / 2
                "purchase_price" => 18000,
                "motor_id" => null,
            ],
            [
                "name" => "KTC USD",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 50000, // 100000 / 2
                "purchase_price" => 50000,
                "motor_id" => null,
            ],
            [
                "name" => "Ninja 250 / K84",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 67500, // 135000 / 2
                "purchase_price" => 18000,
                "motor_id" => null,
            ],
            [
                "name" => "Ninja 2 Tak",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 50000, // 100000 / 2
                "purchase_price" => 20000,
                "motor_id" => null,
            ],
            [
                "name" => "VESMET (14-34-14)",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 30000, // 60000 / 2
                "purchase_price" => 13000,
                "motor_id" => null,
            ],
            [
                "name" => "45P",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 62500, // 125000 / 2
                "purchase_price" => 20000,
                "motor_id" => null,
            ],

            // SEAL BELAKANG Category
            [
                "name" => "Seal ADV",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 4000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal Byson 14 41",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 18000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 10 30 12",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 8500,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 10 28 7",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 10000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 10 28 13,4",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 3800,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 12 24 5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 60000,
                "purchase_price" => 5000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 12 26 7",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 6000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 12 37 12",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 8000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 12,5 36 12",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 6500,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 12,5 32 15",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 8250,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 12,5 24 5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 60000,
                "purchase_price" => 9000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 12,5 35 12",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 8000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 12.5 37 12",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 6800,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 12.20.5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 4500,
                "motor_id" => null,
            ],
            [
                "name" => "Seal LBH 12 20",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 6000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal LBH 14",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 4500,
                "motor_id" => null,
            ],
            [
                "name" => "Seal NMAX",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 4000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal Ninja 12 27 5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 60000,
                "purchase_price" => 9000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal OHLINS",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 80000,
                "purchase_price" => 30000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 14 22 5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "purchase_price" => 4500,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 14 24 NOK",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 6000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 14 36 12",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 11000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 14 27 7",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 10000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal 14 30 5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 60000,
                "purchase_price" => 20000,
                "motor_id" => null,
            ],
            [
                "name" => "Seal Vesmet 10 28 13,4",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 65000,
                "purchase_price" => 3800,
                "motor_id" => null,
            ],
            [
                "name" => "Seal YSS 12 31,5 15",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 8250,
                "motor_id" => null,
            ],
            [
                "name" => "Seal KYB Elite 15 28 10",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 60000,
                "purchase_price" => 28000,
                "motor_id" => null,
            ],

            // AS DEPAN Category
            [
                "name" => "AS Vesmet",
                "category" => "AS DEPAN",
                "qty" => 100,
                "price" => 80000,
                "purchase_price" => 25000,
                "motor_id" => null,
            ],

            // AS BELAKANG Category
            [
                "name" => "AS NMAX",
                "category" => "AS BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 7500,
                "motor_id" => null,
            ],
            [
                "name" => "AS YSS",
                "category" => "AS BELAKANG",
                "qty" => 100,
                "price" => 80000,
                "purchase_price" => 17500,
                "motor_id" => null,
            ],
            [
                "name" => "AS ADV",
                "category" => "AS BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "purchase_price" => 8000,
                "motor_id" => null,
            ],
            [
                "name" => "AS SHOCK STANDAR VARIO",
                "category" => "AS BELAKANG",
                "qty" => 100,
                "price" => 30000,
                "purchase_price" => 8000,
                "motor_id" => null,
            ],
            [
                "name" => "AS KTC EXTREME BERONGGA",
                "category" => "AS BELAKANG",
                "qty" => 100,
                "price" => 200000,
                "purchase_price" => 160000,
                "motor_id" => null,
            ],

            // PER DOWNSIZE Category
            [
                "name" => "HONDA",
                "category" => "PER DOWNSIZE",
                "qty" => 100,
                "price" => 15000,
                "purchase_price" => 7000,
                "motor_id" => null,
            ],
            [
                "name" => "NMAX / PCX",
                "category" => "PER DOWNSIZE",
                "qty" => 100,
                "price" => 25000,
                "purchase_price" => 15000,
                "motor_id" => null,
            ],
            [
                "name" => "X-MAX",
                "category" => "PER DOWNSIZE",
                "qty" => 100,
                "price" => 25000,
                "purchase_price" => 15000,
                "motor_id" => null,
            ],
            [
                "name" => "YAMAHA",
                "category" => "PER DOWNSIZE",
                "qty" => 100,
                "price" => 15000,
                "purchase_price" => 7000,
                "motor_id" => null,
            ],

            // OLI TURALIT Category
            [
                "name" => "Oli 200ml",
                "category" => "OLI TURALIT",
                "qty" => 100,
                "price" => 10000,
                "purchase_price" => 6700000, // This seems like an error in the original data
                "motor_id" => null,
            ],
            [
                "name" => "Oli 500ml",
                "category" => "OLI TURALIT",
                "qty" => 100,
                "price" => 25000,
                "purchase_price" => 6700000, // This seems like an error in the original data
                "motor_id" => null,
            ],
        ]);

        Seal::insert([
            // SEAL Category - dari foto 2
            [
                "name" => "3HB - 30x40,5x10,5",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 50000, // Harga jual dari foto
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 1
            ],
            [
                "name" => "GN5 - 26x37x10,5",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 50000, // Harga jual dari foto
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 2
            ],
            [
                "name" => "KC5 - 31x43x10,5",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 120000, // Harga jual dari foto
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 3
            ],
            [
                "name" => "K84 CRF - 37x50x10",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 230000, // Harga jual dari foto
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 4
            ],
            [
                "name" => "KWL - 37x50x11",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 240000, // Harga jual dari foto
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 5
            ],
            [
                "name" => "3CI/5BP - 33x45x10,5",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 140000, // Harga jual dari foto
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 6
            ],
            [
                "name" => "Vesmet Std - 14x34x14",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 65000, // Harga jual dari foto
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 7
            ],
            [
                "name" => "KLX TELECOPIC - 33x46x11",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 25000, // Estimasi dari range harga
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 8
            ],
            [
                "name" => "Seal depan B48 R15 - 37x49,1X10",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 300000, // Harga jual dari foto
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 9
            ],
            [
                "name" => "45p 3145-00",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 200000, // Harga jual dari foto
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 10
            ],
            [
                "name" => "USD Real Jump - 48x58x10,5",
                "category" => "SEAL DEPAN",
                "qty" => 100,
                "price" => 480000, // Harga jual dari foto
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 11
            ],
            [
                "name" => "LBH 12 - 12x20X4,5/6",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 12
            ],
            [
                "name" => "LBH 14 - 14 X 22 X 4,5/6",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 13
            ],
            [
                "name" => "YSS - 12x31,5x15",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 14
            ],
            [
                "name" => "YSS - 12,5x32x15",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 50000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 15
            ],
            [
                "name" => "Ohlins - 14x24x5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 80000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 16
            ],
            [
                "name" => "USH - 14x22x5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 17
            ],
            [
                "name" => "NOK - 10x30x12",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 18
            ],
            [
                "name" => "RSD ST - 14x36x12",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 19
            ],
            [
                "name" => "USH CRF/KLX blkng - 12x24x5",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 60000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 20
            ],
            [
                "name" => "UHN - 14x24x6",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 65000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 21
            ],
            [
                "name" => "Seal KYB - 15x28x10",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 70000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 22
            ],
            [
                "name" => "Seal Vesmet blkng - 10x28x13,4",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 65000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 23
            ],
            [
                "name" => "Seal belakang NMAX",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 24
            ],
            [
                "name" => "Seal belakang ADV",
                "category" => "SEAL BELAKANG",
                "qty" => 100,
                "price" => 40000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 25
            ],
            // AS Category - dari foto 1
            [
                "name" => "AS belakang NMAX",
                "category" => "AS BELAKANG",
                "qty" => 100,
                "price" => 60000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 26
            ],
            [
                "name" => "AS belakang ADV",
                "category" => "AS BELAKANG",
                "qty" => 100,
                "price" => 60000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 27
            ],
            [
                "name" => "AS belakang CRF",
                "category" => "AS BELAKANG",
                "qty" => 100,
                "price" => 80000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 28
            ],
            [
                "name" => "AS Depan Vario",
                "category" => "AS DEPAN",
                "qty" => 100,
                "price" => 350000, // Harga jual dari foto (set)
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 29
            ],
            [
                "name" => "AS Depan PCX",
                "category" => "AS DEPAN",
                "qty" => 100,
                "price" => 500000, // Harga jual dari foto (set)
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 30
            ],
            [
                "name" => "AS Depan NMAX",
                "category" => "AS DEPAN",
                "qty" => 100,
                "price" => 600000, // Harga jual dari foto (set)
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 31
            ],
            [
                "name" => "AS Depan VCx 150",
                "category" => "AS DEPAN",
                "qty" => 100,
                "price" => 500000, // Harga jual dari foto (set)
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 32
            ],
            [
                "name" => "AS Depan VCCX 160",
                "category" => "AS DEPAN",
                "qty" => 100,
                "price" => 600000, // Harga jual dari foto (set)
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 33
            ],
            [
                "name" => "AS Depan AEROX",
                "category" => "AS DEPAN",
                "qty" => 100,
                "price" => 80000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 34
            ],
            [
                "name" => "AS depan vesmet standar",
                "category" => "AS DEPAN",
                "qty" => 100,
                "price" => 50000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 35
            ],
            [
                "name" => "As YSS",
                "category" => "AS",
                "qty" => 100,
                "price" => 70000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 36
            ],
            // PER Category - dari foto 1
            [
                "name" => "Per Downsize",
                "category" => "PER",
                "qty" => 100,
                "price" => 15000, // Harga jual dari foto (set)
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 37
            ],
            [
                "name" => "Per downsize aerox",
                "category" => "PER",
                "qty" => 100,
                "price" => 27000, // Harga jual dari foto (set)
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 38
            ],
            [
                "name" => "Per downsize NMAX/PCX",
                "category" => "PER",
                "qty" => 100,
                "price" => 150000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 39
            ],
            [
                "name" => "Per Downsize YSS",
                "category" => "PER",
                "qty" => 100,
                "price" => 15000, // Harga jual dari foto (set)
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 40
            ],
            [
                "name" => "Per downsize vario",
                "category" => "PER",
                "qty" => 100,
                "price" => 15000, // Harga jual dari foto (set)
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 41
            ],
            // OLI Category - dari foto 1
            [
                "name" => "200ML",
                "category" => "OLI",
                "qty" => 100,
                "price" => 150000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 42
            ],
            [
                "name" => "500ML",
                "category" => "OLI",
                "qty" => 100,
                "price" => 150000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 43
            ],
            [
                "name" => "Karet blader YSS",
                "category" => "ACC",
                "qty" => 100,
                "price" => 72000,
                "motor_id" => null,
                "gerai_id" => 1,
                "sparepart_id" => 44
            ],
        ]);
        // $warehouseSealBeatStreetGN5 = WarehouseSeal::create([
        //     'cc_range' => 'GN5',
        //     'price' => 60000,
        //     'qty' => 100,
        //     'motor_id' => $motorBeatStreet->id,
        // ]);
        // $warehouseSealPCXKC5 = WarehouseSeal::create([
        //     'cc_range' => 'KC5',
        //     'price' => 120000,
        //     'qty' => 100,
        //     'motor_id' => $motorPCX->id,
        // ]);
        // $warehouseSealV125YSSB = WarehouseSeal::create([
        //     'cc_range' => 'YSS BELAKANG',
        //     'price' => 40000,
        //     'qty' => 100,
        //     'motor_id' => $motorVario125->id,
        // ]);
        // $warehouseSealScoopyYSSB = WarehouseSeal::create([
        //     'cc_range' => 'YSS BELAKANG',
        //     'price' => 40000,
        //     'qty' => 100,
        //     'motor_id' => $motorScoopy->id,
        // ]);
        // $warehouseSealBeatFIYSSB = WarehouseSeal::create([
        //     'cc_range' => 'YSS BELAKANG',
        //     'price' => 40000,
        //     'qty' => 100,
        //     'motor_id' => $motorBeatFI->id,
        // ]);
        // $warehouseSealBeatStYSSB = WarehouseSeal::create([
        //     'cc_range' => 'YSS BELAKANG',
        //     'price' => 40000,
        //     'qty' => 100,
        //     'motor_id' => $motorBeatStreet->id,
        // ]);
        // $warehouseSealAsNMAX = WarehouseSeal::create([
        //     'cc_range' => 'AS NMAX BELAKANG',
        //     'price' => 60000,
        //     'qty' => 100,
        //     'motor_id' => $motorNmax->id,
        // ]);
        // $warehouseSealNMAX = WarehouseSeal::create([
        //     'cc_range' => 'NMAX BELAKANG',
        //     'price' => 40000,
        //     'qty' => 100,
        //     'motor_id' => $motorNmax->id,
        // ]);
        // $warehouseSealAsADV = WarehouseSeal::create([
        //     'cc_range' => 'AS ADV BELAKANG',
        //     'price' => 60000,
        //     'qty' => 100,
        //     'motor_id' => $motorADV->id,
        // ]);
        // $warehouseSealADV = WarehouseSeal::create([
        //     'cc_range' => 'ADV BELAKANG',
        //     'price' => 40000,
        //     'qty' => 100,
        //     'motor_id' => $motorADV->id,
        // ]);

        // 12. Seed StockRequests
        // $stockRequest1 = StockRequest::create([
        //     'gerai_id' => $geraiBekasi->id,
        //     'warehouse_seal_id' => $warehouseSealBeat->id,
        //     'qty_requested' => 10,
        //     'status' => 'PENDING',
        //     'approved_at' => null,
        // ]);
        // $stockRequest2 = StockRequest::create([
        //     'gerai_id' => $geraiJakarta->id,
        //     'warehouse_seal_id' => $warehouseSealVario->id,
        //     'qty_requested' => 5,
        //     'status' => 'APPROVED',
        //     'approved_at' => now(),
        // ]);
    }
}
