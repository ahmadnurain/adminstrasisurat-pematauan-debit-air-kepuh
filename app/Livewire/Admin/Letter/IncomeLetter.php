<?php

namespace App\Livewire\Admin\Letter;

use App\Models\Request;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;

class IncomeLetter extends Component
{
    #[Layout('layouts.admin')]
    #[Title('Surat Pendapatan | Desa Kepuh')]

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
            ->with(['incomeLetter', 'typeLetter', 'user'])
            ->whereHas('typeLetter', fn($q) => $q->where('name', 'Surat Pendapatan'))
            ->where('request_status_id', 5)
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereHas('incomeLetter', function ($i) use ($term) {
                        $i->where('name', 'like', $term)
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

        return view('livewire.admin.letter.income-letter', [
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

    public function terbilang($angka)
    {
        $huruf = [
            '',
            'Satu',
            'Dua',
            'Tiga',
            'Empat',
            'Lima',
            'Enam',
            'Tujuh',
            'Delapan',
            'Sembilan',
            'Sepuluh',
            'Sebelas',
            'Dua Belas',
            'Tiga Belas',
            'Empat Belas',
            'Lima Belas',
            'Enam Belas',
            'Tujuh Belas',
            'Delapan Belas',
            'Sembilan Belas',
            'Dua Puluh',
            'Tiga Puluh',
            'Empat Puluh',
            'Lima Puluh',
            'Enam Puluh',
            'Tujuh Puluh',
            'Delapan Puluh',
            'Sembilan Puluh'
        ];
        $angka = (int) $angka;
        if ($angka < 12) return $huruf[$angka];
        if ($angka < 20) return $huruf[$angka - 10] . ' Belas';
        if ($angka < 100) return $huruf[intval($angka / 10)] . ' Puluh ' . $huruf[$angka % 10];
        if ($angka < 200) return 'Seratus ' . $this->terbilang($angka - 100);
        if ($angka < 1000) return $huruf[intval($angka / 100)] . ' Ratus ' . $this->terbilang($angka % 100);
        if ($angka < 2000) return 'Seribu ' . $this->terbilang($angka - 1000);
        if ($angka < 1000000) return $this->terbilang(intval($angka / 1000)) . ' Ribu ' . $this->terbilang($angka % 1000);
        if ($angka < 1000000000) return $this->terbilang(intval($angka / 1000000)) . ' Juta ' . $this->terbilang($angka % 1000000);
        if ($angka < 1000000000000) return $this->terbilang(intval($angka / 1000000000)) . ' Miliar ' . $this->terbilang($angka % 1000000000);
        return $angka;
    }

    public function streamPDF($id)
    {
        $incomeLetter = \App\Models\IncomeLetter::findOrFail($id);
        $formattedIncome = number_format($incomeLetter->average_income, 0, ',', '.');
        $incomeText = $this->terbilang($incomeLetter->average_income);
        $pdf = Pdf::loadView('pdf.income_letter', [
            'income' => $incomeLetter,
            'formattedIncome' => $formattedIncome,
            'incomeText' => ucwords($incomeText)
        ])->setPaper('a4')->setOption('isHtml5ParserEnabled', true)->setOption('isRemoteEnabled', true);
        $namaSurat = Str::slug($incomeLetter->name);
        $pdfFileName = "Surat-Keterangan-Penghasilan-{$namaSurat}.pdf";
        $filePath = storage_path("app/public/pdf/{$pdfFileName}");
        $pdf->save($filePath);
        return $pdf->stream();
    }

    public function downloadPDF($id)
    {
        $income = \App\Models\IncomeLetter::findOrFail($id);
        $formattedIncome = number_format($income->average_income, 0, ',', '.');
        $incomeText = $this->terbilang($income->average_income);
        $pdf = Pdf::loadView('pdf.income_letter', [
            'income' => $income,
            'formattedIncome' => $formattedIncome,
            'incomeText' => ucwords($incomeText)
        ])->setPaper('a4')->setOption('isHtml5ParserEnabled', true)->setOption('isRemoteEnabled', true);
        $namaSurat = Str::slug($income->name);
        $pdfFileName = "Surat Keterangan Penghasilan | {$namaSurat}.pdf";
        return $pdf->download($pdfFileName);
    }
}