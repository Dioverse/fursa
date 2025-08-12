<template>
    <div class="min-h-screen flex">
        <!-- Left Sidebar -->
        <div
            class="hidden lg:flex lg:w-2/5 bg-gradient-to-br from-yellow-400 via-yellow-500 to-yellow-600 p-12 items-center justify-center">
            <div class="text-white text-center max-w-md">
                <img src="/images/logo.png" alt="Fursa Energy" class="w-32 mx-auto mb-8 filter brightness-0 invert" />
                <slot name="sidebar">
                    <h1 class="text-4xl font-bold mb-4">Welcome to Fursa Energy</h1>
                    <p class="text-lg opacity-90">Your trusted partner for premium lubricants and energy solutions</p>
                </slot>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">
                <div class="lg:hidden mb-8 text-center">
                    <img src="/images/logo.png" alt="Fursa Energy" class="w-24 mx-auto" />
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup>
// Auth layout for login/register pages
</script>