<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Setting;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'المعاملات المالية';

    protected static ?string $modelLabel = 'معاملة';

    protected static ?string $pluralModelLabel = 'المعاملات';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'الإدارة المالية';    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات المعاملة')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('نوع المعاملة')
                            ->options([
                                'income' => 'إيراد',
                                'expense' => 'مصروف',
                                'refund' => 'استرجاع',
                            ])
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\Select::make('category')
                            ->label('التصنيف')
                            ->options(function ($get) {
                                $type = $get('type');
                                if ($type === 'income') {
                                    return [
                                        'sales' => 'مبيعات',
                                        'other_income' => 'إيرادات أخرى',
                                    ];
                                } elseif ($type === 'expense') {
                                    return [
                                        'shipping' => 'شحن',
                                        'marketing' => 'تسويق',
                                        'supplies' => 'لوازم',
                                        'salaries' => 'رواتب',
                                        'rent' => 'إيجار',
                                        'utilities' => 'خدمات',
                                        'other_expense' => 'مصروفات أخرى',
                                    ];
                                } else {
                                    return [
                                        'refund' => 'استرجاع مبلغ',
                                    ];
                                }
                            })
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ')
                            ->required()
                            ->numeric()
                            ->prefix(Setting::get('currency_symbol', '\u20aa'))
                            ->minValue(0),
                    ])->columns(3),

                Forms\Components\Section::make('التفاصيل')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->label('رقم الطلب')
                            ->relationship('order', 'id')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->helperText('اختر الطلب المرتبط (اختياري)'),

                        Forms\Components\Select::make('payment_method')
                            ->label('طريقة الدفع')
                            ->options([
                                'cash' => 'نقدي',
                                'cash_on_delivery' => 'الدفع عند الاستلام',
                                'bank_transfer' => 'تحويل بنكي',
                                'credit_card' => 'بطاقة ائتمان',
                                'paypal' => 'باي بال',
                                'stripe' => 'سترايب',
                                'other' => 'أخرى',
                            ])
                            ->native(false),

                        Forms\Components\TextInput::make('reference')
                            ->label('المرجع')
                            ->maxLength(100)
                            ->helperText('رقم المرجع أو الفاتورة'),
                    ])->columns(3),

                Forms\Components\Section::make('ملاحظات')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'income' => 'إيراد',
                        'expense' => 'مصروف',
                        'refund' => 'استرجاع',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                        'warning' => 'refund',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('التصنيف')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sales' => 'مبيعات',
                        'shipping' => 'شحن',
                        'marketing' => 'تسويق',
                        'supplies' => 'لوازم',
                        'salaries' => 'رواتب',
                        'rent' => 'إيجار',
                        'utilities' => 'خدمات',
                        'other_income' => 'إيرادات أخرى',
                        'other_expense' => 'مصروفات أخرى',
                        'refund' => 'استرجاع مبلغ',
                        default => $state,
                    })
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->formatStateUsing(fn ($state, $record) =>
                        ($record->type === 'expense' || $record->type === 'refund' ? '-' : '+') .
                        number_format($state, 0) . ' ' . Setting::get('currency_symbol', '\u20aa')
                    )
                    ->color(fn ($record) => match ($record->type) {
                        'income' => 'success',
                        'expense' => 'danger',
                        'refund' => 'warning',
                        default => null,
                    })
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('المجموع')
                            ->formatStateUsing(function ($state) {
                                $income = Transaction::where('type', 'income')->sum('amount');
                                $expense = Transaction::where('type', 'expense')->sum('amount');
                                $refund = Transaction::where('type', 'refund')->sum('amount');
                                $total = $income - $expense - $refund;
                                return number_format($total, 0) . ' ' . Setting::get('currency_symbol', '\u20aa');
                            }),
                    ]),

                Tables\Columns\TextColumn::make('order.id')
                    ->label('رقم الطلب')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('طريقة الدفع')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'cash' => 'نقدي',
                        'cash_on_delivery' => 'الدفع عند الاستلام',
                        'bank_transfer' => 'تحويل بنكي',
                        'credit_card' => 'بطاقة ائتمان',
                        'paypal' => 'باي بال',
                        'stripe' => 'سترايب',
                        'other' => 'أخرى',
                        default => $state ?? '-',
                    })
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('reference')
                    ->label('المرجع')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع المعاملة')
                    ->options([
                        'income' => 'إيراد',
                        'expense' => 'مصروف',
                        'refund' => 'استرجاع',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('category')
                    ->label('التصنيف')
                    ->options([
                        'sales' => 'مبيعات',
                        'shipping' => 'شحن',
                        'marketing' => 'تسويق',
                        'supplies' => 'لوازم',
                        'salaries' => 'رواتب',
                        'rent' => 'إيجار',
                        'utilities' => 'خدمات',
                        'other_income' => 'إيرادات أخرى',
                        'other_expense' => 'مصروفات أخرى',
                        'refund' => 'استرجاع مبلغ',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options([
                        'cash' => 'نقدي',
                        'cash_on_delivery' => 'الدفع عند الاستلام',
                        'bank_transfer' => 'تحويل بنكي',
                        'credit_card' => 'بطاقة ائتمان',
                        'paypal' => 'باي بال',
                        'stripe' => 'سترايب',
                        'other' => 'أخرى',
                    ])
                    ->native(false),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('من تاريخ')
                            ->native(false),
                        Forms\Components\DatePicker::make('until')
                            ->label('إلى تاريخ')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
