<?php

namespace App\Livewire\Movements;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MovementForm extends Component
{
    use WithFileUploads;

    public $product_id, $movement_type, $quantity, $observation, $executed_at;
    public array $files = [];

    public $max_files_per_movement;
    public array $allowed_extensions;
    public int $max_file_size; // KB

    public ?string $successMsg = null;

    public function mount(): void
    {
        $this->executed_at = now()->format('Y-m-d\TH:i');

        // Defaults caso não tenha settings na base
        $this->max_files_per_movement = Setting::get('max_files_per_movement', 1);
        $this->allowed_extensions = Setting::get('upload_allowed_extensions', ['jpg','jpeg','png','webp','tiff','pdf']);
        $this->max_file_size = (int) Setting::get('upload_max_file_size', 5) * 1024; // KB
    }

    public function updatedFiles(): void
    {
        $max = $this->max_files_per_movement === 'unlimited' ? null : (int) $this->max_files_per_movement;
        if ($max && count($this->files) > $max) {
            $this->files = array_slice($this->files, 0, $max);
            $this->addError('files', "Você só pode anexar até {$max} arquivo(s).");
        }
    }

    public function save(): void
    {
        $this->validate([
            'product_id'    => 'required|exists:products,id',
            'movement_type' => 'required|in:entrada,saida',
            'quantity'      => 'required|integer|min:1',
            'executed_at'   => 'required|date',
            'files.*'       => 'file|max:' . $this->max_file_size . '|mimes:' . implode(',', $this->allowed_extensions),
        ]);

        $product = Product::with('items')->findOrFail($this->product_id);
        $qty     = (int) $this->quantity;
        $now     = now();

        DB::beginTransaction();
        try {
            $before = $product->is_consumable
                ? (int) optional(Inventory::firstOrCreate(['product_id' => $product->id]))->qnt_estoque
                : (int) $product->available_count;

            // atualiza estoque
            if ($this->movement_type === 'entrada') {
                if ($product->is_consumable) {
                    $inv = Inventory::firstOrCreate(['product_id' => $product->id], ['qnt_estoque' => 0, 'qnt_ideal' => 0]);
                    $inv->increment('qnt_estoque', $qty);
                } else {
                    // cria N itens disponíveis
                    for ($i = 0; $i < $qty; $i++) {
                        ProductItem::create([
                            'product_id'        => $product->id,
                            'serial_internal'   => null,
                            'serial_external'   => null,
                            'status'            => ProductItem::STATUS_AVAILABLE,
                            'current_holder_id' => null,
                            'acquired_at'       => $now,
                            'notes'             => 'Entrada via formulário Livewire',
                        ]);
                    }
                }
                $type = InventoryMovement::TYPE_ENTRY;
            } else { // saída/consumo
                if ($product->is_consumable) {
                    $inv = Inventory::firstOrCreate(['product_id' => $product->id], ['qnt_estoque' => 0, 'qnt_ideal' => 0]);
                    $inv->decrement('qnt_estoque', $qty);
                } else {
                    // marca itens como consumidos
                    $items = $product->items()
                        ->where('status', ProductItem::STATUS_AVAILABLE)
                        ->limit($qty)
                        ->get();

                    foreach ($items as $it) {
                        $it->update(['status' => ProductItem::STATUS_CONSUMED]);
                    }
                }
                $type = InventoryMovement::TYPE_CONSUMPTION;
            }

            $after = $product->is_consumable
                ? (int) Inventory::firstWhere('product_id', $product->id)->qnt_estoque
                : (int) $product->fresh()->available_count;

            // custo unitário usado para o registro (avg_cost como base)
            $unitCost = (float) ($product->avg_cost ?? $product->unit_price ?? 0);

            // salva metadados dos anexos dentro de properties.attachments
            $attachments = [];
            foreach ($this->files as $file) {
                $path = $file->store('movements', 'public');
                $attachments[] = [
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'extension'     => $file->getClientOriginalExtension(),
                    'size'          => $file->getSize(),
                ];
            }

            InventoryMovement::create([
                'product_id'     => $product->id,
                'product_item_id'=> null,
                'type'           => $type,
                'qty'            => $qty,
                'before_stock'   => $before,
                'after_stock'    => $after,
                'unit_cost'      => $unitCost,
                'total_cost'     => round($unitCost * $qty, 2),
                'collaborator_id'=> null,
                'performed_by'   => Auth::id(),
                'performed_at'   => $this->executed_at,
                'notes'          => $this->observation,
                'properties'     => ['attachments' => $attachments],
            ]);

            DB::commit();

            $this->reset(['product_id', 'movement_type', 'quantity', 'observation', 'files']);
            $this->executed_at = now()->format('Y-m-d\TH:i');
            $this->successMsg = 'Movimentação salva com sucesso!';
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('files', 'Erro ao salvar movimentação: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.movements.movement-form', [
            'products'           => Product::orderBy('name')->get(),
            'allowed_extensions' => $this->allowed_extensions,
        ]);
    }
}
