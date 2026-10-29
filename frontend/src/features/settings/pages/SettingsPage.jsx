import { Building2, MapPin, Phone, Receipt } from 'lucide-react'
import { FormAlert } from '@/components/common/FormAlert'
import { FormField } from '@/components/forms/FormField'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { useAuth } from '@/context/AuthContext'
import { useBusinessProfileForm } from '@/features/settings/hooks/useBusinessProfileForm'

const underlineInputClassName =
  'h-auto rounded-none border-x-0 border-t-0 border-b-[1.5px] border-input bg-transparent px-0.5 py-2 text-[14.5px] shadow-none focus-visible:border-primary focus-visible:ring-0'

export default function SettingsPage() {
  const { user } = useAuth()
  const { register, errors, values, handleSubmit, isLoading, isLoadError, isPending, isSaved, errorMessage } =
    useBusinessProfileForm()

  const displayName = values.company_name || user?.name || '—'

  return (
    <div>
      <div className="mb-5">
        <h1 className="text-lg font-semibold">Settings</h1>
        <p className="text-[13px] text-muted-foreground">
          This information appears on every invoice PDF and email you send.
        </p>
      </div>

      {isLoading ? (
        <Card className="max-w-lg">
          <CardContent className="py-10 text-center text-sm text-muted-foreground">Loading settings…</CardContent>
        </Card>
      ) : isLoadError ? (
        <Card className="max-w-lg">
          <CardContent className="py-10 text-center text-sm text-destructive">
            Couldn&apos;t load your business profile. Please try again.
          </CardContent>
        </Card>
      ) : (
        <div className="flex max-w-4xl items-start gap-7">
          <Card className="min-w-0 flex-1">
            <CardContent>
              <div className="mb-6 flex items-center gap-2.5">
                <div className="flex h-8.5 w-8.5 shrink-0 items-center justify-center rounded-[10px] bg-accent text-primary">
                  <Building2 size={16} />
                </div>
                <div>
                  <p className="text-[15px] font-semibold">Business profile</p>
                  <p className="text-xs text-muted-foreground">
                    Shown as the &quot;From&quot; party on invoices instead of your name.
                  </p>
                </div>
              </div>

              <form noValidate onSubmit={handleSubmit} className="flex flex-col gap-5.5">
                <FormField
                  id="company_name"
                  label="Company name"
                  icon={<Building2 size={9} />}
                  error={errors.company_name?.message}
                >
                  <Input
                    id="company_name"
                    placeholder={user?.name ?? 'Your company name'}
                    className={underlineInputClassName}
                    {...register('company_name')}
                  />
                </FormField>

                <div className="grid grid-cols-2 gap-5">
                  <FormField id="phone" label="Phone" icon={<Phone size={9} />} error={errors.phone?.message}>
                    <Input
                      id="phone"
                      type="tel"
                      placeholder="(555) 123-4567"
                      className={underlineInputClassName}
                      {...register('phone')}
                    />
                  </FormField>
                  <FormField id="tax_id" label="Tax ID" icon={<Receipt size={9} />} error={errors.tax_id?.message}>
                    <Input
                      id="tax_id"
                      placeholder="GSTIN / Tax ID"
                      className={underlineInputClassName}
                      {...register('tax_id')}
                    />
                  </FormField>
                </div>

                <FormField id="address" label="Address" icon={<MapPin size={9} />} error={errors.address?.message}>
                  <Textarea
                    id="address"
                    rows={2}
                    placeholder="120 Market St, Suite 4, Portland, OR"
                    className={`${underlineInputClassName} min-h-0`}
                    {...register('address')}
                  />
                </FormField>

                <FormAlert>{errorMessage}</FormAlert>

                {isSaved && !errorMessage && (
                  <p className="animate-in fade-in slide-in-from-top-1 text-sm text-success duration-200">
                    Saved.
                  </p>
                )}

                <div className="mt-1 flex items-center gap-2.5">
                  <Button
                    type="submit"
                    disabled={isPending}
                    className="flex-1 shadow-[0_8px_20px_rgba(217,119,87,0.28)]"
                  >
                    {isPending ? 'Saving…' : 'Save changes'}
                  </Button>
                </div>
              </form>
            </CardContent>
          </Card>

          <div className="sticky top-8 w-65 shrink-0">
            <p className="mb-3 pl-0.5 text-[11px] font-semibold tracking-wide text-muted-foreground uppercase">
              Preview
            </p>
            <Card>
              <CardContent>
                <p className="mb-2 text-[11px] font-semibold tracking-wide text-muted-foreground uppercase">From</p>
                <p className="text-sm font-semibold">{displayName}</p>
                <div className="mt-2.5 flex flex-col gap-2 text-[13px] text-muted-foreground">
                  {values.phone && (
                    <span className="flex items-start gap-1.75">
                      <Phone size={13} className="mt-0.5 shrink-0" />
                      <span className="min-w-0 break-words">{values.phone}</span>
                    </span>
                  )}
                  {values.address && (
                    <span className="flex items-start gap-1.75">
                      <MapPin size={13} className="mt-0.5 shrink-0" />
                      <span className="min-w-0 break-words">{values.address}</span>
                    </span>
                  )}
                  {values.tax_id && (
                    <span className="flex items-start gap-1.75">
                      <Receipt size={13} className="mt-0.5 shrink-0" />
                      <span className="min-w-0 break-words">Tax ID: {values.tax_id}</span>
                    </span>
                  )}
                </div>
              </CardContent>
            </Card>
            <p className="mt-3 pl-0.5 text-xs leading-relaxed text-muted-foreground">
              This is how your business will appear on every invoice PDF and email.
            </p>
          </div>
        </div>
      )}
    </div>
  )
}
