@extends('layouts.app')

@section('title', 'Plants Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Plants Management</h3>
        <a href="#" class="btn btn-orange">+ Add New Plant</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Plant</th>
                        <th>Name</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>QR Code</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#PL001</td>
                        <td><img src="/images/plants/monstera.jpg" width="50" class="rounded"></td>
                        <td>Monstera Deliciosa</td>
                        <td>15</td>
                        <td>$49.99</td>
                        <td><img src="/qr/PL001.png" width="50"></td>
                        <td>
                            <a href="#" class="text-primary me-2">✏️</a>
                            <a href="#" class="text-danger">🗑️</a>
                        </td>
                    </tr>
                    <!-- more rows -->
                </tbody>
            </table>
        </div>
    </div>
@endsection
