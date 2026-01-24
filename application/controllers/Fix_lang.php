<?php
class Fix_lang extends CI_Controller {
    public function index() {
        $dir = APPPATH.'language/arabic';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        
        $lang_ar = [
            'direction' => 'rtl',
            'menu_dashboard' => 'لوحة التحكم',
            'menu_products' => 'المنتجات',
            'menu_categories' => 'الفئات',
            'menu_purchases' => 'المشتريات',
            'menu_reports' => 'التقارير',
            'btn_save' => 'حفظ',
            'btn_signin' => 'دخول',
            'email_ph' => 'البريد الإلكتروني',
            'password_ph' => 'كلمة المرور',
            'remember_me' => 'تذكرني',
            'login_title' => 'تسجيل الدخول'
        ];
        
        file_put_contents($dir.'/app_lang.php', 
            "<?php defined('BASEPATH') OR exit('No direct script access allowed');\nreturn " . var_export($lang_ar, true) . ";");
        
        echo "✅ arabic/app_lang.php CRÉÉ ! <a href='/'>Login</a>";
    }
}
