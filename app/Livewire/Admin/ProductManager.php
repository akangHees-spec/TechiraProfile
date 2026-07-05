<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;

class ProductManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterCategory = '';
    public $filterStatus = '';

    public $isEditing = false;
    public $productId = null;

    // Form fields
    public $category_id = '';
    public $name = '';
    public $slug = '';
    public $short_description = '';
    public $description = '';
    public $price = '';
    public $is_featured = false;
    public $is_active = true;
    public $order = 0;

    // Specifications array
    public $specs = []; // [['key' => '', 'value' => '']]

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
        $product = Product::findOrFail($id);
        $product->is_active = !$product->is_active;
        $product->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status produk "' . $product->name . '" berhasil diperbarui.'
        ]);
    }

    public function toggleFeatured(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->is_featured = !$product->is_featured;
        $product->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status unggulan produk "' . $product->name . '" berhasil diperbarui.'
        ]);
    }

    public function updateOrder(array $items): void
    {
        foreach ($items as $item) {
            Product::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Urutan produk berhasil diperbarui.'
        ]);
    }

    public function addSpec(): void
    {
        $this->specs[] = ['key' => '', 'value' => ''];
    }

    public function removeSpec(int $index): void
    {
        unset($this->specs[$index]);
        $this->specs = array_values($this->specs);
    }

    public function create(): void
    {
        $this->resetForm();
        
        // Pick first product category as default if available
        $defaultCat = Category::where('type', 'product')->first();
        if ($defaultCat) {
            $this->category_id = $defaultCat->id;
        }

        $this->isEditing = true;
        $this->productId = null;
    }

    public function edit(int $id): void
    {
        $this->resetForm();
        $product = Product::findOrFail($id);
        $this->productId = $product->id;
        $this->category_id = $product->category_id;
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->short_description = $product->short_description ?? '';
        $this->description = $product->description ?? '';
        $this->price = $product->price;
        $this->is_featured = (bool) $product->is_featured;
        $this->is_active = (bool) $product->is_active;
        $this->order = $product->order;
        $this->existingImageUrl = $product->getFirstMediaUrl('image');

        // Convert JSON specs to array format
        $specsJson = $product->specifications ?? [];
        foreach ($specsJson as $k => $v) {
            $this->specs[] = ['key' => $k, 'value' => $v];
        }

        $this->isEditing = true;
    }

    public function save(): void
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'required|integer|min:0',
            'imageFile' => 'nullable|image|max:2048',
            'specs.*.key' => 'required_with:specs.*.value|string|max:100',
            'specs.*.value' => 'required_with:specs.*.key|string|max:255',
        ];

        $this->validate($rules);

        // Convert specs to associative array
        $specifications = [];
        foreach ($this->specs as $spec) {
            if (!empty($spec['key'])) {
                $specifications[$spec['key']] = $spec['value'];
            }
        }

        $data = [
            'category_id' => $this->category_id,
            'name' => $this->name,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'price' => $this->price ?: null,
            'specifications' => empty($specifications) ? null : $specifications,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'order' => $this->order,
        ];

        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            $product->update($data);
            $message = 'Produk berhasil diperbarui.';
        } else {
            $product = Product::create($data);
            $message = 'Produk berhasil ditambahkan.';
        }

        if ($this->imageFile) {
            $product->addMedia($this->imageFile->getRealPath())
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
        $product = Product::findOrFail($id);
        $product->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Produk berhasil dihapus.'
        ]);
    }

    public function cancel(): void
    {
        $this->isEditing = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->productId = null;
        $this->category_id = '';
        $this->name = '';
        $this->slug = '';
        $this->short_description = '';
        $this->description = '';
        $this->price = '';
        $this->is_featured = false;
        $this->is_active = true;
        $this->order = 0;
        $this->specs = [];
        $this->imageFile = null;
        $this->existingImageUrl = null;
        $this->resetErrorBag();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $query = Product::query()->with('category');

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

        $products = $query->orderBy('order')
                          ->orderBy('id', 'desc')
                          ->paginate(10);

        $categories = Category::where('type', 'product')->get();

        return view('livewire.admin.product-manager', [
            'products' => $products,
            'categories' => $categories
        ]);
    }
}
