# FastFood Ordering System

A full-stack food ordering web application built with Laravel, featuring OTP-based authentication, a shopping cart, coupon discounts, and real payment gateway integration.

## Features

- **OTP Authentication** – Passwordless login/signup via one-time codes sent to the user's phone number
- **Product Catalog** – Browse categories and products with images, pricing, and sale/discount support
- **Shopping Cart** – Add, update, and remove items before checkout
- **Coupon System** – Apply discount codes with expiration handling
- **Wishlist** – Save favorite products for later
- **User Addresses** – Manage multiple delivery addresses per user (with province/city support)
- **Order Management** – Full order lifecycle from cart to confirmed purchase
- **Payment Integration** – Real payment gateway integration via [Zibal](https://zibal.ir)
- **Admin Panel** – A separate companion project for managing products, categories, orders, coupons, and site content (sliders, footer, about/contact pages)

## Tech Stack

- **Backend:** PHP, Laravel (MVC architecture)
- **Database:** MySQL
- **Templating:** Blade
- **Payment Gateway:** Zibal
- **Tools:** Composer, Git

## Installation

1. Clone the repository
   ```bash
   git clone https://github.com/shabani17/laravel-fastfood.git
   cd laravel-fastfood
   ```

2. Install dependencies
   ```bash
   composer install
   ```

3. Set up environment file
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure your database credentials in `.env`
   ```
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. Run migrations
   ```bash
   php artisan migrate
   ```

6. Serve the application
   ```bash
   php artisan serve
   ```

7. Visit `http://127.0.0.1:8000` in your browser

## Project Structure

- `app/Http/Controllers` – Application logic (Auth, Cart, Order, Payment, Product, Profile)
- `app/Models` – Eloquent models (Product, Category, Order, Coupon, Transaction, etc.)
- `database/migrations` – Database schema definitions
- `resources/views` – Blade templates

## Related Project

The admin panel for this system lives in a separate repository and shares the same database schema, allowing site administrators to manage products, orders, coupons, and content.

## Roadmap

- [ ] Automated tests for cart and checkout flow
- [ ] Order status notifications (SMS/email)
- [ ] Admin dashboard analytics

## License

This project is open for educational and portfolio purposes.