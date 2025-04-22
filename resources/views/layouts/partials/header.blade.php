<nav class="navbar navbar-light bg-white px-4 border-bottom" style="z-index: 1030; position: sticky; top: 0; left: 0; width: 100%;">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-secondary d-md-none me-3" id="toggleSidebar">
                ☰
            </button>
            <h5 class="mb-0 fw-bold">Plants Management</h5>
        </div>
        <div class="d-flex align-items-center">
            <span class="me-3">🔔</span>
            <div class="dropdown">
                <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown">
                    <img src="https://i.pravatar.cc/30" class="rounded-circle" alt="User" width="30"> Admin User
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Profil</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">Keluar</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<style>
    #toggleSidebar {
        display: none;
    }

    @media (max-width: 768px) {
        #toggleSidebar {
            display: inline-block;
        }
    }
</style>
