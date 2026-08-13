import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from '@/services/axios'
import { extractErrors } from '@/utils/extractErrors' // Permet d'extraire les erreurs de manière cohérente
import { backendUrl } from '@/config/env'

export const useAuthStore = defineStore('auth', () => {

  // Fonction pour obtenir le cookie CSRF, nécessaire pour les requêtes d'authentification seulement, une fois authentifié, il n'est plus nécessaire de l'appeler à chaque requête
  const getCsrfCookie = () => axios.get(`${backendUrl}/sanctum/csrf-cookie`)

  const user = ref(null)
  const authenticated = ref(false)
  const isAuthResolved = ref(false)

  const isAuthenticated = computed(() => authenticated.value)

  const setAuthenticated = (value) => {
    authenticated.value = value
  }

  const setUser = (userData) => {
    user.value = userData
  }

  const logout = async() => {
    try {
        await axios.post('/logout')
        setUser(null)
        setAuthenticated(false)

        
      // Navigation réelle (pas AJAX) vers Passport pour fermer AUSSI sa
      // propre session web — sinon un prochain "Login" réutilise la session
      // Passport encore active et reconnecte instantanément sans repasser
      // par le formulaire.
      window.location.href ='http://localhost:8000/oauth/logout?redirect_uri=' +
      encodeURIComponent('http://localhost:5174/')

        return null
    } catch (errors) {
        return extractErrors(errors)
    }
  }

  const attempt = async () => {
    try {
        // Simulate an API call to check authentication status
        const response = await axios.get('/user'); 
        console.log('User data fetched:', response.data); // Log the user data for debugging
        setUser(response.data)
        setAuthenticated(true)
    } catch (erreurs ) {
        setUser(null)
        setAuthenticated(false)
      // Nous ne retournons pas les erreurs ici, car cette fonction est principalement utilisée pour vérifier l'état d'authentification au démarrage de l'application
    } finally {
        isAuthResolved.value = true
    }
  }

  return {
    user,
    authenticated,
    isAuthResolved,
    isAuthenticated,
    setAuthenticated,
    setUser,
    // login,
    // register,
    logout,
    attempt
  }


})
