@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold">Products</h1>
                    @role('clinic_admin')
                    <a href="{{ route('products.create') }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add New Product
                    </a>
                    @endrole
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($products as $product)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                    class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">No Image</span>
                                </div>
                            @endif

                            <div class="p-4">
                                <h2 class="text-xl font-semibold mb-2">{{ $product->name }}</h2>
                                <p class="text-gray-600 mb-4">{{ Str::limit($product->description, 100) }}</p>
                                
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-lg font-bold text-blue-600">${{ number_format($product->price, 2) }}</span>
                                    <span class="text-sm text-gray-500">Stock: {{ $product->stock }}</span>
                                </div>

                                @role('clinic_admin')
                                <div class="flex justify-between items-center">
                                    <a href="{{ route('products.edit', $product) }}"
                                        class="text-blue-600 hover:text-blue-800">
                                        Edit
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                                @endrole
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3">
                            <p class="text-gray-600 text-center">No products available.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 