<nav class="navbar navbar-light bg-white px-4 border-bottom" style="z-index: 1030; position: sticky; top: 0; left: 0; width: 100%;">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <h5 class="mb-0 fw-bold">Plants Management</h5>
        </div>
        <div class="d-flex align-items-center">
            <span class="me-3">🔔</span>
            <div class="d-flex align-items-center">
                <img src="https://i.pravatar.cc/30" class="rounded-circle me-2" alt="User" width="30">
                <span>Admin User</span>
                <form action="{{ route('logout') }}" method="POST" class="ms-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</nav>
