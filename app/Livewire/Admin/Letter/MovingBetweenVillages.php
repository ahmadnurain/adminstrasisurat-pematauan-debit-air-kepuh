<?php

namespace App\Livewire\Admin\Letter;

use App\Models\Family;
use App\Models\Request;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\DomicileLetter;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Spatie\Browsershot\Browsershot;

class MovingBetweenVillages extends Component
{
    #[Layout('layouts.admin')]
    #[Title('Surat Keterangan Pindah Antar Desa | Desa Kepuh')]

    public $letters;
    public $isModalOpen = false;
    public $modalImage;
    public $search = ''; // Tambahkan properti untuk pencarian
    public $familyMemberFields = [
        ['name' => 'nik', 'label' => 'NIK', 'type' => 'number', 'placeholder' => 'Masukkan NIK'],
        ['name' => 'name', 'label' => 'Nama', 'type' => 'text', 'placeholder' => 'Masukkan Nama'],
        ['name' => 'shdk', 'label' => 'Status Hubungan Keluarga', 'type' => 'text', 'placeholder' => 'Masukkan Status'],
    ];  // Define the family member fields
    public $familyMembers = [];  // Store family members

    public function render()
    {

        $requests = Request::whereHas('typeLetter', function ($query) {
            $query->where('name', 'Surat Keterangan Pindah Antar Desa');
        })
            ->where('request_status_id', 5)
            ->when($this->search, function ($query) {
                // Pencarian berdasarkan applicant_name
                $query->where('applicant_name', 'like', '%' . $this->search . '%');
            })
            ->get();

        return view('livewire.admin.letter.moving-between-villages', [
            'requests' => $requests,
        ]);
    }
    public function addFamilyMember()
    {
        $this->familyMembers[] = [
            'nik' => '',
            'name' => '',
            'shdk' => '',
        ];
    }

    // Method to remove a family member
    public function removeFamilyMember($index)
    {
        unset($this->familyMembers[$index]);
        $this->familyMembers = array_values($this->familyMembers);  // Reindex array
    }
    public function openModal($imageUrl)
    {
        $this->modalImage = $imageUrl; // Set image URL
        $this->isModalOpen = true;    // Open modal
    }
    public function closeModal()
    {
        $this->isModalOpen = false; // Close modal
    }
    public function downloadPDF($id)
    {
        //composer require barryvdh/laravel-dompdf
        // Retrieve data for the document
        $MovingBetweenVillagesLetter = \App\Models\MovingBetweenVillages::findOrFail($id);

        // Ambil data keluarga berdasarkan request_id
        $familyMembers = Family::where('request_id', $MovingBetweenVillagesLetter->request_id)->get();

        // Generate HTML content
        $htmlContent = view('pdf.MovingBetweenVillages_letter', compact('MovingBetweenVillagesLetter', 'familyMembers'))->render();

        // Generate PDF using DomPDF
        $pdf = Pdf::loadHTML($htmlContent)
            ->setPaper('a4') // Set the paper size (optional)
            ->setOption('isHtml5ParserEnabled', true) // Optional settings
            ->setOption('isRemoteEnabled', true);     // Allow loading external CSS/JS (optional)

        $namaSurat = Str::slug($MovingBetweenVillagesLetter->applicant_full_name);

        // Return the PDF as a download
        $pdfFileName = "Surat Keterangan Pindah Antar Desa | {$namaSurat}.pdf";
        return $pdf->download($pdfFileName);
    }

    public function streamPDF($id)
    {
        // Retrieve data for the document
        $MovingBetweenVillagesLetter = \App\Models\MovingBetweenVillages::findOrFail($id);

        // Ambil data keluarga berdasarkan request_id
        $familyMembers = Family::where('request_id', $MovingBetweenVillagesLetter->request_id)->get();
        // Generate PDF using DomPDF
        // Kirim data surat dan keluarga ke view PDF
        $pdf = Pdf::loadView('pdf.MovingBetweenVillages_letter', [
            'MovingBetweenVillagesLetter' => $MovingBetweenVillagesLetter,
            'familyMembers' => $familyMembers // Tambahkan variabel ini
        ])
            ->setPaper('a4') // Set the paper size (optional)
            ->setOption('isHtml5ParserEnabled', true) // Optional settings
            ->setOption('isRemoteEnabled', true);     // Allow loading external CSS/JS (optional)

        $namaSurat = Str::slug($MovingBetweenVillagesLetter->applicant_full_name);

        // Save the PDF to a specific directory
        $pdfFileName = "Surat-Keterangan-Pindah-Antar-Desa-{$namaSurat}.pdf";
        $filePath = storage_path('app/public/pdf/' . $pdfFileName);
        $pdf->save($filePath);


        // Stream the PDF to the browser
        return $pdf->stream();
    }
}
