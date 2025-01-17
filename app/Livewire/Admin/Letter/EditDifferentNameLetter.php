<?php

namespace App\Livewire\Admin\Letter;

use Livewire\Component;
use App\Models\DifferentNameLetter;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

class EditDifferentNameLetter extends Component
{
    public $letterId; // ID data yang akan diedit
    public $request_id;
    public $name;
    public $same_name;
    public $nik;
    public $gender;
    public $birth_place;
    public $birth_date;
    public $nationality;
    public $religion;
    public $address;
    public $occupation;
    public $number_letter;

    #[Layout('layouts.admin')]
    #[Title('Edit Data Surat Keterangan Beda Nama | Desa Kepuh')]
    public function mount($id)
    {
        $letter = DifferentNameLetter::findOrFail($id); // Ambil data berdasarkan ID
        $this->letterId = $letter->id;
        $this->request_id = $letter->request_id;
        $this->name = $letter->name;
        $this->same_name = $letter->same_name;
        $this->nik = $letter->nik;
        $this->gender = $letter->gender;
        $this->birth_place = $letter->birth_place;
        $this->birth_date = $letter->birth_date;
        $this->nationality = $letter->nationality;
        $this->religion = $letter->religion;
        $this->address = $letter->address;
        $this->occupation = $letter->occupation;
        $this->number_letter = $letter->number_letter;
    }

    public function update()
    {
        $this->validate([
            'request_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'same_name' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'gender' => 'required|string',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'nationality' => 'required|string|max:255',
            'religion' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            'number_letter' => 'required|string|max:255',
        ]);

        $letter = DifferentNameLetter::findOrFail($this->letterId);
        $letter->update([
            'request_id' => $this->request_id,
            'name' => $this->name,
            'same_name' => $this->same_name,
            'nik' => $this->nik,
            'gender' => $this->gender,
            'birth_place' => $this->birth_place,
            'birth_date' => $this->birth_date,
            'nationality' => $this->nationality,
            'religion' => $this->religion,
            'address' => $this->address,
            'occupation' => $this->occupation,
            'number_letter' => $this->number_letter,
        ]);

        session()->flash('success', 'Data surat keterangan beda nama berhasil diperbarui!');
        return redirect()->route('admin.different-name-letter'); // Ganti sesuai dengan route index Anda
    }

    public function render()
    {
        return view('livewire.admin.letter.edit-different-name-letter');
    }
}
