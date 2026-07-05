<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\PageSection;
use Illuminate\Validation\Rule;

class PageSectionManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterStatus = '';

    public $isEditing = false;
    public $sectionId = null;

    // Form fields
    public $section_key = '';
    public $title = '';
    public $subtitle = '';
    public $content = '';
    public $is_active = true;
    public $order = 0;

    // Image Upload
    public $imageFile;
    public $existingImageUrl = null;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $section = PageSection::findOrFail($id);
        $section->is_active = !$section->is_active;
        $section->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status section "' . $section->section_key . '" berhasil diperbarui.'
        ]);
    }

    public function updateOrder(array $items): void
    {
        foreach ($items as $item) {
            PageSection::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Urutan section berhasil diperbarui.'
        ]);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->isEditing = true;
        $this->sectionId = null;
    }

    public function edit(int $id): void
    {
        $this->resetForm();
        $section = PageSection::findOrFail($id);
        $this->sectionId = $section->id;
        $this->section_key = $section->section_key;
        $this->title = $section->title ?? '';
        $this->subtitle = $section->subtitle ?? '';
        $this->content = $section->content ?? '';
        $this->is_active = (bool) $section->is_active;
        $this->order = $section->order;
        $this->existingImageUrl = $section->getFirstMediaUrl('image');

        $this->isEditing = true;
    }

    public function save(): void
    {
        $rules = [
            'section_key' => ['required', 'string', 'max:50', Rule::unique('page_sections', 'section_key')->ignore($this->sectionId)],
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'required|integer|min:0',
            'imageFile' => 'nullable|image|max:2048',
        ];

        $this->validate($rules);

        $data = [
            'section_key' => $this->section_key,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'content' => $this->content,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ];

        if ($this->sectionId) {
            $section = PageSection::findOrFail($this->sectionId);
            $section->update($data);
            $message = 'Konten Halaman berhasil diperbarui.';
        } else {
            $section = PageSection::create($data);
            $message = 'Konten Halaman berhasil ditambahkan.';
        }

        if ($this->imageFile) {
            $section->addMedia($this->imageFile->getRealPath())
                ->usingFileName($this->imageFile->getClientOriginalName())
                ->toMediaCollection('image');
        }

        $this->isEditing = false;
        $this->resetForm();

        session()->flash('toast', [
            'type' => 'success',
            'message' => $message
        ]);
    }

    public function delete(int $id): void
    {
        $section = PageSection::findOrFail($id);
        $section->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Konten Halaman berhasil dihapus.'
        ]);
    }

    public function cancel(): void
    {
        $this->isEditing = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->sectionId = null;
        $this->section_key = '';
        $this->title = '';
        $this->subtitle = '';
        $this->content = '';
        $this->is_active = true;
        $this->order = 0;
        $this->imageFile = null;
        $this->existingImageUrl = null;
        $this->resetErrorBag();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $query = PageSection::query();

        if (!empty($this->search)) {
            $query->where('section_key', 'like', '%' . $this->search . '%')
                  ->orWhere('title', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus);
        }

        $sections = $query->orderBy('order')
                          ->orderBy('id', 'desc')
                          ->paginate(10);

        return view('livewire.admin.page-section-manager', [
            'sections' => $sections
        ]);
    }
}
