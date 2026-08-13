const trimTrailingSlash = (value) => value.replace(/\/$/, '')

const backendUrl = trimTrailingSlash(import.meta.env.VITE_BACKEND_URL || 'http://localhost:8002')
const apiBaseUrl = trimTrailingSlash(import.meta.env.VITE_API_BASE_URL || `${backendUrl}/api/v1`)

export { backendUrl, apiBaseUrl }