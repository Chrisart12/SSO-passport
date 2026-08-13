export const extractErrors = (error) => {
    return (
        error.response?.data?.errors ??
        error.response?.data?.message ??
        error.message ??
        'Unknown error'
    )
}