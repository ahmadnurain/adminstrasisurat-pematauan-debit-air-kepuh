<?php

namespace App\Livewire\Admin\Letter;

use App\Models\BirthLetter as ModelsBirthLetter;
use App\Models\Request;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;

class BirthLetter extends Component
{
    #[Layout('layouts.admin')]
    #[Title('Akta Kelahiran | Desa Kepuh')]

    public $letters;
    public $isModalOpen = false;
    public $modalImage;
    public $search = '';

    public int $perPage = 10;
    public ?string $cursor = null;
    public ?string $prevCursor = null;
    public ?string $nextCursor = null;

    public function updatingSearch()
    {
        $this->cursor = null;
    }

    public function goto(string $cursor = null)
    {
        $this->cursor = $cursor ?: null;
    }

    public function render()
    {
        $base = Request::query()
            ->with(['birthLetter', 'typeLetter', 'user'])
            ->whereHas('typeLetter', fn($q) => $q->where('name', 'Akta Kelahiran'))
            ->where('request_status_id', 5)
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereHas('birthLetter', function ($b) use ($term) {
                        $b->where('reporter_name', 'like', $term)
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

        return view('livewire.admin.letter.birth-letter', [
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
        $birthLetter = ModelsBirthLetter::findOrFail($id);
        $htmlContent = view('pdf.birth_letter', compact('birthLetter'))->render();
        $pdf = Pdf::loadHTML($htmlContent)->setPaper('a4')->setOption('isHtml5ParserEnabled', true)->setOption('isRemoteEnabled', true);
        $namaSurat = Str::slug($birthLetter->name);
        $pdfFileName = "Akta Kelahiran | {$namaSurat}.pdf";
        return $pdf->download($pdfFileName);
    }

    public function streamPDF($id)
    {
        $birthLetter = ModelsBirthLetter::findOrFail($id);
        $pdf = Pdf::loadView('pdf.birth_letter', ['birthLetter' => $birthLetter])->setPaper('a4')->setOption('isHtml5ParserEnabled', true)->setOption('isRemoteEnabled', true);
        $namaSurat = Str::slug($birthLetter->name);
        $pdfFileName = "Akta-Kelahiran-{$namaSurat}.pdf";
        $filePath = storage_path("app/public/pdf/{$pdfFileName}");
        $pdf->save($filePath);
        return $pdf->stream();
    }
}