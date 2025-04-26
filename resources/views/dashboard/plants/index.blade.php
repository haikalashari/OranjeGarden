@extends('layouts.app')

@section('title')
    Dashboard | OranjeGarden
@endsection

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-orange-600">Plants Management</h1>
    </div>

    <div class="flex flex-col md:flex-row items-start md:items-center justify-between pb-4 gap-3">
        <label for="table-search" class="sr-only">Search</label>
        <div class="relative w-full md:w-auto">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8 4a4 4 0 1 1 0 8 4 4 0 0 1 0-8ZM2 8a6 6 0 1 1 11.293 3.707l4 4a1 1 0 0 1-1.414 1.414l-4-4A6 6 0 0 1 2 8Z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <input type="text" id="table-search" class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for products">
        </div>

        <button onclick="openAddModal()" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 md:w-auto text-center">+ Add Plant</button>
    </div>

    <div class="relative overflow-hidden rounded-lg shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                <thead class="text-xs text-white uppercase bg-orange-500 dark:bg-orange-600">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-semibold">Tanaman</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Photo</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Stock</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Harga</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800">
                    @foreach ($plants as $plant)
                    <tr class="border-b last:rounded-b-lg last:border-none hover:bg-orange-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $plant->name }}</td>
                        <td class="px-6 py-4">
                            <img src="{{ asset('storage/' . $plant->photo) }}" alt="{{ $plant->name }}" class="h-20 w-20 object-cover rounded-lg shadow-sm">
                        </td>
                        <td class="px-6 py-4">{{ $plant->stock }}</td>
                        <td class="px-6 py-4 font-semibold text-orange-600 dark:text-orange-400">Rp {{ number_format($plant->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 space-y-2 md:space-y-0 md:space-x-2 flex flex-col md:flex-row">
                        <a href="#" onclick="openQRModal('{{ $plant->id }}', '{{ $plant->name }}', '{{ asset('storage/' . $plant->qr_code) }}')" class="inline-block px-3 py-1 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-300 focus:outline-none text-center">
                                QR
                            </a>                        
                            <a href="#" onclick="openEditModal({{ $plant->id }}, '{{ $plant->name }}', '{{ $plant->photo }}', {{ $plant->stock }}, {{ $plant->price }})" class="inline-block px-3 py-1 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-300 focus:outline-none text-center">Edit</a>
                            <form action="{{ route('dashboard.kelola.plant.hapus', $plant->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menghapus tanaman ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 focus:ring-2 focus:ring-red-300 focus:outline-none text-center">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach

                    @if ($plants->isEmpty())
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">No plants found.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Add Plant Modal -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden px-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-orange-600 mb-4">Add New Plant</h2>
        <form id="addForm" action="{{ route('dashboard.kelola.plant.tambah') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Plant Name</label>
                <input type="text" name="name" id="name" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
                <input type="file" name="photo" id="photo" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                <input type="number" name="stock" id="stock" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                <input type="number" name="price" id="price" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg">Add</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Plant Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden px-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-orange-600 mb-4">Edit Plant</h2>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="edit-name" class="block text-sm font-medium text-gray-700">Plant Name</label>
                <input type="text" name="name" id="edit-name" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="edit-photo" class="block text-sm font-medium text-gray-700">Photo</label>
                <input type="file" name="photo" id="edit-photo" class="block w-full mt-1 p-2 border rounded-lg">
                <img id="edit-photo-preview" src="" class="h-12 w-12 object-cover rounded-lg shadow-sm mt-2 hidden">
            </div>
            <div class="mb-4">
                <label for="edit-stock" class="block text-sm font-medium text-gray-700">Stock</label>
                <input type="number" name="stock" id="edit-stock" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label for="edit-price" class="block text-sm font-medium text-gray-700">Price</label>
                <input type="number" name="price" id="edit-price" class="block w-full mt-1 p-2 border rounded-lg">
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="qrCodeModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden px-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md text-center">
        <h2 class="text-2xl font-bold text-orange-600 mb-4">Plant QR Code</h2>

        <!-- QR Code Display -->
        <div id="qr-code-inner" class="flex justify-center mb-6">
            <!-- QR will be injected here dynamically -->
        </div>

        <!-- Plant Info -->
        <div class="mb-6 space-y-2">
            <p class="text-sm"><span class="font-medium">Plant ID:</span> <span id="qr-plant-id"></span></p>
            <p class="text-sm"><span class="font-medium">Name:</span> <span id="qr-plant-name"></span></p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between">
            <button onclick="printQRCode()" class="px-4 py-2 bg-blue-500 text-white rounded-lg flex items-center gap-2">
                <!-- Print Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
            
            <button onclick="downloadQRCode()" class="px-4 py-2 bg-green-500 text-white rounded-lg flex items-center gap-2">
                <!-- Download Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download
            </button>
            
            <button onclick="closeQRModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg">
                Close
            </button>
        </div>
    </div>
</div>



@endsection

@section('scripts')
<script>
    // Function to open the add plant modal
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }

    // Function to close the add plant modal
    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    // Function to open the edit plant modal with pre-filled data
    function openEditModal(id, name, photo, stock, price) {
        const editModal = document.getElementById('editModal');
        const editForm = document.getElementById('editForm');
        const editName = document.getElementById('edit-name');
        const editStock = document.getElementById('edit-stock');
        const editPrice = document.getElementById('edit-price');
        const editPhotoPreview = document.getElementById('edit-photo-preview');

        // Set form values
        editName.value = name;
        editStock.value = stock;
        editPrice.value = price;
        
        // Set the form action with the correct URL and ID
        editForm.action = `/dashboard/plant/${id}`;

        // Handle photo preview
        if (photo) {
            editPhotoPreview.src = `/storage/${photo}`;
            editPhotoPreview.classList.remove('hidden');
        } else {
            editPhotoPreview.classList.add('hidden');
        }

        // Show modal
        editModal.classList.remove('hidden');
    }

    // Function to close the edit plant modal
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Handle photo preview when editing
    document.getElementById('edit-photo').addEventListener('change', function (event) {
        const file = event.target.files[0];
        const preview = document.getElementById('edit-photo-preview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
        }
    });

    function openQRModal(plantId, plantName, qrCodeUrl) {
        document.getElementById('qrCodeModal').classList.remove('hidden');

        // Update the plant info
        document.getElementById('qr-plant-id').textContent = plantId;
        document.getElementById('qr-plant-name').textContent = plantName;

        // Fetch the QR Code file dynamically
        fetch(qrCodeUrl)
            .then(response => response.text())
            .then(svg => {
                document.getElementById('qr-code-inner').innerHTML = svg;
            });
    }

    function closeQRModal() {
        document.getElementById('qrCodeModal').classList.add('hidden');
    }

    function printQRCode() {
        const printWindow = window.open('', '_blank');
        const qrContent = document.getElementById('qr-code-inner').innerHTML;
        const plantName = document.getElementById('qr-plant-name').textContent;
        const plantId = document.getElementById('qr-plant-id').textContent;

        printWindow.document.write(`
            <html>
                <head><title>Print QR Code</title></head>
                <body style="text-align: center; padding-top: 50px;">
                    ${qrContent}
                    <h2>${plantName} (ID: ${plantId})</h2>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }

    function downloadQRCode() {
        const svgElement = document.getElementById('qr-code-inner').querySelector('svg');
        if (!svgElement) {
            alert('QR code not found!');
            return;
        }

        const svgData = new XMLSerializer().serializeToString(svgElement);
        const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);

        const link = document.createElement('a');
        link.href = url;
        link.download = 'plant-qr-code.svg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
</script>
@endsection
