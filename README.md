# ElectroMart

**ElectroMart** is an e-commerce web application built using **Laravel** and powered by **Filament Admin Panel**. The platform allows users to browse and purchase electronic products, while providing administrators with powerful tools to manage products, orders, users and more.

---

## Features

### Customer Side
- **User Authentication**: Register, login, logout
- **Product Browsing**: View products by category, brand and filters (on sale, price range)
- **Product Details**: Dynamic pages with complete product information
- **Shopping  Cart**:
    - Add/remove items
    - Update quantity
    - Real-time price calculation
- **Checkout Process**: Submit orders with cart data
- **Order Placement**:
    - Store order and related items in database
    - Success page after placement
    - Email confirmation
- **Order History**: View user-specific orders

---

### Admin Panel (Powered by Filament)
- **Role-based Access**:
    - Admin access restircted via Spatie Roles & Permissions
- **Dashboard Widgets**:
    - Order statistics widget for tracking status (e.g. pending, shipped)
- **User Management**: View and manage user accounts
- **Product Management**:
    - CRUD operations for products, brands, categories
    - Define attributes like `is_featured`, `on_sale`, price, etc.
- **Order Management**:
    - Tabs to filter orders by status
- **Address Management**:
    - Assign addresses to users with relationships
- **Dynamic UI Updates**:
    - Used Livewire and `wire:navigate` for UI state updates without full reload
 
---

## Tech Stack

- **Framwork**: Laravel
- **Admin Panel**: Filament
- **Frontend Styling**: Tailwind CSS, Preline UI
- **Interactivity**: Livewire
- **Role Management**: Spatie Laravel-Permission

---

## Getting Started

### Prerequisites
- PHP
- Composer
- MySQl
- Node.js & npm (for frontend assets)

### Installation
```bash
git clone https://github.com/Hagar-Elbakry/ElectroMart.git
cd electromart
composer install
npm install && npm run dev
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve
```
**Make Sure** to set up your `.env` file with correct database and mail configurations.
