import { onBeforeUnmount, onMounted, ref } from 'vue'

export const useInfiniteScroll = (options = {}) => {
    const root = options.root ?? null
    const rootMargin = options.rootMargin ?? '300px 0px'
    const threshold = options.threshold ?? 0.1
    const canLoadMore = options.canLoadMore ?? (() => true)
    const isLoading = options.isLoading ?? (() => false)
    const onLoadMore = options.onLoadMore ?? (async () => {})

    const sentinel = ref(null)
    let observer = null

    const triggerLoadMore = async () => {
        if (isLoading() || !canLoadMore()) return
        await onLoadMore()
    }

    const disconnectObserver = () => {
        if (observer) {
            observer.disconnect()
            observer = null
        }
    }

    const setupObserver = () => {
        if (!sentinel.value || observer) return

        observer = new IntersectionObserver(
            async (entries) => {
                const first = entries[0]
                if (first?.isIntersecting) {
                    await triggerLoadMore()
                }
            },
            {
                root,
                rootMargin,
                threshold,
            },
        )

        observer.observe(sentinel.value)
    }

    onMounted(() => {
        setupObserver()
    })

    onBeforeUnmount(() => {
        disconnectObserver()
    })

    return {
        sentinel,
        triggerLoadMore,
        setupObserver,
        disconnectObserver,
    }
}