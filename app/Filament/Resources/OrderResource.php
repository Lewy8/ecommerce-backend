<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Customer Details')
                            ->schema([
                                Forms\Components\TextInput::make('customer_name')
                                    ->required(),
                                Forms\Components\TextInput::make('customer_email')
                                    ->email(),
                                Forms\Components\TextInput::make('customer_phone')
                                    ->tel(),
                            ])
                            ->columns(2),
                        Forms\Components\Section::make('Order Items')
                            ->schema([
                                Forms\Components\Repeater::make('items')
                                    ->schema([
                                        Forms\Components\TextInput::make('product_id')
                                            ->label('Product ID')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('name')
                                            ->label('Product Name')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('quantity')
                                            ->numeric()
                                            ->disabled(),
                                        Forms\Components\TextInput::make('price')
                                            ->numeric()
                                            ->prefix('$')
                                            ->disabled(),
                                    ])
                                    ->columnSpanFull()
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Order Status')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'shipped' => 'Shipped',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->required()
                                    ->native(false),
                                Forms\Components\TextInput::make('total_price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled() // Usually calculated from items
                                    ->dehydrated(), // Save it even if disabled
                            ]),
                        Forms\Components\MarkdownEditor::make('notes')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'warning',
                        'shipped' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
