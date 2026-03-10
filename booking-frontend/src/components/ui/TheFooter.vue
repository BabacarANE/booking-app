<script setup>
</script>
<template>
  <footer class="bg-white border-t border-gray-100 mt-auto">
    <div class="max-w-7xl mx-auto px-4 py-8">
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Logo -->
        <RouterLink to="/" class="flex items-center gap-2 group">
          <img
            src="/favicon.ico"
            alt="BookingApp"
            class="w-9 h-9 rounded-xl group-hover:scale-105 transition-transform"
          />
          <span class="font-bold text-xl text-gray-900">Booking<span class="text-coral-500">App</span></span>
        </RouterLink>
        <!-- Copyright -->
        <p class="text-gray-400 text-sm">© 2026 BookingApp — Tous droits réservés</p>
        <!-- Links -->
        <div class="flex items-center gap-4 text-sm text-gray-400">
          <a href="#" class="hover:text-coral-500 transition-colors">Mentions légales</a>
          <a href="#" class="hover:text-coral-500 transition-colors">Confidentialité</a>
          <a href="#" class="hover:text-coral-500 transition-colors">Contact</a>
        </div>
      </div>
    </div>
  </footer>
</template>
