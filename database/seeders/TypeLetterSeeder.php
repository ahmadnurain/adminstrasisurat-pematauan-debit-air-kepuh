<?php

namespace Database\Seeders;

use App\Models\TypeLetter;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TypeLetterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typeLetter = [
            ['name' => 'Surat Keterangan Tidak Mampu'],
            ['name' => 'Surat Pindah Datang Dalam Satu Desa'],
            ['name' => 'Surat Domisili'],
            ['name' => 'Surat Keterangan Usaha'],
            ['name' => 'Akta Kelahiran'],
            ['name' => 'Surat Desa'],
            ['name' => 'Surat Pendapatan'],
            ['name' => 'Surat Keterangan Belum Menikah'],
            ['name' => 'Surat Keterangan Janda/Duda'],
            ['name' => 'Surat Keterangan Beda Nama'],
            ['name' => 'Surat Keterangan Kematian'],
            ['name' => 'Surat Keterangan Pindah Antar Desa'],
            ['name' => 'Surat Keterangan Pindah Antar Kecamatan'],
            ['name' => 'Surat Keterangan Pindah Antar Kabupaten'],

        ];

        TypeLetter::insert($typeLetter);
    }
}
