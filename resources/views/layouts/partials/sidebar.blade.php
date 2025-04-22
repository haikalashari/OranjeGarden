<div class="bg-white border-end p-3 vh-100" style="width: 240px;">
    <h4 class="text-orange fw-bold mb-4">Oranje Garden</h4>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->is('dashboard') ? 'active bg-orange text-white' : '' }}" href="{{ route('dashboard.index') }}">
                🏠 Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('dashboard/plant') ? 'active bg-orange text-white' : '' }}" href="{{ route('dashboard.kelola.plant') }}">
                🪴 Plants
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('dashboard/orders') ? 'active bg-orange text-white' : '' }}" href="{{ route('dashboard.kelola.order') }}">
                📦 Orders
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('dashboard/deliveries') ? 'active bg-orange text-white' : '' }}" href="{{ route('dashboard.kelola.delivery') }}">
                🚚 Deliveries
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('dashboard/customers') ? 'active bg-orange text-white' : '' }}" href="{{ route('dashboard.kelola.customer') }}">
                👥 Customers
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                📊 Reports
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                ⚙️ Settings
            </a>
        </li>
    </ul>
</div>

<style>
    .text-orange {
        color: #ff6600;
    }
    .bg-orange {
        background-color: #ff6600 !important;
    }
    .nav-link {
        color: #333;
        font-weight: 500;
        padding: 10px 15px;
        border-radius: 5px;
    }
    .nav-link:hover {
        background-color: #ffe6cc;
        color: #ff6600;
    }
    .nav-link.active {
        font-weight: bold;
    }
</style>
