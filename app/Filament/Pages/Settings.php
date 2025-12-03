<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'إعدادات المتجر';

    protected static ?string $title = 'إعدادات المتجر';

    protected static ?int $navigationSort = 100;

    protected static ?string $navigationGroup = 'الإدارة';

    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'store_name' => Setting::get('store_name', 'متجر مستحضرات التجميل'),
            'store_email' => Setting::get('store_email', 'info@store.com'),
            'store_phone' => Setting::get('store_phone', '+972 123 456 789'),
            'store_address' => Setting::get('store_address', 'فلسطين'),
            'store_logo' => Setting::get('store_logo'),
            'currency' => Setting::get('currency', 'ILS'),
            'currency_symbol' => Setting::get('currency_symbol', '₪'),
            'tax_rate' => Setting::get('tax_rate', 0),
            'shipping_cost' => Setting::get('shipping_cost', 50),
            'free_shipping_threshold' => Setting::get('free_shipping_threshold', 500),
            'cod_enabled' => Setting::get('cod_enabled', true),
            'stripe_enabled' => Setting::get('stripe_enabled', false),
            'stripe_publishable_key' => Setting::get('stripe_publishable_key'),
            'stripe_secret_key' => Setting::get('stripe_secret_key'),
            'paypal_enabled' => Setting::get('paypal_enabled', false),
            'paypal_client_id' => Setting::get('paypal_client_id'),
            'paypal_secret' => Setting::get('paypal_secret'),
            'paypal_mode' => Setting::get('paypal_mode', 'sandbox'),
            'order_confirmation_email' => Setting::get('order_confirmation_email', true),
            'order_shipped_email' => Setting::get('order_shipped_email', true),
            'order_delivered_email' => Setting::get('order_delivered_email', true),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('معلومات المتجر')
                    ->schema([
                        TextInput::make('store_name')
                            ->label('اسم المتجر')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('store_email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required(),

                        TextInput::make('store_phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->required(),

                        Textarea::make('store_address')
                            ->label('العنوان')
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::make('store_logo')
                            ->label('شعار المتجر')
                            ->image()
                            ->directory('store')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('إعدادات العملة والأسعار')
                    ->schema([
                        Select::make('currency')
                            ->label('العملة')
                            ->options([
                                'ILS' => 'شيكل إسرائيلي جديد (₪)',
                                'USD' => 'دولار أمريكي ($)',
                                'EUR' => 'يورو (€)',
                                'GBP' => 'جنيه إسترليني (£)',
                                'SYP' => 'ليرة سورية (ل.س)',
                                'JOD' => 'دينار أردني (د.أ)',
                                'EGP' => 'جنيه مصري (ج.م)',
                                'SAR' => 'ريال سعودي (ر.س)',
                                'AED' => 'درهم إماراتي (د.إ)',
                            ])
                            ->default('ILS')
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $symbols = [
                                    'ILS' => '₪',
                                    'USD' => '$',
                                    'EUR' => '€',
                                    'GBP' => '£',
                                    'SYP' => 'ل.س',
                                    'JOD' => 'د.أ',
                                    'EGP' => 'ج.م',
                                    'SAR' => 'ر.س',
                                    'AED' => 'د.إ',
                                ];
                                $set('currency_symbol', $symbols[$state] ?? '');
                            }),

                        TextInput::make('currency_symbol')
                            ->label('رمز العملة')
                            ->required()
                            ->maxLength(10),

                        TextInput::make('tax_rate')
                            ->label('نسبة الضريبة (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->suffix('%'),

                        TextInput::make('shipping_cost')
                            ->label('تكلفة الشحن')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->suffix(fn (Get $get) => $get('currency_symbol') ?? '₪'),

                        TextInput::make('free_shipping_threshold')
                            ->label('الحد الأدنى للشحن المجاني')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('اتركه 0 لتعطيل الشحن المجاني')
                            ->suffix(fn (Get $get) => $get('currency_symbol') ?? '₪'),
                    ])->columns(2),

                Section::make('طرق الدفع')
                    ->description('قم بتفعيل وإعداد بوابات الدفع المطلوبة')
                    ->schema([
                        Toggle::make('cod_enabled')
                            ->label('الدفع عند الاستلام')
                            ->default(true)
                            ->helperText('تفعيل الدفع عند الاستلام')
                            ->columnSpanFull(),

                        Toggle::make('stripe_enabled')
                            ->label('تفعيل Stripe')
                            ->default(false)
                            ->live()
                            ->columnSpanFull(),

                        TextInput::make('stripe_publishable_key')
                            ->label('Stripe Publishable Key')
                            ->maxLength(255)
                            ->visible(fn (Get $get) => $get('stripe_enabled'))
                            ->required(fn (Get $get) => $get('stripe_enabled'))
                            ->helperText('مفتاح Stripe العام (pk_...)'),

                        TextInput::make('stripe_secret_key')
                            ->label('Stripe Secret Key')
                            ->password()
                            ->maxLength(255)
                            ->visible(fn (Get $get) => $get('stripe_enabled'))
                            ->required(fn (Get $get) => $get('stripe_enabled'))
                            ->helperText('مفتاح Stripe السري (sk_...)'),

                        Toggle::make('paypal_enabled')
                            ->label('تفعيل PayPal')
                            ->default(false)
                            ->live()
                            ->columnSpanFull(),

                        TextInput::make('paypal_client_id')
                            ->label('PayPal Client ID')
                            ->maxLength(255)
                            ->visible(fn (Get $get) => $get('paypal_enabled'))
                            ->required(fn (Get $get) => $get('paypal_enabled'))
                            ->helperText('معرف عميل PayPal'),

                        TextInput::make('paypal_secret')
                            ->label('PayPal Secret')
                            ->password()
                            ->maxLength(255)
                            ->visible(fn (Get $get) => $get('paypal_enabled'))
                            ->required(fn (Get $get) => $get('paypal_enabled'))
                            ->helperText('المفتاح السري لـ PayPal'),

                        Select::make('paypal_mode')
                            ->label('PayPal Mode')
                            ->options([
                                'sandbox' => 'Sandbox (تجريبي)',
                                'live' => 'Live (مباشر)',
                            ])
                            ->default('sandbox')
                            ->visible(fn (Get $get) => $get('paypal_enabled'))
                            ->required(fn (Get $get) => $get('paypal_enabled'))
                            ->native(false),
                    ])->columns(2),

                Section::make('إعدادات البريد الإلكتروني')
                    ->schema([
                        Toggle::make('order_confirmation_email')
                            ->label('إرسال بريد تأكيد الطلب')
                            ->default(true),

                        Toggle::make('order_shipped_email')
                            ->label('إرسال بريد عند الشحن')
                            ->default(true),

                        Toggle::make('order_delivered_email')
                            ->label('إرسال بريد عند التوصيل')
                            ->default(true),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // حفظ جميع الإعدادات في قاعدة البيانات
        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->body('تم تحديث جميع إعدادات المتجر')
            ->success()
            ->send();

        // إعادة تحميل الصفحة لتطبيق التغييرات
        redirect()->route('filament.admin.pages.settings');
    }
}
