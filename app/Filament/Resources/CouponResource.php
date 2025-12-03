<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Filament\Resources\CouponResource\RelationManagers;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'كوبونات الخصم';

    protected static ?string $modelLabel = 'كوبون';

    protected static ?string $pluralModelLabel = 'الكوبونات';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'التسويق';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الكوبون')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('كود الخصم')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->uppercase()
                            ->helperText('الكود الذي سيستخدمه العميل'),

                        Forms\Components\Select::make('type')
                            ->label('نوع الخصم')
                            ->options([
                                'percentage' => 'نسبة مئوية',
                                'fixed' => 'مبلغ ثابت',
                            ])
                            ->required()
                            ->live()
                            ->native(false),

                        Forms\Components\TextInput::make('value')
                            ->label('قيمة الخصم')
                            ->required()
                            ->numeric()
                            ->suffix(fn ($get) => $get('type') === 'percentage' ? '%' : 'ل.س')
                            ->helperText(fn ($get) => $get('type') === 'percentage' ? 'أدخل النسبة المئوية (مثال: 10 = 10%)' : 'أدخل المبلغ بالليرة السورية'),

                        Forms\Components\TextInput::make('min_purchase')
                            ->label('الحد الأدنى للشراء')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('ل.س')
                            ->helperText('أقل مبلغ يجب أن يدفعه العميل لاستخدام الكوبون'),
                    ])->columns(2),

                Forms\Components\Section::make('حدود الاستخدام')
                    ->schema([
                        Forms\Components\TextInput::make('max_uses')
                            ->label('الحد الأقصى للاستخدام')
                            ->numeric()
                            ->helperText('اتركه فارغاً لاستخدام غير محدود'),

                        Forms\Components\TextInput::make('used_count')
                            ->label('عدد مرات الاستخدام')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),

                Forms\Components\Section::make('الفترة الزمنية')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('تاريخ البداية')
                            ->native(false),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('تاريخ النهاية')
                            ->native(false)
                            ->after('start_date'),
                    ])->columns(2),

                Forms\Components\Section::make('الحالة')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('مفعّل')
                            ->default(true)
                            ->helperText('هل الكوبون نشط ويمكن استخدامه؟'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('الكود')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percentage' => 'نسبة مئوية',
                        'fixed' => 'مبلغ ثابت',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'percentage',
                        'info' => 'fixed',
                    ]),

                Tables\Columns\TextColumn::make('value')
                    ->label('القيمة')
                    ->formatStateUsing(fn ($record) => $record->type === 'percentage' ? $record->value . '%' : number_format($record->value, 0) . ' ل.س')
                    ->sortable(),

                Tables\Columns\TextColumn::make('min_purchase')
                    ->label('الحد الأدنى للشراء')
                    ->formatStateUsing(fn ($state) => number_format($state, 0) . ' ل.س')
                    ->sortable(),

                Tables\Columns\TextColumn::make('usage')
                    ->label('الاستخدام')
                    ->formatStateUsing(fn ($record) => $record->max_uses
                        ? "{$record->used_count} / {$record->max_uses}"
                        : "{$record->used_count} / غير محدود")
                    ->badge()
                    ->color(fn ($record) => $record->max_uses && $record->used_count >= $record->max_uses
                        ? 'danger'
                        : 'success'),

                Tables\Columns\TextColumn::make('validity')
                    ->label('الصلاحية')
                    ->formatStateUsing(function ($record) {
                        $now = now();
                        if ($record->start_date && $now->lt($record->start_date)) {
                            return 'لم يبدأ بعد';
                        }
                        if ($record->end_date && $now->gt($record->end_date)) {
                            return 'منتهي';
                        }
                        if ($record->start_date && $record->end_date) {
                            return $record->start_date->format('Y/m/d') . ' - ' . $record->end_date->format('Y/m/d');
                        }
                        return 'دائم';
                    })
                    ->badge()
                    ->color(function ($record) {
                        $now = now();
                        if ($record->end_date && $now->gt($record->end_date)) {
                            return 'danger';
                        }
                        if ($record->start_date && $now->lt($record->start_date)) {
                            return 'warning';
                        }
                        return 'success';
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('مفعّل')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع الخصم')
                    ->options([
                        'percentage' => 'نسبة مئوية',
                        'fixed' => 'مبلغ ثابت',
                    ])
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('الكل')
                    ->trueLabel('مفعّل')
                    ->falseLabel('معطّل')
                    ->native(false),

                Tables\Filters\Filter::make('expired')
                    ->label('منتهية الصلاحية')
                    ->query(fn ($query) => $query->where('end_date', '<', now()))
                    ->toggle(),

                Tables\Filters\Filter::make('not_started')
                    ->label('لم تبدأ بعد')
                    ->query(fn ($query) => $query->where('start_date', '>', now()))
                    ->toggle(),

                Tables\Filters\Filter::make('fully_used')
                    ->label('مستخدم بالكامل')
                    ->query(fn ($query) => $query->whereNotNull('max_uses')->whereColumn('used_count', '>=', 'max_uses'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
                Tables\Actions\Action::make('toggle_status')
                    ->label(fn ($record) => $record->is_active ? 'تعطيل' : 'تفعيل')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                    ->action(function ($record) {
                        $record->update(['is_active' => !$record->is_active]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => $record->is_active ? 'تعطيل الكوبون' : 'تفعيل الكوبون')
                    ->modalDescription(fn ($record) => $record->is_active
                        ? 'هل تريد تعطيل هذا الكوبون؟ لن يتمكن العملاء من استخدامه بعد الآن.'
                        : 'هل تريد تفعيل هذا الكوبون؟ سيتمكن العملاء من استخدامه فوراً.')
                    ->modalSubmitActionLabel('تأكيد'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('تفعيل المحدد')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('تعطيل المحدد')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
