<?php

namespace App\Livewire\Admin\Letter;

use App\Models\Request;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;

class VillageLetter extends Component
{
    #[Layout('layouts.admin')]
    #[Title('Surat Desa | Desa Kepuh')]

    public $letters;
    public $isModalOpen = false;
    public $modalImage;
    public string $search = '';
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
            ->with(['villageLetter', 'typeLetter', 'user'])
            ->whereHas('typeLetter', fn($q) => $q->where('name', 'Surat Desa'))
            ->where('request_status_id', 5)
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereHas('villageLetter', function ($m) use ($term) {
                        $m->where('land_owner', 'like', $term)
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

        return view('livewire.admin.letter.village-letter', [
            'requests' => $paginator,
        ]);
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

    public function streamPDF($id)
    {
        $villageLetter = \App\Models\VillageLetter::findOrFail($id);
        $pdf = Pdf::loadView('pdf.village_letter', ['villageLetter' => $villageLetter])
            ->setPaper('a4')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
        $namaSurat = Str::slug($villageLetter->name);
        $pdfFileName = "Surat-Desa-{$namaSurat}.pdf";
        $filePath = storage_path("app/public/pdf/{$pdfFileName}");
        $pdf->save($filePath);
        return $pdf->stream();
    }

    public function downloadPDF($id)
    {
        $villageLetter = \App\Models\VillageLetter::findOrFail($id);
        $htmlContent = view('pdf.village_letter', compact('villageLetter'))->render();
        $pdf = Pdf::loadHTML($htmlContent)
            ->setPaper('a4')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
        $namaSurat = Str::slug($villageLetter->name);
        $pdfFileName = "Surat Desa | {$namaSurat}.pdf";
        return $pdf->download($pdfFileName);
    }
}