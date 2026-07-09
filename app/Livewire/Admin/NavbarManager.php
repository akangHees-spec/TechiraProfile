<?php

namespace App\Livewire\Admin;

use App\Models\NavItem;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class NavbarManager extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $isEditing = false;

    public $navItemId = null;

    // Form fields
    public $label = '';

    public $url = '';

    public $parent_id = '';

    public $order = 0;

    public $is_active = true;

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
        $item = NavItem::findOrFail($id);
        $item->is_active = ! $item->is_active;
        $item->save();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Status menu "'.$item->label.'" berhasil diperbarui.',
        ]);
    }

    public function updateOrder(array $items): void
    {
        foreach ($items as $item) {
            NavItem::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Urutan menu berhasil diperbarui.',
        ]);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->isEditing = true;
        $this->navItemId = null;
    }

    public function edit(int $id): void
    {
        $this->resetForm();
        $item = NavItem::findOrFail($id);
        $this->navItemId = $item->id;
        $this->label = $item->label;
        $this->url = $item->url ?? '';
        $this->parent_id = $item->parent_id ?? '';
        $this->order = $item->order;
        $this->is_active = (bool) $item->is_active;

        $this->isEditing = true;
    }

    public function save(): void
    {
        $rules = [
            'label' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:nav_items,id',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];

        $this->validate($rules);

        // Prevent setting self as parent
        if ($this->navItemId && $this->parent_id == $this->navItemId) {
            $this->addError('parent_id', 'Menu tidak boleh menjadi sub-menu dari dirinya sendiri.');

            return;
        }

        $data = [
            'label' => $this->label,
            'url' => $this->url ?: null,
            'parent_id' => $this->parent_id ?: null,
            'order' => $this->order,
            'is_active' => $this->is_active,
        ];

        if ($this->navItemId) {
            $item = NavItem::findOrFail($this->navItemId);
            $item->update($data);
            $message = 'Menu berhasil diperbarui.';
        } else {
            $item = NavItem::create($data);
            $message = 'Menu berhasil ditambahkan.';
        }

        $this->isEditing = false;
        $this->resetForm();

        session()->flash('toast', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function delete(int $id): void
    {
        $item = NavItem::findOrFail($id);
        $item->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Menu berhasil dihapus.',
        ]);
    }

    public function cancel(): void
    {
        $this->isEditing = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->navItemId = null;
        $this->label = '';
        $this->url = '';
        $this->parent_id = '';
        $this->order = 0;
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render(): View
    {
        $query = NavItem::query()->with('parent');

        if (! empty($this->search)) {
            $query->where('label', 'like', '%'.$this->search.'%')
                ->orWhere('url', 'like', '%'.$this->search.'%');
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus);
        }

        $navItems = $query->orderBy('parent_id')
            ->orderBy('order')
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Parent candidates are items that don't have a parent themselves
        $parentsQuery = NavItem::whereNull('parent_id');
        if ($this->navItemId) {
            $parentsQuery->where('id', '!=', $this->navItemId);
        }
        $parents = $parentsQuery->orderBy('order')->get();

        return view('livewire.admin.navbar-manager', [
            'navItems' => $navItems,
            'parents' => $parents,
        ]);
    }
}
