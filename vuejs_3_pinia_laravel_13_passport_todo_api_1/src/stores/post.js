import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from '@/services/axios'
import { extractErrors } from '@/utils/extractErrors'

export const usePostStore = defineStore('post', () => {
    const posts = ref([])
    const error = ref(null)
    const currentPage = ref(1)
    const perPage = 10
    const hasMorePosts = ref(true)
    const isFetchingPosts = ref(false)

    const normalizePosts = (payload) => {
        if (Array.isArray(payload)) return payload
        if (Array.isArray(payload?.data)) return payload.data
        return []
    }

    const getNextPage = (payload, fallbackPage) => {
        if (payload?.meta?.current_page && payload?.meta?.last_page) {
            return payload.meta.current_page < payload.meta.last_page
                ? payload.meta.current_page + 1
                : null
        }

        if (payload?.links?.next || payload?.next_page_url) {
            return fallbackPage + 1
        }

        return null
    }

    const mergePosts = (existingPosts, incomingPosts) => {
        const postMap = new Map()

        for (const post of existingPosts) {
            postMap.set(post.id, post)
        }

        for (const post of incomingPosts) {
            postMap.set(post.id, post)
        }

        return Array.from(postMap.values())
    }

    const getPosts = computed(() => posts.value)

    const resetPosts = () => {
        posts.value = []
        currentPage.value = 1
        hasMorePosts.value = true
    }

    const fetchPosts = async ({ reset = false } = {}) => {
        if (isFetchingPosts.value) return
        if (!hasMorePosts.value && !reset) return

        if (reset) {
            resetPosts()
        }

        error.value = null
        isFetchingPosts.value = true

        try {
            const requestedPage = currentPage.value
            const response = await axios.get('/posts', {
                params: {
                    page: requestedPage,
                    per_page: perPage,
                },
            })
            const fetchedPosts = normalizePosts(response.data)
            const nextPage = getNextPage(response.data, requestedPage)

            posts.value = reset
                ? fetchedPosts
                : mergePosts(posts.value, fetchedPosts)

            hasMorePosts.value = nextPage !== null && fetchedPosts.length > 0

            if (nextPage !== null) {
                currentPage.value = nextPage
            }
        } catch (err) {
            error.value = extractErrors(err)
        } finally {
            isFetchingPosts.value = false
        }
    }

    const fetchNextPosts = async () => {
        await fetchPosts()
    }

    const createPost = async (post) => {
        error.value = null
        try {
            const response = await axios.post('/posts', post)
            posts.value = normalizePosts(posts.value)
            posts.value.push(response.data)
            return null
        } catch (err) {
            const extracted = extractErrors(err)
            error.value = extracted
            return extracted
        }
    }

    const editPost = async (id, payload) => {
        error.value = null
        try {
            const response = await axios.put(`/posts/${id}`, payload)
            const updatedPost = response.data
            posts.value = normalizePosts(posts.value)
            posts.value = posts.value.map((post) =>
                post.id === id ? updatedPost : post
            )
            return null
        } catch (err) {
            const extracted = extractErrors(err)
            error.value = extracted
            return extracted
        }
    }

    const deletePost = async (id) => {
        error.value = null
        try {
            await axios.delete(`/posts/${id}`)
            posts.value = normalizePosts(posts.value)
            posts.value = posts.value.filter(p => p.id !== id)
            return null
        } catch (err) {
            const extracted = extractErrors(err)
            error.value = extracted
            return extracted
        }
    }

    return { 
        posts: getPosts, 
        error, 
        hasMorePosts,
        isFetchingPosts,
        fetchPosts, 
        fetchNextPosts,
        resetPosts,
        createPost,
        editPost,
        deletePost 
    }
})