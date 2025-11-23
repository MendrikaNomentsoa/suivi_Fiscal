<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 relative overflow-hidden">
    
    <!-- Decorative Elements -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
    <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>

    <!-- Content Container -->
    <div class="relative z-10 min-h-screen flex flex-col items-center justify-center px-4 py-12">
      
      <!-- Header Section -->
      <div class="text-center mb-20 max-w-4xl">
        <!-- Logo or Icon -->
        <div class="mb-6 inline-block">
          <div class="w-20 h-20 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg transform rotate-3">
            <svg class="w-12 h-12 text-white -rotate-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
        </div>
        
        <h1 class="text-5xl md:text-7xl font-bold text-slate-900 mb-6 tracking-tight">
          Gestion Fiscale
          <span class="block text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">
            Madagascar
          </span>
        </h1>
        
        <p class="text-lg md:text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
          Plateforme digitale pour la déclaration et le suivi de vos obligations fiscales en toute simplicité
        </p>
      </div>

      <!-- Cards Container -->
      <div class="grid md:grid-cols-2 gap-8 w-full max-w-5xl">

        <!-- Contribuable Card -->
        <div class="group relative bg-white/80 backdrop-blur-sm rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 border border-slate-200/50 hover:border-indigo-200">
          <!-- Accent Line -->
          <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-t-3xl transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
          
          <!-- Icon Container -->
          <div class="mb-6 relative">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <!-- Floating Badge -->
            <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
              <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>

          <!-- Content -->
          <h2 class="text-2xl font-bold text-slate-900 mb-3">
            Espace Contribuable
          </h2>
          
          <p class="text-slate-600 mb-8 leading-relaxed">
            Accédez à votre espace personnel pour déclarer vos impôts, consulter votre historique et gérer vos documents fiscaux
          </p>

          <!-- Features List -->
          <ul class="space-y-2 mb-8">
            <li class="flex items-center text-sm text-slate-600">
              <svg class="w-5 h-5 text-indigo-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              Déclarations en ligne
            </li>
            <li class="flex items-center text-sm text-slate-600">
              <svg class="w-5 h-5 text-indigo-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              Suivi en temps réel
            </li>
            <li class="flex items-center text-sm text-slate-600">
              <svg class="w-5 h-5 text-indigo-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              Gestion des litiges
            </li>
          </ul>

          <!-- Buttons -->
          <div class="flex flex-col sm:flex-row gap-3">
            <button @click="$router.push({ name: 'ContribuableLogin' })"
                    class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 px-6 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
              Connexion
            </button>
            <button @click="$router.push({ name: 'ContribuableSignup' })"
                    class="flex-1 bg-slate-100 text-slate-700 py-3 px-6 rounded-xl font-semibold hover:bg-slate-200 transition-all duration-200">
              Inscription
            </button>
          </div>
        </div>

        <!-- Agent Card -->
        <div class="group relative bg-white/80 backdrop-blur-sm rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 border border-slate-200/50 hover:border-pink-200">
          <!-- Accent Line -->
          <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-pink-500 to-red-500 rounded-t-3xl transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
          
          <!-- Icon Container -->
          <div class="mb-6 relative">
            <div class="w-16 h-16 bg-gradient-to-br from-pink-100 to-red-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <!-- Floating Badge -->
            <div class="absolute -top-2 -right-2 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center shadow-lg">
              <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
              </svg>
            </div>
          </div>

          <!-- Content -->
          <h2 class="text-2xl font-bold text-slate-900 mb-3">
            Espace Agent
          </h2>
          
          <p class="text-slate-600 mb-8 leading-relaxed">
            Interface professionnelle pour la gestion et le traitement des dossiers fiscaux des contribuables
          </p>

          <!-- Features List -->
          <ul class="space-y-2 mb-8">
            <li class="flex items-center text-sm text-slate-600">
              <svg class="w-5 h-5 text-pink-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              Validation des déclarations
            </li>
            <li class="flex items-center text-sm text-slate-600">
              <svg class="w-5 h-5 text-pink-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              Traitement des litiges
            </li>
            <li class="flex items-center text-sm text-slate-600">
              <svg class="w-5 h-5 text-pink-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              Tableau de bord analytique
            </li>
          </ul>

          <!-- Buttons -->
          <div class="flex flex-col sm:flex-row gap-3">
            <button @click="$router.push({ name: 'AgentLogin' })"
                    class="flex-1 bg-gradient-to-r from-pink-600 to-red-600 text-white py-3 px-6 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
              Connexion
            </button>
            <button @click="$router.push({ name: 'AgentSignup' })"
                    class="flex-1 bg-slate-100 text-slate-700 py-3 px-6 rounded-xl font-semibold hover:bg-slate-200 transition-all duration-200">
              Inscription
            </button>
          </div>
        </div>

      </div>

      <!-- Footer Info -->
      <div class="mt-16 text-center">
        <p class="text-sm text-slate-500">
          Plateforme sécurisée • Conforme aux normes fiscales malgaches • Support 24/7
        </p>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  name: "Home",
};
</script>

<style scoped>
@keyframes blob {
  0%, 100% {
    transform: translate(0, 0) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}
</style>