# Laravel 代碼重構：使用Services, Events, Jobs, Actions 來重構控制器方法

中文：https://learnku.com/laravel/t/68751  
英文：https://laravel-news.com/controller-refactor

## 準備工作
composer require laravel/breeze --dev  
php artisan breeze:install  
php artisan migrate  

php artisan make:migration add_google_auth_to_users_table
php migrate

# 創建 Voucher Model
php artisan make:model Voucher -m

# 創建通知
php artisan make:notification NewUserWelcomeNotification
php artisan make:notification NewUserAdminNotification

# 本地開發測試郵件服務 MailHog
https://github.com/mailhog/MailHog
Mac安裝 brew update && brew install mailhog
啟動 mailhog，允許連線可以設定無
Web UI : http://0.0.0.0:8025/
.env 修改 MAIL_HOST=127.0.0.1

# 創建隊列
php artisan make:job NewUserNotifyAdminsJob

# Queue
參考：https://ithelp.ithome.com.tw/articles/10281596
.env QUEUE_CONNECTION=database (default sync 沒有隊列，及時執行工作的意思，適合在開發環境用。)
php artisan queue:table  
php artisan migrate  
php artisan queue:listen
