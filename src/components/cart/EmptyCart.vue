<template>
    <div class="text-center py-12">
        <font-awesome-icon icon="shopping-cart" size="4x" class="text-gray-400 mb-4" />
        <h2 class="text-2xl font-semibold mb-2">Your cart is empty</h2>
        <p class="text-gray-600 mb-6">Looks like you haven't added anything to your cart yet</p>
        <RouterLink to="/shop"
            class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded hover:bg-opacity-90 transition">
            <font-awesome-icon icon="shopping-cart" />
            <span>Start Shopping</span>
        </RouterLink>
    </div>
</template>