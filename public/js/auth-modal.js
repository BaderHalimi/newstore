// نظام تسجيل الدخول بالـ Popup
class AuthModal {
    constructor() {
        this.modal = null;
        this.currentView = 'login'; // login, register, passwordLogin, codeInput
        this.init();
    }

    init() {
        this.createModal();
        this.attachEventListeners();
    }

    createModal() {
        const modalHTML = `
            <div id="authModal" class="hidden fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 transform transition-all">
                        <!-- زر الإغلاق -->
                        <button onclick="authModal.close()" class="absolute top-4 left-4 text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        <!-- المحتوى -->
                        <div id="authModalContent"></div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.getElementById('authModal');
    }

    open(view = 'login') {
        this.currentView = view;
        this.render();
        this.modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    close() {
        this.modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    render() {
        const content = document.getElementById('authModalContent');

        switch(this.currentView) {
            case 'login':
                content.innerHTML = this.getLoginView();
                break;
            case 'register':
                content.innerHTML = this.getRegisterView();
                break;
            case 'passwordLogin':
                content.innerHTML = this.getPasswordLoginView();
                break;
            case 'codeInput':
                content.innerHTML = this.getCodeInputView();
                break;
            case 'verifyAccount':
                content.innerHTML = this.getVerifyAccountView();
                break;
        }

        this.attachFormListeners();
    }

    getLoginView() {
        return `
            <div class="text-center mb-8">
                <div class="inline-block p-3 bg-purple-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">مرحباً بك</h2>
                <p class="text-gray-600">قم بتسجيل الدخول للمتابعة</p>
            </div>

            <form id="loginForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="example@email.com">
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-purple-800 text-white py-3 rounded-lg font-medium hover:from-purple-700 hover:to-purple-900 transition">
                    إرسال رمز التحقق
                </button>
            </form>

            <div class="mt-6">
                <button onclick="authModal.showView('passwordLogin')" class="w-full text-purple-600 py-2 rounded-lg border border-purple-600 hover:bg-purple-50 transition">
                    خيارات أخرى (كلمة المرور)
                </button>
            </div>

            <div class="mt-6 text-center">
                <p class="text-gray-600">ليس لديك حساب؟
                    <button onclick="authModal.showView('register')" class="text-purple-600 font-medium hover:underline">
                        سجّل الآن
                    </button>
                </p>
            </div>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">أو</span>
                    </div>
                </div>

                <button onclick="window.location.href='/auth/google'" class="mt-4 w-full flex items-center justify-center gap-2 bg-white border border-gray-300 py-3 rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    تسجيل الدخول بواسطة Google
                </button>
            </div>
        `;
    }

    getRegisterView() {
        return `
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">إنشاء حساب جديد</h2>
                <p class="text-gray-600">انضم إلينا الآن</p>
            </div>

            <form id="registerForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل</label>
                    <input type="text" name="name" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="أدخل اسمك الكامل">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="example@email.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف (اختياري)</label>
                    <input type="tel" name="phone"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="+972 123 456 789">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="أدخل كلمة مرور قوية">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="أعد إدخال كلمة المرور">
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-purple-800 text-white py-3 rounded-lg font-medium hover:from-purple-700 hover:to-purple-900 transition">
                    إنشاء حساب
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">لديك حساب بالفعل؟
                    <button onclick="authModal.showView('login')" class="text-purple-600 font-medium hover:underline">
                        تسجيل الدخول
                    </button>
                </p>
            </div>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">أو</span>
                    </div>
                </div>

                <button onclick="window.location.href='/auth/google'" class="mt-4 w-full flex items-center justify-center gap-2 bg-white border border-gray-300 py-3 rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    سجل بواسطة Google
                </button>
            </div>
        `;
    }

    getPasswordLoginView() {
        return `
            <div class="text-center mb-8">
                <button onclick="authModal.showView('login')" class="text-purple-600 flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    العودة
                </button>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">تسجيل الدخول بكلمة المرور</h2>
            </div>

            <form id="passwordLoginForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="example@email.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="أدخل كلمة المرور">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <label for="remember" class="mr-2 text-sm text-gray-700">تذكرني</label>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-purple-800 text-white py-3 rounded-lg font-medium hover:from-purple-700 hover:to-purple-900 transition">
                    تسجيل الدخول
                </button>
            </form>
        `;
    }

    getCodeInputView() {
        return `
            <div class="text-center mb-8">
                <div class="inline-block p-3 bg-green-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">تحقق من بريدك</h2>
                <p class="text-gray-600">أدخل الرمز المكون من 6 أرقام</p>
            </div>

            <form id="codeInputForm" class="space-y-4">
                <div>
                    <input type="text" name="code" required maxlength="6" pattern="[0-9]{6}"
                        class="w-full px-4 py-3 text-center text-2xl tracking-widest border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="000000">
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-purple-800 text-white py-3 rounded-lg font-medium hover:from-purple-700 hover:to-purple-900 transition">
                    تأكيد
                </button>
            </form>

            <div class="mt-6 text-center">
                <button onclick="authModal.resendCode()" class="text-purple-600 hover:underline">
                    إعادة إرسال الرمز
                </button>
            </div>
        `;
    }

    getVerifyAccountView() {
        return `
            <div class="text-center mb-8">
                <div class="inline-block p-3 bg-blue-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">تفعيل الحساب</h2>
                <p class="text-gray-600">تم إرسال رمز التفعيل إلى بريدك الإلكتروني</p>
                <p class="text-sm text-gray-500 mt-2">${this.email || ''}</p>
            </div>

            <form id="verifyAccountForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رمز التفعيل (OTP)</label>
                    <input type="text" name="otp" required maxlength="6" pattern="[0-9]{6}"
                        class="w-full px-4 py-3 text-center text-2xl tracking-widest border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="000000">
                    <p class="text-xs text-gray-500 mt-2">الرمز صالح لمدة 15 دقيقة</p>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white py-3 rounded-lg font-medium hover:from-blue-700 hover:to-blue-900 transition">
                    تفعيل الحساب
                </button>
            </form>

            <div class="mt-6 text-center">
                <button onclick="authModal.resendVerification()" class="text-blue-600 hover:underline">
                    إعادة إرسال رمز التفعيل
                </button>
            </div>
        `;
    }

    attachFormListeners() {
        // تسجيل الدخول - إرسال الرمز
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => this.handleLoginSubmit(e));
        }

        // التسجيل
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', (e) => this.handleRegisterSubmit(e));
        }

        // تسجيل الدخول بكلمة المرور
        const passwordLoginForm = document.getElementById('passwordLoginForm');
        if (passwordLoginForm) {
            passwordLoginForm.addEventListener('submit', (e) => this.handlePasswordLoginSubmit(e));
        }

        // إدخال الرمز
        const codeInputForm = document.getElementById('codeInputForm');
        if (codeInputForm) {
            codeInputForm.addEventListener('submit', (e) => this.handleCodeSubmit(e));
        }

        // تفعيل الحساب
        const verifyAccountForm = document.getElementById('verifyAccountForm');
        if (verifyAccountForm) {
            verifyAccountForm.addEventListener('submit', (e) => this.handleVerifyAccountSubmit(e));
        }
    }

    async handleLoginSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const email = formData.get('email');

        this.email = email; // حفظ البريد للاستخدام لاحقاً

        try {
            const response = await fetch('/auth/send-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email })
            });

            const data = await response.json();

            if (data.success) {
                this.showView('codeInput');
                this.showMessage(data.message, 'success');
            } else {
                this.showMessage(data.message, 'error');
            }
        } catch (error) {
            this.showMessage('حدث خطأ. يرجى المحاولة مرة أخرى', 'error');
        }
    }

    async handleRegisterSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        try {
            const response = await fetch('/auth/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                // حفظ البريد للتحقق
                this.email = data.email;

                // إذا كان الحساب يحتاج تحقق، عرض شاشة التحقق
                if (result.requiresVerification) {
                    this.showView('verifyAccount');
                    this.showMessage(result.message, 'success');
                } else {
                    // تسجيل دخول تلقائي
                    this.showMessage(result.message, 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                }
            } else {
                this.showMessage(result.message, 'error');
            }
        } catch (error) {
            this.showMessage('حدث خطأ. يرجى المحاولة مرة أخرى', 'error');
        }
    }

    async handlePasswordLoginSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        try {
            const response = await fetch('/auth/login-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showMessage(result.message, 'success');
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 1000);
            } else {
                this.showMessage(result.message, 'error');
            }
        } catch (error) {
            this.showMessage('حدث خطأ. يرجى المحاولة مرة أخرى', 'error');
        }
    }

    async handleCodeSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const code = formData.get('code');

        try {
            const response = await fetch('/auth/login-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    email: this.email,
                    code
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showMessage(result.message, 'success');
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 1000);
            } else {
                this.showMessage(result.message, 'error');
            }
        } catch (error) {
            this.showMessage('حدث خطأ. يرجى المحاولة مرة أخرى', 'error');
        }
    }

    async handleVerifyAccountSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const otp = formData.get('otp');

        try {
            const response = await fetch('/auth/verify-account', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    email: this.email,
                    otp
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showMessage(result.message, 'success');
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 1000);
            } else {
                this.showMessage(result.message, 'error');
            }
        } catch (error) {
            this.showMessage('حدث خطأ. يرجى المحاولة مرة أخرى', 'error');
        }
    }

    async resendCode() {
        if (!this.email) return;

        try {
            const response = await fetch('/auth/send-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email: this.email })
            });

            const data = await response.json();
            this.showMessage(data.message, data.success ? 'success' : 'error');
        } catch (error) {
            this.showMessage('حدث خطأ في إعادة الإرسال', 'error');
        }
    }

    async resendVerification() {
        if (!this.email) return;

        try {
            const response = await fetch('/auth/resend-verification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email: this.email })
            });

            const data = await response.json();
            this.showMessage(data.message, data.success ? 'success' : 'error');
        } catch (error) {
            this.showMessage('حدث خطأ في إعادة الإرسال', 'error');
        }
    }

    showView(view) {
        this.currentView = view;
        this.render();
    }

    showMessage(message, type = 'info') {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500'
        };

        const toast = document.createElement('div');
        toast.className = `fixed top-4 left-1/2 transform -translate-x-1/2 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity`;
        toast.textContent = message;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    attachEventListeners() {
        // إضافة event listeners للأزرار في الصفحة
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-auth-action]')) {
                const action = e.target.dataset.authAction;
                this.open(action);
            }
        });
    }
}

// تهيئة النظام
let authModal;
document.addEventListener('DOMContentLoaded', () => {
    authModal = new AuthModal();
    // console.log('✓ Auth Modal initialized successfully');
    // console.log('✓ CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content);
});
