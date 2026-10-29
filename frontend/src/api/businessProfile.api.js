import axiosClient from './axios'
import { API } from '@/constants/api'

export const businessProfileApi = {
  get: () => axiosClient.post(API.BUSINESS_PROFILE.GET),
  update: (payload) => axiosClient.post(API.BUSINESS_PROFILE.UPDATE, payload),
}
