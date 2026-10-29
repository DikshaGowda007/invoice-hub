import { zodResolver } from '@hookform/resolvers/zod'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useEffect } from 'react'
import { useForm } from 'react-hook-form'
import { z } from 'zod'
import { businessProfileApi } from '@/api/businessProfile.api'

const businessProfileSchema = z.object({
  company_name: z.string().max(255).optional(),
  address: z.string().max(500).optional(),
  phone: z.string().max(20).optional(),
  tax_id: z.string().max(50).optional(),
})

export function useBusinessProfileForm() {
  const queryClient = useQueryClient()

  const {
    register,
    handleSubmit: handleFormSubmit,
    reset,
    watch,
    formState: { errors, isDirty },
  } = useForm({ resolver: zodResolver(businessProfileSchema) })

  const values = watch()

  const profileQuery = useQuery({
    queryKey: ['business-profile'],
    queryFn: () => businessProfileApi.get().then((res) => res.data.data.business_profile),
  })

  useEffect(() => {
    if (!profileQuery.data) return

    reset({
      company_name: profileQuery.data.company_name ?? '',
      address: profileQuery.data.address ?? '',
      phone: profileQuery.data.phone ?? '',
      tax_id: profileQuery.data.tax_id ?? '',
    })
  }, [profileQuery.data, reset])

  const mutation = useMutation({
    mutationFn: (values) => businessProfileApi.update(values).then((res) => res.data.data.business_profile),
    onSuccess: (data) => {
      queryClient.setQueryData(['business-profile'], data)
      reset({
        company_name: data.company_name ?? '',
        address: data.address ?? '',
        phone: data.phone ?? '',
        tax_id: data.tax_id ?? '',
      })
    },
  })

  return {
    register,
    errors,
    values,
    handleSubmit: handleFormSubmit((values) => mutation.mutate(values)),
    isLoading: profileQuery.isLoading,
    isLoadError: profileQuery.isError,
    isPending: mutation.isPending,
    isSaved: mutation.isSuccess && !isDirty,
    errorMessage: mutation.isError
      ? (mutation.error?.message ?? 'Something went wrong. Please try again.')
      : null,
  }
}
