<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Request as RequestModel;
use App\Models\DomicileLetter;
use App\Models\PovertyLetter;
use App\Models\BusinessLetter;
use App\Models\BirthLetter;
use App\Models\VillageLetter;
use App\Models\IncomeLetter;
use App\Models\SingleStatusLetter;
use App\Models\MaritalStatusLetter;
use App\Models\DifferentNameLetter;
use App\Models\MovingBetweenVillages;
use App\Models\MovingComesInOneVillage;
use App\Models\Family;
use Illuminate\Database\Seeder;

class DummyRequestSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::where('email', 'user@gmail.com')->first();
        if (!$user1) {
            $user1 = User::create([
                'name' => 'Ahmad Wildan',
                'email' => 'user@gmail.com',
                'password' => bcrypt('user1234'),
            ]);
            $user1->assignRole('user');
        }

        $citizens = [
            User::firstOrCreate(['email' => 'siti.aminah@gmail.com'], [
                'name' => 'Siti Aminah',
                'password' => bcrypt('password123'),
            ]),
            User::firstOrCreate(['email' => 'budi.santoso@gmail.com'], [
                'name' => 'Budi Santoso',
                'password' => bcrypt('password123'),
            ]),
            User::firstOrCreate(['email' => 'dewi.lestari@gmail.com'], [
                'name' => 'Dewi Lestari',
                'password' => bcrypt('password123'),
            ]),
            User::firstOrCreate(['email' => 'eko.prasetyo@gmail.com'], [
                'name' => 'Eko Prasetyo',
                'password' => bcrypt('password123'),
            ]),
        ];
        foreach ($citizens as $c) {
            $c->assignRole('user');
        }

        // 1. Surat Domisili (Type ID 3) - Selesai (5)
        $req1 = RequestModel::create([
            'user_id' => $user1->id,
            'type_letter_id' => 3,
            'image_ktp' => 'ktp/sample_ktp.png',
            'image_selfie' => 'selfie/sample_selfie.png',
            'request_status_id' => 5,
        ]);
        DomicileLetter::create([
            'request_id' => $req1->id,
            'name' => 'Ahmad Wildan',
            'nik' => '3210123456780001',
            'gender' => 'Laki-Laki',
            'birth_place' => 'Majalengka',
            'birth_date' => '1998-05-14',
            'nationality' => 'Indonesia',
            'religion' => 'Islam',
            'occupation' => 'Wiraswasta',
            'address' => 'Blok Babakan Sukamanah RT 02 RW 01 Desa Kepuh',
            'number_letter' => '470/012/Pem/2026',
        ]);

        // 2. Surat Keterangan Tidak Mampu (Type ID 1) - Diproses (4)
        $req2 = RequestModel::create([
            'user_id' => $citizens[0]->id,
            'type_letter_id' => 1,
            'image_ktp' => 'ktp/sample_ktp.png',
            'image_selfie' => 'selfie/sample_selfie.png',
            'request_status_id' => 4,
        ]);
        PovertyLetter::create([
            'request_id' => $req2->id,
            'name' => 'Siti Aminah',
            'nik' => '3210123456780002',
            'gender' => 'Perempuan',
            'birth_place' => 'Majalengka',
            'birth_date' => '2001-08-20',
            'nationality' => 'Indonesia',
            'religion' => 'Islam',
            'occupation' => 'Pelajar/Mahasiswa',
            'purpose' => 'Persyaratan Pengajuan Beasiswa Pendidikan',
            'address' => 'Blok Cilendi RT 01 RW 02 Desa Kepuh',
            'number_letter' => '400/034/Kesra/2026',
        ]);

        // 3. Surat Keterangan Usaha (Type ID 4) - Disetujui (2)
        $req3 = RequestModel::create([
            'user_id' => $citizens[1]->id,
            'type_letter_id' => 4,
            'image_ktp' => 'ktp/sample_ktp.png',
            'image_selfie' => 'selfie/sample_selfie.png',
            'request_status_id' => 2,
        ]);
        BusinessLetter::create([
            'request_id' => $req3->id,
            'name' => 'Budi Santoso',
            'nik' => '3210123456780003',
            'birth_place' => 'Majalengka',
            'birth_date' => '1992-11-10',
            'gender' => 'Laki-Laki',
            'religion' => 'Islam',
            'occupation' => 'Pedagang',
            'address' => 'Blok Mitra Tani RT 03 RW 01 Desa Kepuh',
            'marital_status' => 'Menikah',
            'business_name' => 'Toko Kelontong Berkah Kepuh',
            'business_type' => 'Perdagangan Sembako',
            'business_address' => 'Jl. Raya Desa Kepuh No. 45',
            'number_letter' => '510/056/Ekbang/2026',
        ]);

        // 4. Akta Kelahiran (Type ID 5) - Selesai (5)
        $req4 = RequestModel::create([
            'user_id' => $citizens[2]->id,
            'type_letter_id' => 5,
            'image_ktp' => 'ktp/sample_ktp.png',
            'image_selfie' => 'selfie/sample_selfie.png',
            'request_status_id' => 5,
        ]);
        BirthLetter::create([
            'request_id' => $req4->id,
            'family_head_name' => 'Eko Prasetyo',
            'family_card_number' => '3210120101920001',
            'baby_name' => 'Muhammad Rayyan Al-Farizi',
            'baby_gender' => 'Laki-Laki',
            'birth_date' => '2026-02-15',
            'birth_time' => '08:30:00',
            'birth_order' => 1,
            'birth_helper' => 'Bidan Desa Hj. Maryam',
            'baby_weight' => 3.2,
            'baby_length' => 49.5,
            'mother_nik' => '3210123456780004',
            'mother_name' => 'Dewi Lestari',
            'mother_birth_date' => '1995-03-22',
            'mother_age' => 31,
            'mother_occupation' => 'Ibu Rumah Tangga',
            'mother_address' => 'Blok Garunggang RT 02 RW 02 Desa Kepuh',
            'mother_nationality' => 'Indonesia',
            'mother_ethnicity' => 'Sunda',
            'mother_marriage_date' => '2023-01-10',
            'father_nik' => '3210123456780005',
            'father_name' => 'Eko Prasetyo',
            'father_birth_date' => '1993-07-18',
            'father_age' => 33,
            'father_occupation' => 'Karyawan Swasta',
            'father_address' => 'Blok Garunggang RT 02 RW 02 Desa Kepuh',
            'father_nationality' => 'Indonesia',
            'father_ethnicity' => 'Sunda',
            'father_marriage_date' => '2023-01-10',
            'reporter_nik' => '3210123456780005',
            'reporter_name' => 'Eko Prasetyo',
            'reporter_age' => 33,
            'reporter_gender' => 'Laki-Laki',
            'reporter_occupation' => 'Karyawan Swasta',
            'reporter_address' => 'Blok Garunggang RT 02 RW 02 Desa Kepuh',
            'witness1_nik' => '3210123456780009',
            'witness1_name' => 'H. Jarkasih',
            'witness1_age' => 58,
            'witness1_occupation' => 'Tokoh Masyarakat',
            'witness1_address' => 'Blok Garunggang RT 01 RW 02 Desa Kepuh',
            'number_letter' => '474.1/021/Pem/2026',
        ]);

        // 5. Surat Tanah / Desa (Type ID 6) - Selesai (5)
        $req5 = RequestModel::create([
            'user_id' => $user1->id,
            'type_letter_id' => 6,
            'image_ktp' => 'ktp/sample_ktp.png',
            'image_selfie' => 'selfie/sample_selfie.png',
            'request_status_id' => 5,
        ]);
        VillageLetter::create([
            'request_id' => $req5->id,
            'sppt_number' => '32.10.050.004.012-0045.0',
            'persil_number' => '12 D.II',
            'kohir_number' => '1423',
            'class' => 'D.II',
            'land_area' => 750,
            'land_owner' => 'Ahmad Wildan',
            'north_border' => 'Tanah Kas Desa',
            'east_border' => 'Saluran Irigasi Tersier',
            'south_border' => 'Jalan Desa Kepuh',
            'west_border' => 'Tanah Milik Sdr. Sugiman',
            'letter_c_number' => 'C-892',
            'land_assessment_price' => 150000000,
            'building_assessment_price' => 75000000,
            'total_assessment_price' => 225000000,
            'number_letter' => '590/078/Pem/2026',
        ]);

        // 6. Surat Pendapatan (Type ID 7) - Selesai (5)
        $req6 = RequestModel::create([
            'user_id' => $citizens[0]->id,
            'type_letter_id' => 7,
            'image_ktp' => 'ktp/sample_ktp.png',
            'image_selfie' => 'selfie/sample_selfie.png',
            'request_status_id' => 5,
        ]);
        IncomeLetter::create([
            'request_id' => $req6->id,
            'name' => 'Siti Aminah',
            'nik' => '3210123456780002',
            'birth_place' => 'Majalengka',
            'birth_date' => '2001-08-20',
            'occupation' => 'Wirausaha Kuliner',
            'address' => 'Blok Cilendi RT 01 RW 02 Desa Kepuh',
            'average_income' => 3500000,
            'parent_name' => 'Mamat Rachmat',
            'parent_nik' => '3210123456780077',
            'parent_gender' => 'Laki-Laki',
            'parent_birth_place' => 'Majalengka',
            'parent_nationality' => 'Indonesia',
            'parent_religion' => 'Islam',
            'parent_address' => 'Blok Cilendi RT 01 RW 02 Desa Kepuh',
            'number_letter' => '580/019/Ekbang/2026',
        ]);

        // 7. Surat Keterangan Belum Menikah (Type ID 8) - Belum Disetujui (1)
        $req7 = RequestModel::create([
            'user_id' => $citizens[0]->id,
            'type_letter_id' => 8,
            'image_ktp' => 'ktp/sample_ktp.png',
            'image_selfie' => 'selfie/sample_selfie.png',
            'request_status_id' => 1,
        ]);
        SingleStatusLetter::create([
            'request_id' => $req7->id,
            'name' => 'Siti Aminah',
            'nik' => '3210123456780002',
            'gender' => 'Perempuan',
            'birth_place' => 'Majalengka',
            'birth_date' => '2001-08-20',
            'religion' => 'Islam',
            'occupation' => 'Karyawan Swasta',
            'address' => 'Blok Cilendi RT 01 RW 02 Desa Kepuh',
            'number_letter' => '472/005/Kesra/2026',
        ]);

        // 8. Surat Keterangan Pindah Antar Desa (Type ID 12) - Diproses (4)
        $req8 = RequestModel::create([
            'user_id' => $citizens[3]->id,
            'type_letter_id' => 12,
            'image_ktp' => 'ktp/sample_ktp.png',
            'image_selfie' => 'selfie/sample_selfie.png',
            'request_status_id' => 4,
        ]);
        MovingBetweenVillages::create([
            'request_id' => $req8->id,
            'origin_family_card_number' => '3210129900880001',
            'origin_head_of_family_name' => 'Eko Prasetyo',
            'origin_address' => 'Blok Cisampih RT 04 RW 02',
            'origin_rt' => 4,
            'origin_rw' => 2,
            'origin_hamlet' => 'Cisampih',
            'origin_village' => 'Kepuh',
            'origin_district' => 'Lemahsugih',
            'origin_regency' => 'Majalengka',
            'origin_province' => 'Jawa Barat',
            'origin_postal_code' => '45465',
            'origin_phone' => '081234567890',
            'applicant_nik' => '3210123456780005',
            'applicant_full_name' => 'Eko Prasetyo',
            'moving_reason' => 'Pekerjaan',
            'destination_address' => 'Jl. Pahlawan No. 12 RT 01 RW 03',
            'destination_rt' => 1,
            'destination_rw' => 3,
            'destination_hamlet' => 'Mekarsari',
            'destination_village' => 'Sukasari',
            'destination_district' => 'Bantarujeg',
            'destination_regency' => 'Majalengka',
            'destination_province' => 'Jawa Barat',
            'destination_postal_code' => '45464',
            'destination_phone' => '081234567890',
            'moving_type' => 'Kepala Keluarga dan Seluruh Anggota',
            'kk_status_not_moving' => 'Tidak Ada',
            'kk_status_moving' => 'Membuat KK Baru',
            'number_letter' => '475/028/Pem/2026',
        ]);
        Family::create([
            'request_id' => $req8->id,
            'nik' => '3210123456780004',
            'name' => 'Dewi Lestari',
            'ktp_expiry' => '2030-12-31',
            'shdk' => 'Istri',
        ]);

        // 9. Surat Keterangan Beda Nama (Type ID 10) - Selesai (5)
        $req9 = RequestModel::create([
            'user_id' => $citizens[1]->id,
            'type_letter_id' => 10,
            'image_ktp' => 'ktp/sample_ktp.png',
            'image_selfie' => 'selfie/sample_selfie.png',
            'request_status_id' => 5,
        ]);
        DifferentNameLetter::create([
            'request_id' => $req9->id,
            'name' => 'Budi Santoso',
            'same_name' => 'Boedi Santoso',
            'nik' => '3210123456780003',
            'gender' => 'Laki-Laki',
            'birth_place' => 'Majalengka',
            'birth_date' => '1992-11-10',
            'nationality' => 'Indonesia',
            'religion' => 'Islam',
            'occupation' => 'Wiraswasta',
            'address' => 'Blok Mitra Tani RT 03 RW 01 Desa Kepuh',
            'number_letter' => '470/067/Pem/2026',
        ]);

        // 10. Surat Keterangan Janda/Duda (Type ID 9) - Belum Disetujui (1)
        $req10 = RequestModel::create([
            'user_id' => $user1->id,
            'type_letter_id' => 9,
            'image_ktp' => 'ktp/sample_ktp.png',
            'image_selfie' => 'selfie/sample_selfie.png',
            'request_status_id' => 1,
        ]);
        MaritalStatusLetter::create([
            'request_id' => $req10->id,
            'name' => 'Ahmad Wildan',
            'birth_place' => 'Majalengka',
            'birth_date' => '1998-05-14',
            'religion' => 'Islam',
            'occupation' => 'Wiraswasta',
            'gender' => 'Laki-Laki',
            'address' => 'Blok Babakan Sukamanah RT 02 RW 01 Desa Kepuh',
            'marital_status' => 'Duda',
            'status_reason' => 'Perceraian',
            'number_letter' => '474.2/011/Kesra/2026',
        ]);

        echo "Dummy requests and details successfully created!\n";
    }
}
