# استخدام صورة PHP مع Apache
FROM php:8.1-apache

# نسخ ملفات المشروع إلى المسار الافتراضي لـ Apache
COPY . /var/www/html

# فتح المنفذ 80
EXPOSE 80

# إعداد نقطة البداية
CMD ["apache2-foreground"]
