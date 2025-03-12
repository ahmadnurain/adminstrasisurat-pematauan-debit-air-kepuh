<?php

namespace App\Livewire\Admin\Letter;

use App\Models\Family;
use App\Models\Request;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use App\Models\MovingComesInOneVillage;

class MovingComesOneVillage extends Component
{
    #[Layout('layouts.admin')]
    #[Title('Surat Pindah Datang Dalam satu Desa | Desa Kepuh')]

    public $letters;
    public $isModalOpen = false;
    public $modalImage;
    public $familyMemberFields = [
        ['name' => 'nik', 'label' => 'NIK', 'type' => 'number', 'placeholder' => 'Masukkan NIK'],
        ['name' => 'name', 'label' => 'Nama', 'type' => 'text', 'placeholder' => 'Masukkan Nama'],
        ['name' => 'shdk', 'label' => 'Status Hubungan Keluarga', 'type' => 'text', 'placeholder' => 'Masukkan Status'],
    ];  // Define the family member fields
    public $familyMembers = [];  // Store family members
    public function render()
    {
        // Fetch only the requests where status_id is 4 (assuming 'status_id' is the column for status)
        $requests = Request::whereHas('typeLetter', function ($query) {
            $query->where('name', 'Surat Pindah Datang Dalam satu Desa');
        })
            ->where('request_status_id', 5)  // Filter by status_id being 5
            ->get();

        return view('livewire.admin.letter.moving-comes-one-village', [
            'requests' => $requests,
        ]);
    }

    // Method to add a family member
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
        $MovingComesInOneVillageLetter = MovingComesInOneVillage::findOrFail($id);

        // Ambil data keluarga berdasarkan request_id
        $familyMembers = Family::where('request_id', $MovingComesInOneVillageLetter->request_id)->get();

        // Generate HTML content
        $htmlContent = view('pdf.MovingComesInOneVillage_letter', compact('MovingComesInOneVillageLetter', 'familyMembers'))->render();

        // Generate PDF using DomPDF
        $pdf = Pdf::loadHTML($htmlContent)
            ->setPaper('a4') // Set the paper size (optional)
            ->setOption('isHtml5ParserEnabled', true) // Optional settings
            ->setOption('isRemoteEnabled', true);     // Allow loading external CSS/JS (optional)

        $namaSurat = Str::slug($MovingComesInOneVillageLetter->applicant_full_name);

        // Return the PDF as a download
        $pdfFileName = "Surat Pindah Datang Dalam Satu Desa | {$namaSurat}.pdf";
        return $pdf->download($pdfFileName);
    }

    public function streamPDF($id)
    {
        // Retrieve data for the document
        $MovingComesInOneVillageLetter = MovingComesInOneVillage::findOrFail($id);

        // Ambil data keluarga berdasarkan request_id
        $familyMembers = Family::where('request_id', $MovingComesInOneVillageLetter->request_id)->get();
        // Generate PDF using DomPDF
        // Kirim data surat dan keluarga ke view PDF
        $pdf = Pdf::loadView('pdf.MovingComesInOneVillage_letter', [
            'MovingComesInOneVillageLetter' => $MovingComesInOneVillageLetter,
            'familyMembers' => $familyMembers // Tambahkan variabel ini
        ])
            ->setPaper('a4') // Set the paper size (optional)
            ->setOption('isHtml5ParserEnabled', true) // Optional settings
            ->setOption('isRemoteEnabled', true);     // Allow loading external CSS/JS (optional)

        $namaSurat = Str::slug($MovingComesInOneVillageLetter->applicant_full_name);

        // Save the PDF to a specific directory
        $pdfFileName = "Surat-Pindah-Datang-Dalam-Satu-Desa-{$namaSurat}.pdf";
        $filePath = storage_path('app/public/pdf/' . $pdfFileName);
        $pdf->save($filePath);


        // Stream the PDF to the browser
        return $pdf->stream();
    }
}
