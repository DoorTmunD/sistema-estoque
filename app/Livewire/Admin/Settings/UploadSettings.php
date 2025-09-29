<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Models\Setting;

class UploadSettings extends Component
{
    public $max_file_size;
    public $allowed_extensions = [];
    public $product_image_required = false;
    public $max_files_per_movement = 1; // 1, 2, ou 'unlimited'

    public function mount()
    {
        $this->max_file_size = Setting::get('upload_max_file_size', 5);
        $this->allowed_extensions = Setting::get('upload_allowed_extensions', ['jpg','png','webp','tiff','pdf']);
        $this->product_image_required = Setting::get('product_image_required', false);
        $this->max_files_per_movement = Setting::get('max_files_per_movement', 1);
    }

    public function save()
    {
        Setting::set('upload_max_file_size', $this->max_file_size);
        Setting::set('upload_allowed_extensions', $this->allowed_extensions);
        Setting::set('product_image_required', $this->product_image_required);
        Setting::set('max_files_per_movement', $this->max_files_per_movement);
        session()->flash('success', 'Configurações salvas com sucesso!');
    }

    public function render()
    {
        return view('admin.settings.upload-settings');
    }
}
