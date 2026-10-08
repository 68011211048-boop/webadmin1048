FROM php:8.1-apache

# ติดตั้ง Extension mysqli และ pdo_mysql สำหรับเชื่อมต่อฐานข้อมูล
RUN docker-php-ext-install mysqli pdo pdo_mysql

# คัดลอกไฟล์ทั้งหมดไปยัง Web Server
COPY . /var/www/html/

EXPOSE 80