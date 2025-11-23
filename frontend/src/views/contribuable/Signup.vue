<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 relative overflow-hidden flex items-center justify-center p-4 py-12">
    
    <!-- Decorative Background Blobs -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>

    <!-- Signup Card -->
    <div class="relative z-10 w-full max-w-4xl">
      
      <!-- Back Button -->
      <button 
        @click="$router.push('/')"
        class="mb-6 flex items-center text-slate-600 hover:text-slate-900 transition-colors duration-200 group"
      >
        <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="font-medium">Retour à l'accueil</span>
      </button>

      <!-- Main Card -->
      <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-xl p-8 md:p-10 border border-slate-200/50">
        
        <!-- Header Section -->
        <div class="text-center mb-8">
          <!-- Icon -->
          <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl mb-4">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
          </div>

          <!-- Title -->
          <h1 class="text-3xl font-bold text-slate-900 mb-2">
            Créer un compte
          </h1>
          <p class="text-slate-600">
            Rejoignez la plateforme de gestion fiscale
          </p>
        </div>

        <!-- Signup Form -->
        <form @submit.prevent="handleSignup" class="space-y-5">
          
          <!-- Name Row -->
          <div class="grid md:grid-cols-2 gap-5">
            <!-- Nom -->
            <div>
              <label for="nom" class="block text-sm font-semibold text-slate-700 mb-2">
                Nom <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <input
                  id="nom"
                  type="text"
                  v-model="formData.nom"
                  placeholder="Votre nom"
                  required
                  class="w-full pl-12 pr-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 outline-none text-slate-900 placeholder-slate-400"
                />
              </div>
            </div>

            <!-- Prenom -->
            <div>
              <label for="prenom" class="block text-sm font-semibold text-slate-700 mb-2">
                Prénom <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <input
                  id="prenom"
                  type="text"
                  v-model="formData.prenom"
                  placeholder="Votre prénom"
                  required
                  class="w-full pl-12 pr-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 outline-none text-slate-900 placeholder-slate-400"
                />
              </div>
            </div>
          </div>

          <!-- NIF -->
          <div>
            <label for="nif" class="block text-sm font-semibold text-slate-700 mb-2">
              Numéro NIF <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <input
                id="nif"
                type="text"
                v-model="formData.nif"
                placeholder="Ex: 1234567890"
                required
                class="w-full pl-12 pr-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 outline-none text-slate-900 placeholder-slate-400"
              />
            </div>
          </div>

          <!-- Email & Telephone Row -->
          <div class="grid md:grid-cols-2 gap-5">
            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                Adresse email <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                  </svg>
                </div>
                <input
                  id="email"
                  type="email"
                  v-model="formData.email"
                  placeholder="exemple@email.com"
                  required
                  class="w-full pl-12 pr-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 outline-none text-slate-900 placeholder-slate-400"
                />
              </div>
            </div>

            <!-- Telephone -->
            <div>
              <label for="telephone" class="block text-sm font-semibold text-slate-700 mb-2">
                Téléphone
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                </div>
                <input
                  id="telephone"
                  type="tel"
                  v-model="formData.telephone"
                  placeholder="+261 34 00 000 00"
                  class="w-full pl-12 pr-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 outline-none text-slate-900 placeholder-slate-400"
                />
              </div>
            </div>
          </div>

          <!-- Password Row -->
          <div class="grid md:grid-cols-2 gap-5">
            <!-- Password -->
            <div>
              <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                Mot de passe <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <input
                  id="password"
                  type="password"
                  v-model="formData.password"
                  placeholder="Minimum 6 caractères"
                  required
                  minlength="6"
                  class="w-full pl-12 pr-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 outline-none text-slate-900 placeholder-slate-400"
                />
              </div>
            </div>

            <!-- Password Confirmation -->
            <div>
              <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">
                Confirmer le mot de passe <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </div>
                <input
                  id="password_confirmation"
                  type="password"
                  v-model="formData.password_confirmation"
                  placeholder="Retapez votre mot de passe"
                  required
                  class="w-full pl-12 pr-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 outline-none text-slate-900 placeholder-slate-400"
                />
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3.5 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none mt-6"
          >
            <span v-if="!loading" class="flex items-center justify-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
              Créer mon compte
            </span>
            <span v-else class="flex items-center justify-center">
              <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Inscription en cours...
            </span>
          </button>

          <!-- Error Message -->
          <div v-if="error" class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <div>
              <p class="font-semibold text-sm">Erreur</p>
              <p class="text-sm">{{ error }}</p>
            </div>
          </div>

          <!-- Success Message -->
          <div v-if="success" class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <div>
              <p class="font-semibold text-sm">Succès</p>
              <p class="text-sm">{{ success }}</p>
            </div>
          </div>
        </form>

        <!-- Divider -->
        <div class="relative my-8">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-200"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-white text-slate-500">Vous avez déjà un compte ?</span>
          </div>
        </div>

        <!-- Login Link -->
        <div class="text-center">
          <button
            @click="$router.push({ name: 'ContribuableLogin' })"
            class="text-indigo-600 hover:text-indigo-700 font-semibold inline-flex items-center group"
          >
            Se connecter à votre compte
            <svg class="w-5 h-5 ml-1 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </button>
        </div>

      </div>

      <!-- Security Info -->
      <div class="mt-6 text-center">
        <p class="text-sm text-slate-600">
          En créant un compte, vous acceptez nos conditions d'utilisation et notre politique de confidentialité
        </p>
      </div>

    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

export default {
  name: 'ContribuableSignup',
  setup() {
    const router = useRouter()

    const formData = ref({
      nom: '',
      prenom: '',
      nif: '',
      email: '',
      telephone: '',
      password: '',
      password_confirmation: '',
      date_inscription: new Date().toISOString().split('T')[0]
    })

    const loading = ref(false)
    const error = ref('')
    const success = ref('')

    const handleSignup = async () => {
      loading.value = true
      error.value = ''
      success.value = ''

      // Vérifier que les mots de passe correspondent
      if (formData.value.password !== formData.value.password_confirmation) {
        error.value = 'Les mots de passe ne correspondent pas'
        loading.value = false
        return
      }

      try {
        await axios.post('/api/contribuables', {
          nom: formData.value.nom,
          prenom: formData.value.prenom,
          nif: formData.value.nif,
          email: formData.value.email,
          telephone: formData.value.telephone,
          password: formData.value.password,
          date_inscription: formData.value.date_inscription
        })

        success.value = 'Inscription réussie ! Redirection vers la connexion...'
        
        setTimeout(() => {
          router.push({ name: 'ContribuableLogin' })
        }, 2000)

      } catch (err) {
        console.error('Erreur inscription:', err)
        error.value = err.response?.data?.message || 'Erreur lors de l\'inscription'
      } finally {
        loading.value = false
      }
    }

    return {
      formData,
      loading,
      error,
      success,
      handleSignup
    }
  }
}
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
</style>