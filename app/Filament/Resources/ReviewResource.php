<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'التقييمات';

    protected static ?string $modelLabel = 'تقييم';

    protected static ?string $pluralModelLabel = 'التقييمات';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'المحتوى';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات التقييم')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('المنتج')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Forms\Components\Select::make('user_id')
                            ->label('المستخدم')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->helperText('اتركه فارغاً للعملاء الضيوف'),

                        Forms\Components\TextInput::make('customer_name')
                            ->label('اسم العميل')
                            ->required()
                            ->maxLength(100),
                    ])->columns(3),

                Forms\Components\Section::make('التقييم والتعليق')
                    ->schema([
                        Forms\Components\Select::make('rating')
                            ->label('التقييم')
                            ->options([
                                5 => '⭐⭐⭐⭐⭐ ممتاز (5)',
                                4 => '⭐⭐⭐⭐ جيد جداً (4)',
                                3 => '⭐⭐⭐ جيد (3)',
                                2 => '⭐⭐ مقبول (2)',
                                1 => '⭐ ضعيف (1)',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Textarea::make('comment')
                            ->label('التعليق')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('حالة الموافقة')
                    ->schema([
                        Forms\Components\Toggle::make('is_approved')
                            ->label('موافق عليه')
                            ->helperText('هل تريد عرض هذا التقييم للعملاء؟')
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('المنتج')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('اسم العميل')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('التقييم')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state) . " ($state/5)")
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('comment')
                    ->label('التعليق')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_approved')
                    ->label('موافق عليه')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التقييم')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('التقييم')
                    ->options([
                        5 => '⭐⭐⭐⭐⭐ (5)',
                        4 => '⭐⭐⭐⭐ (4)',
                        3 => '⭐⭐⭐ (3)',
                        2 => '⭐⭐ (2)',
                        1 => '⭐ (1)',
                    ])
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('حالة الموافقة')
                    ->placeholder('الكل')
                    ->trueLabel('موافق عليه')
                    ->falseLabel('بانتظار الموافقة')
                    ->native(false),

                Tables\Filters\Filter::make('high_rating')
                    ->label('تقييمات عالية (4-5)')
                    ->query(fn ($query) => $query->where('rating', '>=', 4))
                    ->toggle(),

                Tables\Filters\Filter::make('low_rating')
                    ->label('تقييمات منخفضة (1-2)')
                    ->query(fn ($query) => $query->where('rating', '<=', 2))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('موافقة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (Review $record) => $record->update(['is_approved' => true]))
                    ->visible(fn (Review $record) => !$record->is_approved)
                    ->requiresConfirmation()
                    ->modalHeading('الموافقة على التقييم')
                    ->modalDescription('هل تريد الموافقة على هذا التقييم؟ سيظهر للعملاء فوراً.')
                    ->modalSubmitActionLabel('موافقة'),

                Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(fn (Review $record) => $record->update(['is_approved' => false]))
                    ->visible(fn (Review $record) => $record->is_approved)
                    ->requiresConfirmation()
                    ->modalHeading('إلغاء الموافقة')
                    ->modalDescription('هل تريد إلغاء الموافقة على هذا التقييم؟ لن يظهر للعملاء بعد الآن.')
                    ->modalSubmitActionLabel('رفض'),

                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('الموافقة على المحدد')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_approved' => true]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('reject')
                        ->label('رفض المحدد')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_approved' => false]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ]);
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
