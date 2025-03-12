<?php

namespace App\Livewire\Admin\Letter;

use App\Models\Family;
use Livewire\Component;
use App\Models\MovingBetweenVillages;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

class EditMovingBetweenVillages extends Component
{
    #[Layout('layouts.admin')]
    #[Title('Edit Data Surat Pindah Satu Desa | Desa Kepuh')]

    public $movingRequestId;
    public $number_letter;
    public $origin_family_card_number, $origin_head_of_family_name, $origin_address, $origin_rt, $origin_rw, $origin_hamlet;
    public $origin_village, $origin_district, $origin_regency, $origin_province, $origin_postal_code, $origin_phone;
    public $applicant_nik, $applicant_full_name, $moving_reason;
    public $destination_address, $destination_rt, $destination_rw, $destination_hamlet, $destination_village;
    public $destination_district, $destination_regency, $destination_province, $destination_postal_code, $destination_phone;
    public $moving_type, $kk_status_not_moving, $kk_status_moving;
    public $familyMembers = [];

    public function mount($id)
    {
        $movingRequest = MovingBetweenVillages::findOrFail($id);
        $this->movingRequestId = $id;

        // Mengisi properti langsung tanpa `fill()`
        foreach ($movingRequest->toArray() as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        // Ambil anggota keluarga terkait
        $this->familyMembers = Family::where('request_id', $movingRequest->request_id)
            ->get()
            ->map(fn($member) => [
                'nik' => $member->nik,
                'name' => $member->name,
                'ktp_expiry' => $member->ktp_expiry,
                'shdk' => $member->shdk,
            ])
            ->toArray();
    }

    public function addFamilyMember()
    {
        $this->familyMembers[] = [
            'nik' => '',
            'name' => '',
            'ktp_expiry' => '',
            'shdk' => '',
        ];
    }

    public function removeFamilyMember($index)
    {
        if (isset($this->familyMembers[$index]['nik'])) {
            Family::where('request_id', $this->movingRequestId)
                ->where('nik', $this->familyMembers[$index]['nik'])
                ->delete();
        }
        unset($this->familyMembers[$index]);
        $this->familyMembers = array_values($this->familyMembers);
    }

    protected $rules = [
        'number_letter' => 'required|string',
        'origin_family_card_number' => 'required|string',
        'origin_head_of_family_name' => 'required|string',
        'origin_address' => 'required|string',
        'origin_rt' => 'required|numeric',
        'origin_rw' => 'required|numeric',
        'origin_hamlet' => 'required|string',
        'origin_village' => 'required|string',
        'origin_district' => 'required|string',
        'origin_regency' => 'required|string',
        'origin_province' => 'required|string',
        'origin_postal_code' => 'required|string',
        'origin_phone' => 'required|string',
        'applicant_nik' => 'required|string',
        'applicant_full_name' => 'required|string',
        'moving_reason' => 'required|string',
        'destination_address' => 'required|string',
        'destination_rt' => 'required|numeric',
        'destination_rw' => 'required|numeric',
        'destination_hamlet' => 'required|string',
        'destination_village' => 'required|string',
        'destination_district' => 'required|string',
        'destination_regency' => 'required|string',
        'destination_province' => 'required|string',
        'destination_postal_code' => 'required|string',
        'destination_phone' => 'required|string',
        'moving_type' => 'required|string',
        'kk_status_not_moving' => 'required|string',
        'kk_status_moving' => 'required|string',
        'familyMembers.*.nik' => 'required|string',
        'familyMembers.*.name' => 'required|string',
        'familyMembers.*.ktp_expiry' => 'required|date',
        'familyMembers.*.shdk' => 'required|string',
    ];

    public function update()
    {
        $validatedData = $this->validate();

        $movingRequest = MovingBetweenVillages::findOrFail($this->movingRequestId);
        $movingRequest->fill($validatedData);
        $movingRequest->save(); // Gunakan `save()` bukan `update($validatedData)`

        foreach ($this->familyMembers as $member) {
            Family::updateOrCreate(
                ['request_id' => $movingRequest->request_id, 'nik' => $member['nik']],
                [
                    'name' => $member['name'],
                    'ktp_expiry' => $member['ktp_expiry'],
                    'shdk' => $member['shdk'],
                ]
            );
        }

        session()->flash('message', 'Data berhasil diperbarui.');
        return redirect()->route('admin.moving-between-villages');
    }

    public function render()
    {
        return view('livewire.admin.letter.edit-moving-between-villages');
    }
}
