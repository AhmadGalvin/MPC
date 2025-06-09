@extends('layouts.admin')

@section('title', 'Products')
@section('header', 'Products')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Products List</h2>
        <button onclick="openModal()"
                class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Add Product
        </button>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="aspect-w-16 aspect-h-9">
                <img src="{{ asset('storage/' . $product->image_path) }}" 
                     alt="{{ $product->name }}"
                     class="w-full h-48 object-cover">
            </div>
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                    <span class="px-2 py-1 text-sm {{ $product->stock > 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full">
                        Stock: {{ $product->stock }}
                    </span>
                </div>
                <p class="text-gray-600 text-sm mb-2">{{ Str::limit($product->description, 100) }}</p>
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    <div class="space-x-2">
                        <button onclick="editProduct({{ $product->id }})"
                                class="text-primary hover:text-primary-dark">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteProduct({{ $product->id }})"
                                class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal -->
<div id="productModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modalTitle">Add Product</h3>
            <form id="productForm" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="product_id" id="productId">

                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Product Image</label>
                    <input type="file" 
                           name="image" 
                           id="productImage"
                           accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" 
                           name="name" 
                           id="productName"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" 
                              id="productDescription"
                              rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"></textarea>
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Price (Rp)</label>
                    <input type="number" 
                           name="price" 
                           id="productPrice"
                           required
                           min="0"
                           step="1000"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <!-- Stock -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Stock</label>
                    <input type="number" 
                           name="stock" 
                           id="productStock"
                           required
                           min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button"
                            onclick="closeModal()"
                            class="px-4 py-2 border text-gray-700 rounded-md hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">
                        <span id="submitButtonText">Add Product</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal() {
        document.getElementById('modalTitle').textContent = 'Add Product';
        document.getElementById('submitButtonText').textContent = 'Add Product';
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = '';
        document.getElementById('productModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('productModal').classList.add('hidden');
    }

    function editProduct(id) {
        fetch(`/admin/products/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('productId').value = data.id;
                document.getElementById('productName').value = data.name;
                document.getElementById('productDescription').value = data.description;
                document.getElementById('productPrice').value = data.price;
                document.getElementById('productStock').value = data.stock;
                document.getElementById('modalTitle').textContent = 'Edit Product';
                document.getElementById('submitButtonText').textContent = 'Update Product';
                document.getElementById('productModal').classList.remove('hidden');
            });
    }

    function deleteProduct(id) {
        if (confirm('Are you sure you want to delete this product?')) {
            fetch(`/admin/products/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                }
            });
        }
    }

    document.getElementById('productForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const productId = formData.get('product_id');
        const method = productId ? 'PUT' : 'POST';
        const url = productId ? `/admin/products/${productId}` : '/admin/products';

        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.reload();
            }
        });
    });
</script>
@endpush 