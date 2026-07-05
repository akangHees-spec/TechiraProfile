<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Slider;

class SliderManager extends Component
{
    use WithPagination, WithFileUploads;

    // Search & Filter
    public $search = '';
    public $filterStatus = '';

    // Editor state
    public $isEditing = false;
    public $sliderId = null;

    // Form fields
    public $title = '';
    public $subtitle = '';
    public $button_text = '';
    public $button_link = '';
    public $is_active = true;
    public $order = 0;
    
    // File upload
    public $imageFile;
    public $existingImageUrl = null;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $slider = Slider::findOrFail($id);
        $slider->is_active = !$slider->is_active;
        $slider->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status slider "' . $slider->title . '" berhasil diperbarui.'
        ]);
    }

    public function updateOrder(array $items): void
    {
        foreach ($items as $item) {
            Slider::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Urutan slider berhasil diperbarui.'
        ]);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->isEditing = true;
        $this->sliderId = null;
    }

    public function edit(int $id): void
    {
        $this->resetForm();
        $slider = Slider::findOrFail($id);
        $this->sliderId = $slider->id;
        $this->title = $slider->title;
        $this->subtitle = $slider->subtitle ?? '';
        $this->button_text = $slider->button_text ?? '';
        $this->button_link = $slider->button_link ?? '';
        $this->is_active = (bool) $slider->is_active;
        $this->order = $slider->order;
        $this->existingImageUrl = $slider->getFirstMediaUrl('image');

        $this->isEditing = true;
    }

    public function save(): void
    {
        $rules = [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'order' => 'required|integer|min:0',
            'imageFile' => $this->sliderId ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ];

        $this->validate($rules);

        $data = [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'button_text' => $this->button_text,
            'button_link' => $this->button_link,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ];

        if ($this->sliderId) {
            $slider = Slider::findOrFail($this->sliderId);
            $slider->update($data);
            $message = 'Slider berhasil diperbarui.';
        } else {
            $slider = Slider::create($data);
            $message = 'Slider berhasil ditambahkan.';
        }

        // Handle Spatie Media upload
        if ($this->imageFile) {
            $slider->addMedia($this->imageFile->getRealPath())
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
        $slider = Slider::findOrFail($id);
        $slider->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Slider berhasil dihapus.'
        ]);
    }

    public function cancel(): void
    {
        $this->isEditing = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->sliderId = null;
        $this->title = '';
        $this->subtitle = '';
        $this->button_text = '';
        $this->button_link = '';
        $this->is_active = true;
        $this->order = 0;
        $this->imageFile = null;
        $this->existingImageUrl = null;
        $this->resetErrorBag();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $query = Slider::query();

        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('subtitle', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus);
        }

        $sliders = $query->orderBy('order')
                         ->orderBy('id', 'desc')
                         ->paginate(10);

        return view('livewire.admin.slider-manager', [
            'sliders' => $sliders
        ]);
    }
}
