<?php

namespace App\Filament\Pages;

use App\Models\ConsignmentRestockRequest;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class RestockRequests extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-inbox-arrow-down';
    protected static ?string $navigationLabel = 'Pedidos Reposición';
    protected static ?string $title           = 'Pedidos de Reposición';
    protected static ?int    $navigationSort  = 8;
    protected static bool    $shouldRegisterNavigation = false;
    protected static string  $view            = 'filament.pages.restock-requests';

    public function getRequests(): Collection
    {
        return ConsignmentRestockRequest::with('wholesaler')
            ->orderByRaw("FIELD(status, 'pending', 'seen', 'completed')")
            ->orderByDesc('created_at')
            ->get();
    }

    public function markSeen(int $id): void
    {
        ConsignmentRestockRequest::where('id', $id)->update(['status' => 'seen']);
        Notification::make()->title('Marcado como visto')->success()->send();
    }

    public function markCompleted(int $id): void
    {
        ConsignmentRestockRequest::where('id', $id)->update(['status' => 'completed']);
        Notification::make()->title('Marcado como completado')->success()->send();
    }

    public function deletePedido(int $id): void
    {
        ConsignmentRestockRequest::destroy($id);
        Notification::make()->title('Eliminado')->success()->send();
    }
}
