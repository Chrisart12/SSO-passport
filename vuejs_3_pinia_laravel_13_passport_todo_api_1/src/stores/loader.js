import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useLoaderStore = defineStore('loader', () => {

    const loading = ref(false) 

    const showLoader = () => {
        loading.value = true
    }

    const hideLoader = () => {
        loading.value = false
    }

    const withLoader = async (callback) => {
        try {
            loading.value = true
            await callback()
        } finally {
            loading.value = false
        }
    }

    return {
        loading,
        showLoader,
        hideLoader,
        withLoader
    }
    
})