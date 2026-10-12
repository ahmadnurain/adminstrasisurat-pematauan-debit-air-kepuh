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
    public string $search = '';
    public $familyMemberFields = [
        ['name' => 'nik', 'label' => 'NIK', 'type' => 'number', 'placeholder' => 'Masukkan NIK'],
        ['name' => 'name', 'label' => 'Nama', 'type' => 'text', 'placeholder' => 'Masukkan Nama'],
        ['name' => 'shdk', 'label' => 'Status Hubungan Keluarga', 'type' => 'text', 'placeholder' => 'Masukkan Status'],
    ];
    public $familyMembers = [];

    public int $perPage = 10;
    public ?string $cursor = null;
    public ?string $prevCursor = null;
    public ?string $nextCursor = null;

    public function updatingSearch()
    {
        $this->cursor = null;
    }

    public function goto(?string $cursor = null)
    {
        $this->cursor = $cursor ?: null;
    }

    public function render()
    {
        $base = Request::query()
            ->with(['MovingComesInOneVillageLetter', 'typeLetter', 'user'])
            ->whereHas('typeLetter', fn($q) => $q->where('name', 'Surat Pindah Datang Dalam satu Desa'))
            ->where('request_status_id', 5)
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereHas('MovingComesInOneVillageLetter', function ($m) use ($term) {
                        $m->where('applicant_full_name', 'like', $term)
                            ->orWhere('number_letter', 'like', $term);
                    })
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', $term))
                        ->orWhere('id', 'like', $term);
                });
            })
            ->orderBy('created_at', 'desc');

        $paginator = $base->cursorPaginate($this->perPage, ['*'], 'cursor', $this->cursor);
        $this->prevCursor = optional($paginator->previousCursor())->encode();
        $this->nextCursor = optional($paginator->nextCursor())->encode();

        return view('livewire.admin.letter.moving-comes-one-village', [
            'requests' => $paginator,
        ]);
    }

    public function addFamilyMember()
    {
        $this->familyMembers[] = ['nik' => '', 'name' => '', 'shdk' => ''];
    }

    public function removeFamilyMember($index)
    {
        unset($this->familyMembers[$index]);
        $this->familyMembers = array_values($this->familyMembers);
    }

    public function openModal($imageUrl)
    {
        $this->modalImage = $imageUrl;
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function downloadPDF($id)
    {
        $MovingComesInOneVillageLetter = MovingComesInOneVillage::findOrFail($id);
        $familyMembers = Family::where('request_id', $MovingComesInOneVillageLetter->request_id)->get();
        $htmlContent = view('pdf.MovingComesInOneVillage_letter', compact('MovingComesInOneVillageLetter', 'familyMembers'))->render();
        $pdf = Pdf::loadHTML($htmlContent)->setPaper('a4')->setOption('isHtml5ParserEnabled', true)->setOption('isRemoteEnabled', true);
        $namaSurat = Str::slug($MovingComesInOneVillageLetter->applicant_full_name);
        $pdfFileName = "Surat Pindah Datang Dalam Satu Desa | {$namaSurat}.pdf";
        return $pdf->download($pdfFileName);
    }

    public function streamPDF($id)
    {
        $MovingComesInOneVillageLetter = MovingComesInOneVillage::findOrFail($id);
        $familyMembers = Family::where('request_id', $MovingComesInOneVillageLetter->request_id)->get();
        $pdf = Pdf::loadView('pdf.MovingComesInOneVillage_letter', [
            'MovingComesInOneVillageLetter' => $MovingComesInOneVillageLetter,
            'familyMembers' => $familyMembers
        ])->setPaper('a4')->setOption('isHtml5ParserEnabled', true)->setOption('isRemoteEnabled', true);
        $namaSurat = Str::slug($MovingComesInOneVillageLetter->applicant_full_name);
        $pdfFileName = "Surat-Pindah-Datang-Dalam-Satu-Desa-{$namaSurat}.pdf";
        $filePath = storage_path('app/public/pdf/' . $pdfFileName);
        $pdf->save($filePath);
        return $pdf->stream();
    }
}