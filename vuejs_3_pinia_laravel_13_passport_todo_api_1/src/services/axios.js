import axios from "axios"
import { apiBaseUrl } from '@/config/env'

axios.defaults.baseURL = apiBaseUrl

axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
// axios.defaults.withXSRFToken = true // axios.defaults.xsrfCookieName = 'XSRF-TOKEN'

export default axios