<?php

namespace App\Filament\Resources\Messages;

use App\Filament\Resources\Messages\Pages\ManageMessages;
use App\Models\Message;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Contact';

    protected static ?string $modelLabel = 'message';

    protected static ?string $pluralModelLabel = 'messages';

    protected static ?string $navigationLabel = 'Messages';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canCreate(): bool
    {
        return false; // les messages arrivent uniquement par le formulaire du site
    }

    public static function getNavigationBadge(): ?string
    {
        $unread = Message::where('is_read', false)->count();

        return $unread ? (string) $unread : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Messages non lus';
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('name')->label('Nom'),
                TextEntry::make('email')->label('E-mail')->copyable(),
                TextEntry::make('created_at')->label('Reçu le')->dateTime('d/m/Y à H:i'),
                TextEntry::make('source')->label('Via')->badge()->formatStateUsing(fn (string $state) => Message::SOURCES[$state] ?? $state),
                TextEntry::make('subject')->label('Objet')->placeholder('—')->columnSpanFull(),
                TextEntry::make('message')->label('Message')->columnSpanFull()->prose(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordClasses(fn (Message $record) => $record->is_read ? null : 'font-semibold')
            ->columns([
                IconColumn::make('is_read')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-s-envelope')
                    ->trueColor('gray')
                    ->falseColor('warning')
                    ->tooltip(fn (Message $record) => $record->is_read ? 'Lu' : 'Non lu'),
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->description(fn (Message $record) => $record->email),
                TextColumn::make('message')
                    ->label('Message')
                    ->description(fn (Message $record) => $record->subject, position: 'above')
                    ->limit(80)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('source')
                    ->label('Via')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Message::SOURCES[$state] ?? $state)
                    ->color(fn (string $state) => $state === 'portfolio' ? 'warning' : 'info')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Reçu')
                    ->dateTime('d/m/Y H:i')
                    ->description(fn (Message $record) => $record->created_at?->locale('fr')->diffForHumans())
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->label('Via')
                    ->options(Message::SOURCES),
                TernaryFilter::make('is_read')
                    ->label('Statut')
                    ->trueLabel('Lus')
                    ->falseLabel('Non lus'),
            ])
            ->recordActions([
                // Ouvrir un message le marque comme lu
                ViewAction::make()
                    ->label('Lire')
                    ->modalHeading(fn (Message $record) => 'Message de ' . $record->name)
                    ->mountUsing(fn (Message $record) => $record->is_read || $record->update(['is_read' => true]))
                    ->extraModalFooterActions(fn (Message $record) => [
                        Action::make('repondre')
                            ->label('Répondre par e-mail')
                            ->icon('heroicon-o-paper-airplane')
                            ->url('mailto:' . $record->email . '?subject=' . rawurlencode('Re : ' . ($record->subject ?: 'votre demande sur ' . (Message::SOURCES[$record->source] ?? 'Lovaweb')))),
                    ]),
                Action::make('toggleRead')
                    ->label(fn (Message $record) => $record->is_read ? 'Marquer non lu' : 'Marquer lu')
                    ->icon(fn (Message $record) => $record->is_read ? 'heroicon-o-envelope' : 'heroicon-o-envelope-open')
                    ->color('gray')
                    ->action(fn (Message $record) => $record->update(['is_read' => ! $record->is_read])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markRead')
                        ->label('Marquer comme lus')
                        ->icon('heroicon-o-envelope-open')
                        ->action(fn (Collection $records) => $records->each->update(['is_read' => true]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMessages::route('/'),
        ];
    }
}
