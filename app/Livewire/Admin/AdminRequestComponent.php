<?php

namespace App\Livewire\Admin;

use App\Models\DifferentNameLetter;
use App\Models\Family;
use App\Models\MaritalStatusLetter;
use App\Models\Request;
use Livewire\Component;
use App\Models\TypeLetter;
use App\Models\BirthLetter;
use App\Models\IncomeLetter;
use App\Models\PovertyLetter;
use App\Models\VillageLetter;
use Livewire\WithFileUploads;
use App\Models\BusinessLetter;
use App\Models\DomicileLetter;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use WireUi\Traits\WireUiActions;
use App\Models\SingleStatusLetter;
use Illuminate\Support\Facades\Auth;
use App\Models\MovingComesInOneVillage;

class AdminRequestComponent extends Component
{
    use WireUiActions, WithFileUploads;

    public $type_letter_id;
    public $image_ktp;
    public $image_selfie;
    public $formFields = [];
    public $formFieldsValues = [
        'assessment_land_assessment_price' => 0,
        'assessment_building_assessment_price' => 0,
        'assessment_total_assessment_price' => 0,
    ];
    public $isFormVisible = false;
    public $activeTab = 1; // Default tab
    public $familyMembers = []; // New property to store family members
    public $familyMemberFields = [
        ['name' => 'nik', 'label' => 'NIK', 'type' => 'text'],
        ['name' => 'name', 'label' => 'Nama', 'type' => 'text'],
        ['name' => 'ktp_expiry', 'label' => 'Tanggal Kadaluarsa KTP', 'type' => 'date'],
        ['name' => 'shdk', 'label' => 'Status Hubungan Keluarga', 'type' => 'select'],
    ];
    // Daftar Field untuk Detail Taksiran
    public $assessmentFields = [
        [
            'name' => 'land_assessment_price',
            'type' => 'number',
            'label' => 'Harga Taksiran Tanah (Rp)',
            'class' => 'price_assessment',
            'placeholder' => 'Masukkan harga taksiran tanah',
        ],
        [
            'name' => 'building_assessment_price',
            'type' => 'number',
            'label' => 'Harga Taksiran Bangunan (Rp)',
            'class' => 'price_assessment',
            'placeholder' => 'Masukkan harga taksiran bangunan',
        ],
        [
            'name' => 'total_assessment_price',
            'type' => 'number',
            'label' => 'Jumlah Taksiran (Rp)',
            'placeholder' => 'Jumlah Taksiran',
            'class' => 'price_assessment',
            'readonly' => true,
        ],
    ];

    #[Layout('layouts.admin')]
    #[Title('Ajukan Permintaan Surat')]

    public function mount()
    {
        $this->isFormVisible = false;
        $this->formFields = []; // Awalnya kosong
        $this->familyMemberFields = [
            ['name' => 'nik', 'type' => 'number', 'label' => 'NIK', 'placeholder' => 'Masukkan NIK'],
            ['name' => 'name', 'type' => 'text', 'label' => 'Nama', 'placeholder' => 'Masukkan nama'],
            ['name' => 'ktp_expiry', 'type' => 'date', 'label' => 'Tanggal Kadaluarsa KTP', 'placeholder' => ''],
            ['name' => 'shdk', 'type' => 'text', 'label' => 'Status Hubungan Keluarga', 'placeholder' => 'Masukkan status hubungan keluarga'],
        ];
        $this->familyMembers = []; // Initialize family members array
    }


    public function addFamilyMember()
    {
        // Prepare family member data
        $familyMemberData = [];
        foreach ($this->familyMemberFields as $field) {
            // Ensure we're capturing the correct field values
            $familyMemberData[$field['name']] = $this->formFieldsValues['family_' . $field['name']] ?? null;
        }

        // Validation rules
        $rules = [
            'formFieldsValues.family_nik' => 'required|max:16',
            'formFieldsValues.family_name' => 'required|string|max:255',
            'formFieldsValues.family_ktp_expiry' => 'nullable|date',
            'formFieldsValues.family_shdk' => 'required|string|max:255',
        ];

        // Validation messages
        $messages = [
            'formFieldsValues.family_nik.required' => 'NIK harus diisi',
            'formFieldsValues.family_nik.digits' => 'NIK harus terdiri dari 16 digit',
            'formFieldsValues.family_name.required' => 'Nama harus diisi',
            'formFieldsValues.family_shdk.required' => 'Status Hubungan Keluarga harus diisi',
        ];

        // Validate the data
        $validatedData = $this->validate($rules, $messages);

        // Add the validated family member data to the familyMembers array
        $this->familyMembers[] = [
            'nik' => $validatedData['formFieldsValues']['family_nik'],
            'name' => $validatedData['formFieldsValues']['family_name'],
            'ktp_expiry' => $validatedData['formFieldsValues']['family_ktp_expiry'],
            'shdk' => $validatedData['formFieldsValues']['family_shdk'],
        ];

        // Clear form fields after adding the family member
        foreach ($this->familyMemberFields as $field) {
            unset($this->formFieldsValues['family_' . $field['name']]);
        }
    }




    // Method to remove a family member
    public function removeFamilyMember($index)
    {
        unset($this->familyMembers[$index]);
        $this->familyMembers = array_values($this->familyMembers);
    }


    public function updatedTypeLetterId()
    {
        // Memuat form fields sesuai dengan pilihan jenis surat
        $this->loadFormFields();
        // Menampilkan form tambahan jika type_letter_id diisi
        $this->isFormVisible = !empty($this->type_letter_id);
    }


    public function getSelectedTypeNameProperty()
    {
        $selectedType = $this->typeLetters->firstWhere('id', $this->type_letter_id);
        return $selectedType ? $selectedType['name'] : null;
    }




    public function updated($propertyName)
    {
        // Kalkulasi total jika salah satu field diubah
        if (
            $propertyName === 'formFieldsValues.assessment_land_assessment_price' ||
            $propertyName === 'formFieldsValues.assessment_building_assessment_price'
        ) {
            $this->calculateTotal();
        }
    }
    public function calculateTotal()
    {
        // Ambil nilai dari kedua inputan, hapus format ribuan, dan ubah ke float
        $landPrice = (float) str_replace(['.', ','], ['', '.'], $this->formFieldsValues['assessment_land_assessment_price']);
        $buildingPrice = (float) str_replace(['.', ','], ['', '.'], $this->formFieldsValues['assessment_building_assessment_price']);

        // Kalkulasi total jika salah satu atau kedua inputan memiliki nilai
        $total = $landPrice + $buildingPrice;

        // Format total ke dalam format IDR (Rp) dengan titik sebagai pemisah ribuan
        $this->formFieldsValues['assessment_total_assessment_price'] = number_format($total, 0, ',', '.');
    }

    private function cleanCurrency($value)
    {
        return (float) str_replace(['.', ','], ['', '.'], $value); // Hapus titik dan ubah koma menjadi titik desimal
    }



    public function loadFormFields()
    {
        switch ($this->type_letter_id) {
            case 1: // poverty
                $this->formFields = [
                    ['name' => 'name', 'type' => 'text', 'label' => 'Nama', 'placeholder' => 'Masukkan nama'],
                    ['name' => 'nik', 'type' => 'number', 'label' => 'NIK', 'placeholder' => 'Masukkan NIK'],
                    ['name' => 'gender', 'type' => 'select', 'label' => 'Jenis Kelamin', 'options' => ['Laki-laki', 'Perempuan']],
                    ['name' => 'birth_place', 'type' => 'text', 'label' => 'Tempat Lahir', 'placeholder' => 'Masukkan tempat lahir'],
                    ['name' => 'birth_date', 'type' => 'date', 'label' => 'Tanggal Lahir', 'placeholder' => ''],
                    ['name' => 'nationality', 'type' => 'text', 'label' => 'Kewarganegaraan', 'placeholder' => 'Masukkan kewarganegaraan'],
                    ['name' => 'religion', 'type' => 'text', 'label' => 'Agama', 'placeholder' => 'Masukkan agama'],
                    ['name' => 'occupation', 'type' => 'text', 'label' => 'Pekerjaan', 'placeholder' => 'Masukkan pekerjaan'],
                    ['name' => 'purpose', 'type' => 'text', 'label' => 'Tujuan Di Buat Surat', 'placeholder' => 'Masukkan tujuan surat'],
                    ['name' => 'address', 'type' => 'textarea', 'label' => 'Alamat', 'placeholder' => 'Masukkan alamat lengkap'],
                ];
                break;
            case 2:
                $this->formFields = [
                    // Origin details
                    ['name' => 'origin_family_card_number', 'type' => 'number', 'label' => 'Nomor Kartu Keluarga Asal', 'placeholder' => 'Masukkan nomor kartu keluarga asal'],
                    ['name' => 'origin_head_of_family_name', 'type' => 'text', 'label' => 'Nama Kepala Keluarga Asal', 'placeholder' => 'Masukkan nama kepala keluarga asal'],
                    ['name' => 'origin_address', 'type' => 'textarea', 'label' => 'Alamat Asal', 'placeholder' => 'Masukkan alamat asal lengkap'],
                    ['name' => 'origin_rt', 'type' => 'number', 'label' => 'RT Asal', 'placeholder' => 'Masukkan RT asal'],
                    ['name' => 'origin_rw', 'type' => 'number', 'label' => 'RW Asal', 'placeholder' => 'Masukkan RW asal'],
                    ['name' => 'origin_hamlet', 'type' => 'text', 'label' => 'Dusun Asal', 'placeholder' => 'Masukkan dusun asal'],
                    ['name' => 'origin_village', 'type' => 'text', 'label' => 'Desa Asal', 'placeholder' => 'Masukkan desa asal'],
                    ['name' => 'origin_district', 'type' => 'text', 'label' => 'Kecamatan Asal', 'placeholder' => 'Masukkan kecamatan asal'],
                    ['name' => 'origin_regency', 'type' => 'text', 'label' => 'Kabupaten Asal', 'placeholder' => 'Masukkan kabupaten asal'],
                    ['name' => 'origin_province', 'type' => 'text', 'label' => 'Provinsi Asal', 'placeholder' => 'Masukkan provinsi asal'],
                    ['name' => 'origin_postal_code', 'type' => 'number', 'label' => 'Kode Pos Asal', 'placeholder' => 'Masukkan kode pos asal'],
                    ['name' => 'origin_phone', 'type' => 'number', 'label' => 'Telepon Asal', 'placeholder' => 'Masukkan nomor telepon asal'],

                    // Applicant details
                    ['name' => 'applicant_nik', 'type' => 'number', 'label' => 'NIK Pemohon', 'placeholder' => 'Masukkan NIK pemohon'],
                    ['name' => 'applicant_full_name', 'type' => 'text', 'label' => 'Nama Lengkap Pemohon', 'placeholder' => 'Masukkan nama lengkap pemohon'],

                    // Move reason


                    // Destination details
                    ['name' => 'destination_card_number_family', 'type' => 'number', 'label' => 'Nomor Kartu Keluarga Tujuan', 'placeholder' => 'Masukkan nomor kartu keluarga tujua'],
                    ['name' => 'destination_nik_head_of_family', 'type' => 'number', 'label' => 'Nik Kepala Keluarga Tujuan', 'placeholder' => 'Masukkan nik kepala keluarga tujuan'],
                    ['name' => 'destination_name_head_of_family', 'type' => 'text', 'label' => 'Nama Kepala Keluarga Tujuan', 'placeholder' => 'Masukkan nama kepala keluarga tujuan'],
                    ['name' => 'destination_arrival_date', 'type' => 'date', 'label' => 'Tanggal Kedatangan', 'placeholder' => 'Masukkan tanggal kedatangan'],
                    ['name' => 'destination_address', 'type' => 'textarea', 'label' => 'Alamat Tujuan', 'placeholder' => 'Masukkan alamat tujuan lengkap'],
                    ['name' => 'destination_rt', 'type' => 'number', 'label' => 'RT Tujuan', 'placeholder' => 'Masukkan RT tujuan'],
                    ['name' => 'destination_rw', 'type' => 'number', 'label' => 'RW Tujuan', 'placeholder' => 'Masukkan RW tujuan'],
                    ['name' => 'destination_hamlet', 'type' => 'text', 'label' => 'Dusun Tujuan', 'placeholder' => 'Masukkan dusun tujuan'],
                    ['name' => 'destination_village', 'type' => 'text', 'label' => 'Desa Tujuan', 'placeholder' => 'Masukkan desa tujuan'],
                    ['name' => 'destination_district', 'type' => 'text', 'label' => 'Kecamatan Tujuan', 'placeholder' => 'Masukkan kecamatan tujuan'],
                    ['name' => 'destination_regency', 'type' => 'text', 'label' => 'Kabupaten Tujuan', 'placeholder' => 'Masukkan kabupaten tujuan'],
                    ['name' => 'destination_province', 'type' => 'text', 'label' => 'Provinsi Tujuan', 'placeholder' => 'Masukkan provinsi tujuan'],
                    ['name' => 'destination_postal_code', 'type' => 'number', 'label' => 'Kode Pos Tujuan', 'placeholder' => 'Masukkan kode pos tujuan'],


                    // Move type and family card status
                    ['name' => 'kk_status_moving', 'type' => 'select', 'label' => 'Status KK Pindah', 'options' => ['Numpang KK', 'Membuat KK Baru', 'Nomor KK Tetap']],
                ];
                break;
            case 3: // domicille Letter
                $this->formFields =
                    [
                        ['name' => 'name', 'type' => 'text', 'label' => 'Nama', 'placeholder' => 'Masukkan nama'],
                        ['name' => 'nik', 'type' => 'number', 'label' => 'NIK', 'placeholder' => 'Masukkan NIK'],
                        ['name' => 'gender', 'type' => 'select', 'label' => 'Jenis Kelamin', 'options' => ['Laki-laki', 'Perempuan']],
                        ['name' => 'birth_place', 'type' => 'text', 'label' => 'Tempat Lahir', 'placeholder' => 'Masukkan tempat lahir'],
                        ['name' => 'birth_date', 'type' => 'date', 'label' => 'Tanggal Lahir', 'placeholder' => ''],
                        ['name' => 'nationality', 'type' => 'text', 'label' => 'Kewarganegaraan', 'placeholder' => 'Masukkan kewarganegaraan'],
                        ['name' => 'religion', 'type' => 'text', 'label' => 'Agama', 'placeholder' => 'Masukkan agama'],
                        ['name' => 'occupation', 'type' => 'text', 'label' => 'Pekerjaan', 'placeholder' => 'Masukkan pekerjaan'],
                        ['name' => 'address', 'type' => 'textarea', 'label' => 'Alamat', 'placeholder' => 'Masukkan alamat lengkap'],
                    ];
                break;
            case 4: // domicille Letter
                $this->formFields =
                    [
                        ['name' => 'nik', 'type' => 'number', 'label' => 'NIK', 'placeholder' => 'Masukkan NIK'],
                        ['name' => 'name', 'type' => 'text', 'label' => 'Nama', 'placeholder' => 'Masukkan nama'],
                        ['name' => 'birth_place', 'type' => 'text', 'label' => 'Tempat Lahir', 'placeholder' => 'Masukkan tempat lahir'],
                        ['name' => 'birth_date', 'type' => 'date', 'label' => 'Tanggal Lahir', 'placeholder' => ''],
                        ['name' => 'gender', 'type' => 'select', 'label' => 'Jenis Kelamin', 'options' => ['Laki-laki', 'Perempuan']],
                        ['name' => 'religion', 'type' => 'text', 'label' => 'Agama', 'placeholder' => 'Masukkan agama'],
                        ['name' => 'occupation', 'type' => 'text', 'label' => 'Pekerjaan', 'placeholder' => 'Masukkan pekerjaan'],
                        ['name' => 'address', 'type' => 'textarea', 'label' => 'Alamat', 'placeholder' => 'Masukkan alamat lengkap'],
                        ['name' => 'marital_status', 'type' => 'select', 'label' => 'Status Pernikahan', 'options' => ['Belum Menikah', 'Menikah', 'Cerai']],
                        ['name' => 'business_name', 'type' => 'text', 'label' => 'Nama Usaha', 'placeholder' => 'Masukkan nama usaha'],
                        ['name' => 'business_type', 'type' => 'text', 'label' => 'Jenis Usaha', 'placeholder' => 'Masukkan jenis usaha'],
                        ['name' => 'business_address', 'type' => 'textarea', 'label' => 'Alamat Usaha', 'placeholder' => 'Masukkan alamat usaha'],
                    ];
                break;
            case 5: // domicille Letter
                $this->formFields = [
                    ['name' => 'family_head_name', 'type' => 'text', 'label' => 'Nama Kepala Keluarga', 'placeholder' => 'Masukkan Nama Kepala Keluarga'],
                    ['name' => 'family_card_number', 'type' => 'number', 'label' => 'Nomor KK', 'placeholder' => 'Masukkan Nomor KK'],

                    // Data Bayi
                    ['name' => 'baby_name', 'type' => 'text', 'label' => 'Nama Bayi', 'placeholder' => 'Masukkan Nama Bayi'],
                    ['name' => 'baby_gender', 'type' => 'select', 'label' => 'Jenis Kelamin Bayi', 'options' => ['Laki-laki', 'Perempuan']],
                    ['name' => 'birth_date', 'type' => 'date', 'label' => 'Tanggal Lahir', 'placeholder' => ''],
                    ['name' => 'birth_time', 'type' => 'time', 'label' => 'Waktu Lahir', 'placeholder' => 'Masukkan Waktu Lahir'],
                    ['name' => 'birth_order', 'type' => 'number', 'label' => 'Anak Ke-', 'placeholder' => 'Masukkan Urutan Kelahiran'],
                    ['name' => 'birth_helper', 'type' => 'text', 'label' => 'Penolong Kelahiran', 'placeholder' => 'Masukkan Penolong Kelahiran'],
                    ['name' => 'baby_weight', 'type' => 'number', 'label' => 'Berat Bayi (kg)', 'placeholder' => 'Masukkan Berat Bayi'],
                    ['name' => 'baby_length', 'type' => 'number', 'label' => 'Panjang Bayi (cm)', 'placeholder' => 'Masukkan Panjang Bayi'],

                    // Data Ibu
                    ['name' => 'mother_nik', 'type' => 'number', 'label' => 'NIK Ibu', 'placeholder' => 'Masukkan NIK Ibu'],
                    ['name' => 'mother_name', 'type' => 'text', 'label' => 'Nama Ibu', 'placeholder' => 'Masukkan Nama Ibu'],
                    ['name' => 'mother_birth_date', 'type' => 'date', 'label' => 'Tanggal Lahir Ibu', 'placeholder' => ''],
                    ['name' => 'mother_age', 'type' => 'number', 'label' => 'Usia Ibu', 'placeholder' => 'Masukkan Usia Ibu'],
                    ['name' => 'mother_occupation', 'type' => 'text', 'label' => 'Pekerjaan Ibu', 'placeholder' => 'Masukkan Pekerjaan Ibu'],
                    ['name' => 'mother_address', 'type' => 'textarea', 'label' => 'Alamat Ibu', 'placeholder' => 'Masukkan Alamat Ibu'],
                    ['name' => 'mother_nationality', 'type' => 'text', 'label' => 'Kewarganegaraan Ibu', 'placeholder' => 'Masukkan Kewarganegaraan Ibu'],
                    ['name' => 'mother_ethnicity', 'type' => 'text', 'label' => 'Suku Ibu', 'placeholder' => 'Masukkan Suku Ibu'],
                    ['name' => 'mother_marriage_date', 'type' => 'date', 'label' => 'Tanggal Pernikahan Ibu', 'placeholder' => ''],

                    // Data Ayah
                    ['name' => 'father_nik', 'type' => 'number', 'label' => 'NIK Ayah', 'placeholder' => 'Masukkan NIK Ayah'],
                    ['name' => 'father_name', 'type' => 'text', 'label' => 'Nama Ayah', 'placeholder' => 'Masukkan Nama Ayah'],
                    ['name' => 'father_birth_date', 'type' => 'date', 'label' => 'Tanggal Lahir Ayah', 'placeholder' => ''],
                    ['name' => 'father_age', 'type' => 'number', 'label' => 'Usia Ayah', 'placeholder' => 'Masukkan Usia Ayah'],
                    ['name' => 'father_occupation', 'type' => 'text', 'label' => 'Pekerjaan Ayah', 'placeholder' => 'Masukkan Pekerjaan Ayah'],
                    ['name' => 'father_address', 'type' => 'textarea', 'label' => 'Alamat Ayah', 'placeholder' => 'Masukkan Alamat Ayah'],
                    ['name' => 'father_nationality', 'type' => 'text', 'label' => 'Kewarganegaraan Ayah', 'placeholder' => 'Masukkan Kewarganegaraan Ayah'],
                    ['name' => 'father_ethnicity', 'type' => 'text', 'label' => 'Suku Ayah', 'placeholder' => 'Masukkan Suku Ayah'],
                    ['name' => 'father_marriage_date', 'type' => 'date', 'label' => 'Tanggal Pernikahan Ayah', 'placeholder' => ''],

                    // Data Pelapor
                    ['name' => 'reporter_nik', 'type' => 'number', 'label' => 'NIK Pelapor', 'placeholder' => 'Masukkan NIK Pelapor'],
                    ['name' => 'reporter_name', 'type' => 'text', 'label' => 'Nama Pelapor', 'placeholder' => 'Masukkan Nama Pelapor'],
                    ['name' => 'reporter_age', 'type' => 'number', 'label' => 'Usia Pelapor', 'placeholder' => 'Masukkan Usia Pelapor'],
                    ['name' => 'reporter_gender', 'type' => 'select', 'label' => 'Jenis Kelamin Pelapor', 'options' => ['Laki-laki', 'Perempuan']],
                    ['name' => 'reporter_occupation', 'type' => 'text', 'label' => 'Pekerjaan Pelapor', 'placeholder' => 'Masukkan Pekerjaan Pelapor'],
                    ['name' => 'reporter_address', 'type' => 'textarea', 'label' => 'Alamat Pelapor', 'placeholder' => 'Masukkan Alamat Pelapor'],

                    // Data Saksi 1
                    ['name' => 'witness1_nik', 'type' => 'number', 'label' => 'NIK Saksi 1', 'placeholder' => 'Masukkan NIK Saksi 1'],
                    ['name' => 'witness1_name', 'type' => 'text', 'label' => 'Nama Saksi 1', 'placeholder' => 'Masukkan Nama Saksi 1'],
                    ['name' => 'witness1_age', 'type' => 'number', 'label' => 'Usia Saksi 1', 'placeholder' => 'Masukkan Usia Saksi 1'],
                    ['name' => 'witness1_occupation', 'type' => 'text', 'label' => 'Pekerjaan Saksi 1', 'placeholder' => 'Masukkan Pekerjaan Saksi 1'],
                    ['name' => 'witness1_address', 'type' => 'textarea', 'label' => 'Alamat Saksi 1', 'placeholder' => 'Masukkan Alamat Saksi 1'],

                    // Data Saksi 2
                    ['name' => 'witness2_nik', 'type' => 'number', 'label' => 'NIK Saksi 2', 'placeholder' => 'Masukkan NIK Saksi 2'],
                    ['name' => 'witness2_name', 'type' => 'text', 'label' => 'Nama Saksi 2', 'placeholder' => 'Masukkan Nama Saksi 2'],
                    ['name' => 'witness2_age', 'type' => 'number', 'label' => 'Usia Saksi 2', 'placeholder' => 'Masukkan Usia Saksi 2'],
                    ['name' => 'witness2_occupation', 'type' => 'text', 'label' => 'Pekerjaan Saksi 2', 'placeholder' => 'Masukkan Pekerjaan Saksi 2'],
                    ['name' => 'witness2_address', 'type' => 'textarea', 'label' => 'Alamat Saksi 2', 'placeholder' => 'Masukkan Alamat Saksi 2'],
                ];
                break;
            case 6: // domicille Letter
                $this->formFields = [
                    ['name' => 'sppt_number', 'type' => 'text', 'label' => 'Nomor SPPT', 'placeholder' => 'Masukkan nomor SPPT'],
                    ['name' => 'persil_number', 'type' => 'text', 'label' => 'Nomor Persil', 'placeholder' => 'Masukkan nomor persil'],
                    ['name' => 'kohir_number', 'type' => 'text', 'label' => 'Nomor Kohir', 'placeholder' => 'Masukkan nomor kohir'],
                    ['name' => 'class', 'type' => 'text', 'label' => 'Kelas', 'placeholder' => 'Masukkan kelas'],
                    ['name' => 'land_area', 'type' => 'number', 'label' => 'Luas Tanah (m²)', 'placeholder' => 'Masukkan luas tanah'],
                    ['name' => 'land_owner', 'type' => 'text', 'label' => 'Pemilik Tanah', 'placeholder' => 'Masukkan nama pemilik tanah'],
                    ['name' => 'north_border', 'type' => 'text', 'label' => 'Batas Utara', 'placeholder' => 'Masukkan batas utara'],
                    ['name' => 'east_border', 'type' => 'text', 'label' => 'Batas Timur', 'placeholder' => 'Masukkan batas timur'],
                    ['name' => 'south_border', 'type' => 'text', 'label' => 'Batas Selatan', 'placeholder' => 'Masukkan batas selatan'],
                    ['name' => 'west_border', 'type' => 'text', 'label' => 'Batas Barat', 'placeholder' => 'Masukkan batas barat'],

                    // Menambahkan field baru untuk Letter C dan harga taksiran
                    ['name' => 'letter_c_number', 'type' => 'text', 'label' => 'Letter C No', 'placeholder' => 'Masukkan nomor Letter C'],

                ];
                break;
            case 7: // domicille Letter
                $this->formFields = [
                    ['name' => 'name', 'type' => 'text', 'label' => 'Nama', 'placeholder' => 'Masukkan nama'],
                    ['name' => 'nik', 'type' => 'number', 'label' => 'NIK', 'placeholder' => 'Masukkan NIK'],
                    ['name' => 'birth_place', 'type' => 'text', 'label' => 'Tempat Lahir', 'placeholder' => 'Masukkan tempat lahir'],
                    ['name' => 'birth_date', 'type' => 'date', 'label' => 'Tanggal Lahir', 'placeholder' => 'Pilih tanggal lahir'],
                    ['name' => 'occupation', 'type' => 'text', 'label' => 'Pekerjaan', 'placeholder' => 'Masukkan pekerjaan'],
                    ['name' => 'address', 'type' => 'textarea', 'label' => 'Alamat', 'placeholder' => 'Masukkan alamat'],
                    ['name' => 'average_income', 'type' => 'text', 'label' => 'Rata-Rata Penghasilan', 'placeholder' => 'Masukkan rata-rata penghasilan', 'oninput' => 'formatCurrency(this)'],
                    ['name' => 'parent_name', 'type' => 'text', 'label' => 'Nama Orang Tua', 'placeholder' => 'Masukkan nama orang tua'],
                    ['name' => 'parent_nik', 'type' => 'number', 'label' => 'NIK Orang Tua', 'placeholder' => 'Masukkan NIK orang tua'],
                    ['name' => 'parent_gender', 'type' => 'select', 'label' => 'Jenis Kelamin Orang Tua', 'options' => ['L' => 'Laki-laki', 'P' => 'Perempuan'], 'placeholder' => 'Pilih jenis kelamin'],
                    ['name' => 'parent_birth_place', 'type' => 'text', 'label' => 'Tempat Lahir Orang Tua', 'placeholder' => 'Masukkan tempat lahir orang tua'],
                    ['name' => 'parent_nationality', 'type' => 'text', 'label' => 'Kewarganegaraan Orang Tua', 'placeholder' => 'Masukkan kewarganegaraan orang tua'],
                    ['name' => 'parent_religion', 'type' => 'text', 'label' => 'Agama Orang Tua', 'placeholder' => 'Masukkan agama orang tua'],
                    ['name' => 'parent_address', 'type' => 'textarea', 'label' => 'Alamat Orang Tua', 'placeholder' => 'Masukkan alamat orang tua'],
                ];
                break;
            case 8: // domicille Letter
                $this->formFields = [
                    ['name' => 'name', 'type' => 'text', 'label' => 'Nama', 'placeholder' => 'Masukkan nama'],
                    ['name' => 'nik', 'type' => 'number', 'label' => 'NIK', 'placeholder' => 'Masukkan NIK'],
                    ['name' => 'gender', 'type' => 'select', 'label' => 'Jenis Kelamin', 'options' => ['Laki-laki', 'Perempuan']],
                    ['name' => 'birth_place', 'type' => 'text', 'label' => 'Tempat Lahir', 'placeholder' => 'Masukkan tempat lahir'],
                    ['name' => 'birth_date', 'type' => 'date', 'label' => 'Tanggal Lahir', 'placeholder' => 'Pilih tanggal lahir'],
                    ['name' => 'religion', 'type' => 'text', 'label' => 'Agama', 'placeholder' => 'Masukkan agama'],
                    ['name' => 'occupation', 'type' => 'text', 'label' => 'Pekerjaan', 'placeholder' => 'Masukkan pekerjaan'],
                    ['name' => 'address', 'type' => 'textarea', 'label' => 'Alamat', 'placeholder' => 'Masukkan alamat'],
                ];

                break;
            case 9: // domicille Letter
                $this->formFields = [
                    ['name' => 'name', 'type' => 'text', 'label' => 'Nama', 'placeholder' => 'Masukkan nama'],
                    ['name' => 'birth_place', 'type' => 'text', 'label' => 'Tempat Lahir', 'placeholder' => 'Masukkan tempat lahir'],
                    ['name' => 'birth_date', 'type' => 'date', 'label' => 'Tanggal Lahir', 'placeholder' => 'Pilih tanggal lahir'],
                    ['name' => 'religion', 'type' => 'text', 'label' => 'Agama', 'placeholder' => 'Masukkan agama'],
                    ['name' => 'occupation', 'type' => 'text', 'label' => 'Pekerjaan', 'placeholder' => 'Masukkan pekerjaan'],
                    ['name' => 'gender', 'type' => 'select', 'label' => 'Jenis Kelamin', 'options' => ['Laki-laki', 'Perempuan']],
                    ['name' => 'address', 'type' => 'textarea', 'label' => 'Alamat', 'placeholder' => 'Masukkan alamat'],
                    ['name' => 'marital_status', 'type' => 'select', 'label' => 'Status Perkawinan', 'options' => ['Duda', 'Janda']],
                    ['name' => 'status_reason', 'type' => 'select', 'label' => 'Alasan Status', 'options' => ['Perceraian', 'Kematian']],
                ];


                break;
            case 10: // domicille Letter
                $this->formFields = [
                    ['name' => 'nik', 'type' => 'text', 'label' => 'NIK', 'placeholder' => 'Masukkan NIK'],
                    ['name' => 'name', 'type' => 'text', 'label' => 'Nama', 'placeholder' => 'Masukkan nama'],
                    ['name' => 'same_name', 'type' => 'text', 'label' => 'Nama Yang Sama', 'placeholder' => 'Masukkan nama sama'],
                    ['name' => 'gender', 'type' => 'select', 'label' => 'Jenis Kelamin', 'options' => ['Laki-laki', 'Perempuan']],
                    ['name' => 'birth_place', 'type' => 'text', 'label' => 'Tempat Lahir', 'placeholder' => 'Masukkan tempat lahir'],
                    ['name' => 'birth_date', 'type' => 'date', 'label' => 'Tanggal Lahir', 'placeholder' => 'Pilih tanggal lahir'],
                    ['name' => 'nationality', 'type' => 'text', 'label' => 'Kewarganegaraan', 'placeholder' => 'Masukkan kewarganegaraan'],
                    ['name' => 'religion', 'type' => 'text', 'label' => 'Agama', 'placeholder' => 'Masukkan agama'],
                    ['name' => 'occupation', 'type' => 'text', 'label' => 'Pekerjaan', 'placeholder' => 'Masukkan pekerjaan'],
                    ['name' => 'address', 'type' => 'textarea', 'label' => 'Alamat', 'placeholder' => 'Masukkan alamat'],
                ];


                break;

            default:
                $this->formFields = [];
                break;
        }
    }



    public function submit()
    {
        $validationRules = [
            'type_letter_id' => 'required|exists:type_letters,id',
            'image_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'image_selfie' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];

        foreach ($this->formFields as $field) {
            if ($field['name'] === 'nik') {
                $validationRules['formFieldsValues.' . $field['name']] = 'required|digits_between:1,16'; // Pastikan hanya angka dengan panjang tertentu
            } elseif ($field['type'] === 'date') {
                $validationRules['formFieldsValues.' . $field['name']] = 'nullable|date';
            } else {
                $validationRules['formFieldsValues.' . $field['name']] = 'required|string|max:255';
            }
        }

        $this->validate($validationRules);

        // Validasi keamanan file
        $this->validateImage($this->image_ktp);
        $this->validateImage($this->image_selfie);

        // Generate hashed filenames
        $ktpHashName = uniqid() . '.' . $this->image_ktp->getClientOriginalExtension();
        $selfieHashName = uniqid() . '.' . $this->image_selfie->getClientOriginalExtension();

        // Simpan file ke folder public storage dengan nama hash
        $ktpPath = $this->image_ktp->storeAs('ktp', $ktpHashName, 'public');
        $selfiePath = $this->image_selfie->storeAs('selfie', $selfieHashName, 'public');

        $request = Request::create([
            'user_id' => Auth::id(),
            'type_letter_id' => $this->type_letter_id,
            'image_ktp' => $ktpPath,
            'image_selfie' => $selfiePath,
            'request_status_id' => 1,
        ]);

        $data = array_merge($this->formFieldsValues, [
            'user_id' => Auth::id(),
            'request_id' => $request->id,
        ]);

        if ($this->type_letter_id == 1) {
            PovertyLetter::create($data);
        } elseif ($this->type_letter_id == 3) {
            DomicileLetter::create($data);
        } elseif ($this->type_letter_id == 2) {
            MovingComesInOneVillage::create($data);
            // Create family members associated with this request
            foreach ($this->familyMembers as $familyMember) {
                Family::create([
                    'request_id' => $request->id,
                    'nik' => $familyMember['nik'],
                    'name' => $familyMember['name'],
                    'ktp_expiry' => $familyMember['ktp_expiry'],
                    'shdk' => $familyMember['shdk'],
                ]);
            }
        } elseif ($this->type_letter_id == 4) {
            BusinessLetter::create($data);
        } elseif ($this->type_letter_id == 5) {
            BirthLetter::create($data);
        } elseif ($this->type_letter_id == 6) {
            // Bersihkan format IDR sebelum menyimpan ke database
            $data['land_assessment_price'] = $this->cleanCurrency($this->formFieldsValues['assessment_land_assessment_price'] ?? 0);
            $data['building_assessment_price'] = $this->cleanCurrency($this->formFieldsValues['assessment_building_assessment_price'] ?? 0);
            $data['total_assessment_price'] = $this->cleanCurrency($this->formFieldsValues['assessment_total_assessment_price'] ?? 0);

            // Simpan data ke tabel VillageLetter
            VillageLetter::create($data);
        } elseif ($this->type_letter_id == 7) {
            // Debugging: Periksa nilai average_income sebelum diproses



            // Remove thousands separator (dots) but leave the decimal point
            $averageIncome = isset($data['average_income']) && $data['average_income'] !== ''
                ? preg_replace('/\.(?=\d{3})/', '', $data['average_income']) // Removes only dots used as thousand separators
                : null;  // If empty, set to null (not 0)


            // Pastikan data average_income adalah angka desimal jika ada nilai
            $data['average_income'] = is_numeric($averageIncome) ? (float)$averageIncome : null;

            // Simpan data ke tabel income_letters
            IncomeLetter::create($data);
        } elseif ($this->type_letter_id == 8) {
            SingleStatusLetter::create($data);
        } elseif ($this->type_letter_id == 9) {
            MaritalStatusLetter::create($data);
        } elseif ($this->type_letter_id == 10) {
            DifferentNameLetter::create($data);
        }





        $this->reset();
        $this->dialog()->show([
            'icon' => 'success',
            'title' => 'Permintaan Surat Berhasil Diajukan',
        ]);
        $this->redirect(route('admin.request'));
    }

    // Fungsi untuk memvalidasi apakah file adalah gambar yang sah
    public function validateImage($file)
    {
        // Pastikan file benar-benar gambar
        $imageInfo = getimagesize($file->getRealPath());

        if ($imageInfo === false) {
            throw new \Exception('File yang diunggah bukan gambar valid.');
        }

        // Validasi MIME type
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileMimeType = $file->getMimeType();

        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            throw new \Exception('File yang diunggah tidak memiliki tipe MIME valid.');
        }
    }

    public function render()
    {
        return view('livewire.admin.admin-request-component', [
            'typeLetters' => TypeLetter::whereIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->get(),
        ]);
    }
}
