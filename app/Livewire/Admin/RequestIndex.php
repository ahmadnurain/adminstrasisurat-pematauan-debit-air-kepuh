<?php

namespace App\Livewire\Admin;

use App\Models\Request as RequestModel;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use WireUi\Traits\WireUiActions;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RequestIndex extends Component
{
    use WireUiActions;

    #[Layout('layouts.admin')]
    #[Title('Permintaan Surat | Desa Kepuh')]

    public $isModalOpen = false;
    public $modalImage = null;
    public $domicileLetter = null;
    public $isModalOpenpdf = false;

    public string $q = '';
    public string $status = '';
    public int $perPage = 10;

    public ?string $cursor = null;
    public ?string $prevCursor = null;
    public ?string $nextCursor = null;

    public function updatingQ()
    {
        $this->cursor = null;
    }

    public function updatingStatus()
    {
        $this->cursor = null;
    }

    public function resetFilters()
    {
        $this->q = '';
        $this->status = '';
        $this->cursor = null;
    }

    public function goto(string $cursor = null)
    {
        $this->cursor = $cursor ?: null;
    }

    public function confirmApprove($id)
    {
        $this->dialog()->confirm([
            'title' => 'Apakah anda setuju dengan permintaan ini?',
            'icon' => 'question',
            'accept' => [
                'label' => 'Ya, Setuju',
                'method' => 'approved',
                'params' => $id,
            ],
            'reject' => [
                'label' => 'Tidak',
                'method' => 'cancel',
            ],
        ]);
    }

    public function approved($id)
    {
        $req = RequestModel::find($id);
        if ($req) {
            $req->update(['request_status_id' => 2]);
            $this->dialog()->show(['icon' => 'success', 'title' => 'Berhasil Disetujui']);
        }
    }

    public function confirmReject($id)
    {
        $this->dialog()->confirm([
            'title' => 'Apakah anda menolak permintaan ini?',
            'icon' => 'question',
            'accept' => [
                'label' => 'Ya, Tolak',
                'method' => 'rejected',
                'params' => $id,
            ],
            'reject' => [
                'label' => 'Tidak',
                'method' => 'cancel',
            ],
        ]);
    }

    public function rejected($id)
    {
        $req = RequestModel::find($id);
        if ($req) {
            $req->update(['request_status_id' => 3]);
            $this->dialog()->show(['icon' => 'success', 'title' => 'Berhasil Ditolak']);
        }
    }

    public function confirmProcess($id)
    {
        $this->dialog()->confirm([
            'title' => 'Apakah anda ingin memproses permintaan ini?',
            'icon' => 'question',
            'accept' => [
                'label' => 'Ya, Proses',
                'method' => 'processed',
                'params' => $id,
            ],
            'reject' => [
                'label' => 'Tidak',
                'method' => 'cancel',
            ],
        ]);
    }

    public function processed($id)
    {
        $req = RequestModel::find($id);
        if ($req) {
            $req->update(['request_status_id' => 4]);
            $this->dialog()->show(['icon' => 'success', 'title' => 'Berhasil Diproses']);
        }
    }

    public function confirmComplete($id)
    {
        $this->dialog()->confirm([
            'title' => 'Apakah permintaan ini telah selesai?',
            'icon' => 'question',
            'accept' => [
                'label' => 'Ya, Selesai',
                'method' => 'completed',
                'params' => $id,
            ],
            'reject' => [
                'label' => 'Belum',
                'method' => 'cancel',
            ],
        ]);
    }

    public function completed($id)
    {
        $req = RequestModel::find($id);
        if ($req) {
            $req->update(['request_status_id' => 5]);
            $this->dialog()->show(['icon' => 'success', 'title' => 'Surat Telah Selesai Dibuat']);
        }
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

    public function showPdfModalPDF($id)
    {
        $req = RequestModel::with('domicileLetter')->find($id);
        $this->domicileLetter = $req && $req->domicileLetter ? ['content' => $req->domicileLetter->content] : null;
        $this->isModalOpenpdf = true;
    }

    public function downloadPdf($id): ?StreamedResponse
    {
        $req = RequestModel::with('domicileLetter')->find($id);
        if (! $req || ! $req->domicileLetter) {
            $this->dialog()->show(['icon' => 'error', 'title' => 'Data surat tidak tersedia']);
            return null;
        }
        $content = $req->domicileLetter->content;
        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'surat-' . $id . '.pdf', ['Content-Type' => 'application/pdf']);
    }

    protected function query()
    {
        $q = RequestModel::query()->with(['user', 'typeLetter', 'domicileLetter']);
        if ($this->q !== '') {
            $term = '%' . $this->q . '%';
            $q->where(function ($sub) use ($term) {
                $sub->whereHas('user', fn($u) => $u->where('name', 'like', $term))
                    ->orWhereHas('typeLetter', fn($t) => $t->where('name', 'like', $term))
                    ->orWhere('id', 'like', $term);
            });
        }
        if ($this->status !== '') {
            $q->where('request_status_id', (int) $this->status);
        }
        return $q->orderBy('created_at', 'desc');
    }

    public function render()
    {
        $paginator = $this->query()->cursorPaginate($this->perPage, ['*'], 'cursor', $this->cursor);
        $this->prevCursor = optional($paginator->previousCursor())->encode();
        $this->nextCursor = optional($paginator->nextCursor())->encode();

        return view('livewire.admin.request-index', [
            'requests' => $paginator,
        ]);
    }
}