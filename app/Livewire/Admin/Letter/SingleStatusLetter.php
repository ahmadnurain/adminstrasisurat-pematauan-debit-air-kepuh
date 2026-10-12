<?php

namespace App\Livewire\Admin\Letter;

use App\Models\Request;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;

class SingleStatusLetter extends Component
{
    #[Layout('layouts.admin')]
    #[Title('Surat Keterangan Belum Menikah | Desa Kepuh')]

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
            ->with(['singleStatusLetter', 'typeLetter', 'user'])
            ->whereHas('typeLetter', fn($q) => $q->where('name', 'Surat Keterangan Belum Menikah'))
            ->where('request_status_id', 5)
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereHas('singleStatusLetter', function ($m) use ($term) {
                        $m->where('name', 'like', $term)
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

        return view('livewire.admin.letter.single-status-letter', [
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

    public function downloadPDF($id)
    {
        $singleStatusLetter = \App\Models\SingleStatusLetter::findOrFail($id);
        $htmlContent = view('pdf.single_status_letter', compact('singleStatusLetter'))->render();
        $pdf = Pdf::loadHTML($htmlContent)->setPaper('a4')->setOption('isHtml5ParserEnabled', true)->setOption('isRemoteEnabled', true);
        $namaSurat = Str::slug($singleStatusLetter->name);
        $pdfFileName = "Surat Keterangan Status | {$namaSurat}.pdf";
        return $pdf->download($pdfFileName);
    }

    public function streamPDF($id)
    {
        $singleStatusLetter = \App\Models\SingleStatusLetter::findOrFail($id);
        $pdf = Pdf::loadView('pdf.single_status_letter', ['singleStatusLetter' => $singleStatusLetter])->setPaper('a4')->setOption('isHtml5ParserEnabled', true)->setOption('isRemoteEnabled', true);
        $namaSurat = Str::slug($singleStatusLetter->name);
        $pdfFileName = "Surat-Keterangan-Status-{$namaSurat}.pdf";
        $filePath = storage_path("app/public/pdf/{$pdfFileName}");
        $pdf->save($filePath);
        return $pdf->stream();
    }
}