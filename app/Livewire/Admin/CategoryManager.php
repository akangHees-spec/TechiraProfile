<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use Illuminate\Validation\Rule;

class CategoryManager extends Component
{
    use WithPagination;

    // Search and filters
    public $search = '';
    public $filterType = '';
    public $filterStatus = '';

    // Editor state
    public $isEditing = false;
    public $categoryId = null;

    // Form fields
    public $name = '';
    public $slug = ''; // Optional, generated automatically by spatie sluggable
    public $type = 'product';
    public $icon = 'folder';
    public $description = '';
    public $whatsapp_number = '';
    public $is_active = true;
    public $order = 0;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $category = Category::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status kategori "' . $category->name . '" berhasil diperbarui.'
        ]);
    }

    public function updateOrder(array $items): void
    {
        foreach ($items as $item) {
            Category::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Urutan kategori berhasil diperbarui.'
        ]);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->isEditing = true;
        $this->categoryId = null;
    }

    public function edit(int $id): void
    {
        $this->resetForm();
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->type = $category->type;
        $this->icon = $category->icon ?: 'folder';
        $this->description = $category->description ?? '';
        $this->whatsapp_number = $category->whatsapp_number ?? '';
        $this->is_active = (bool) $category->is_active;
        $this->order = $category->order;

        $this->isEditing = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['product', 'service'])],
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'whatsapp_number' => 'nullable|string|max:30',
            'is_active' => 'boolean',
            'order' => 'required|integer|min:0',
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'icon' => $this->icon ?: 'folder',
            'description' => $this->description,
            'whatsapp_number' => $this->whatsapp_number,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ];

        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            $category->update($data);
            $message = 'Kategori "' . $this->name . '" berhasil diperbarui.';
        } else {
            $category = Category::create($data);
            $message = 'Kategori "' . $this->name . '" berhasil ditambahkan.';
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
        $category = Category::findOrFail($id);
        $category->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Kategori berhasil dihapus.'
        ]);
    }

    public function cancel(): void
    {
        $this->isEditing = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->categoryId = null;
        $this->name = '';
        $this->slug = '';
        $this->type = 'product';
        $this->icon = 'folder';
        $this->description = '';
        $this->whatsapp_number = '';
        $this->is_active = true;
        $this->order = 0;
        $this->resetErrorBag();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $query = Category::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->filterType)) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus);
        }

        $categories = $query->orderBy('order')
                            ->orderBy('id', 'desc')
                            ->paginate(10);

        return view('livewire.admin.category-manager', [
            'categories' => $categories
        ]);
    }
}
