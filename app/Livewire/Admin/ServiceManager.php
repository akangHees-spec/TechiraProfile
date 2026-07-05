<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Service;
use App\Models\Category;
use App\Models\ServiceFeature;

class ServiceManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterCategory = '';
    public $filterStatus = '';

    public $isEditing = false;
    public $serviceId = null;

    // Form fields
    public $category_id = '';
    public $name = '';
    public $slug = '';
    public $icon = 'check'; // Lucide icon for card
    public $short_description = '';
    public $description = '';
    public $is_featured = false;
    public $is_active = true;
    public $order = 0;

    // Dynamic Service Features Input
    public $featuresInput = []; // [['title' => '', 'icon' => 'check']]

    // Image Upload
    public $imageFile;
    public $existingImageUrl = null;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $service = Service::findOrFail($id);
        $service->is_active = !$service->is_active;
        $service->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status layanan "' . $service->name . '" berhasil diperbarui.'
        ]);
    }

    public function toggleFeatured(int $id): void
    {
        $service = Service::findOrFail($id);
        $service->is_featured = !$service->is_featured;
        $service->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status unggulan layanan "' . $service->name . '" berhasil diperbarui.'
        ]);
    }

    public function updateOrder(array $items): void
    {
        foreach ($items as $item) {
            Service::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Urutan layanan berhasil diperbarui.'
        ]);
    }

    public function addFeatureInput(): void
    {
        $this->featuresInput[] = ['title' => '', 'icon' => 'check'];
    }

    public function removeFeatureInput(int $index): void
    {
        unset($this->featuresInput[$index]);
        $this->featuresInput = array_values($this->featuresInput);
    }

    public function create(): void
    {
        $this->resetForm();
        
        // Default category service
        $defaultCat = Category::where('type', 'service')->first();
        if ($defaultCat) {
            $this->category_id = $defaultCat->id;
        }

        $this->isEditing = true;
        $this->serviceId = null;
    }

    public function edit(int $id): void
    {
        $this->resetForm();
        $service = Service::findOrFail($id);
        $this->serviceId = $service->id;
        $this->category_id = $service->category_id;
        $this->name = $service->name;
        $this->slug = $service->slug;
        $this->icon = $service->icon ?: 'check';
        $this->short_description = $service->short_description ?? '';
        $this->description = $service->description ?? '';
        $this->is_featured = (bool) $service->is_featured;
        $this->is_active = (bool) $service->is_active;
        $this->order = $service->order;
        $this->existingImageUrl = $service->getFirstMediaUrl('image');

        // Load features
        $features = $service->features()->get();
        foreach ($features as $feat) {
            $this->featuresInput[] = [
                'title' => $feat->title,
                'icon' => $feat->icon ?: 'check'
            ];
        }

        $this->isEditing = true;
    }

    public function save(): void
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'short_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'required|integer|min:0',
            'imageFile' => 'nullable|image|max:2048',
            'featuresInput.*.title' => 'required|string|max:255',
            'featuresInput.*.icon' => 'nullable|string|max:50',
        ];

        $this->validate($rules);

        $data = [
            'category_id' => $this->category_id,
            'name' => $this->name,
            'icon' => $this->icon ?: 'check',
            'short_description' => $this->short_description,
            'description' => $this->description,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ];

        if ($this->serviceId) {
            $service = Service::findOrFail($this->serviceId);
            $service->update($data);
            $message = 'Layanan berhasil diperbarui.';
        } else {
            $service = Service::create($data);
            $message = 'Layanan berhasil ditambahkan.';
        }

        // Save features (replace existing ones)
        $service->features()->delete();
        foreach ($this->featuresInput as $feat) {
            if (!empty($feat['title'])) {
                $service->features()->create([
                    'title' => $feat['title'],
                    'icon' => $feat['icon'] ?: 'check'
                ]);
            }
        }

        if ($this->imageFile) {
            $service->addMedia($this->imageFile->getRealPath())
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
        $service = Service::findOrFail($id);
        $service->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Layanan berhasil dihapus.'
        ]);
    }

    public function cancel(): void
    {
        $this->isEditing = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->serviceId = null;
        $this->category_id = '';
        $this->name = '';
        $this->slug = '';
        $this->icon = 'check';
        $this->short_description = '';
        $this->description = '';
        $this->is_featured = false;
        $this->is_active = true;
        $this->order = 0;
        $this->featuresInput = [];
        $this->imageFile = null;
        $this->existingImageUrl = null;
        $this->resetErrorBag();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $query = Service::query()->with('category');

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('short_description', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filterCategory)) {
            $query->where('category_id', $this->filterCategory);
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus);
        }

        $services = $query->orderBy('order')
                          ->orderBy('id', 'desc')
                          ->paginate(10);

        $categories = Category::where('type', 'service')->get();

        return view('livewire.admin.service-manager', [
            'services' => $services,
            'categories' => $categories
        ]);
    }
}
