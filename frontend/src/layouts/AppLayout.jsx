import { LogOut, Moon, Sun } from 'lucide-react'
import { NavLink, Outlet } from 'react-router-dom'
import { useAuth } from '@/context/AuthContext'
import { useTheme } from '@/context/ThemeContext'
import { ROUTES } from '@/utils/routePaths'

const navItemClassName = ({ isActive }) =>
  `flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-[13.5px] font-medium transition-colors ${
    isActive ? 'bg-accent text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground'
  }`

function DashboardIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
      <rect x="3" y="3" width="7" height="7" rx="1.5" />
      <rect x="14" y="3" width="7" height="7" rx="1.5" />
      <rect x="3" y="14" width="7" height="7" rx="1.5" />
      <rect x="14" y="14" width="7" height="7" rx="1.5" />
    </svg>
  )
}

function ClientsIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
      <circle cx="9" cy="8" r="3.2" />
      <path d="M3.5 20c0-3.3 2.5-5.5 5.5-5.5s5.5 2.2 5.5 5.5" />
      <circle cx="17" cy="8.5" r="2.6" />
      <path d="M15.8 14.8c2.6 0.3 4.7 2.3 4.7 5.2" />
    </svg>
  )
}

function InvoicesIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
      <path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" />
      <path d="M14 3v5h5" />
      <path d="M8.5 13h7" />
      <path d="M8.5 16.5h7" />
    </svg>
  )
}

function RecurringIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
      <path d="M17 2.1 21 6l-4 3.9" />
      <path d="M3 11V9a4 4 0 0 1 4-4h14" />
      <path d="M7 21.9 3 18l4-3.9" />
      <path d="M21 13v2a4 4 0 0 1-4 4H3" />
    </svg>
  )
}

function SettingsIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
      <path d="M4 6h9" />
      <path d="M17 6h3" />
      <circle cx="14.5" cy="6" r="2" />
      <path d="M4 12h3" />
      <path d="M11 12h9" />
      <circle cx="8" cy="12" r="2" />
      <path d="M4 18h9" />
      <path d="M17 18h3" />
      <circle cx="14.5" cy="18" r="2" />
    </svg>
  )
}

export function AppLayout() {
  const { user, logout } = useAuth()
  const { theme, toggleTheme } = useTheme()
  const initials = (user?.name ?? '')
    .split(' ')
    .map((part) => part[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()

  return (
    <div className="flex min-h-svh bg-background">
      <aside className="sticky top-0 flex h-svh w-60 min-w-60 flex-col border-r border-border bg-card p-3">
        <NavLink to={ROUTES.HOME} className="flex items-center gap-2.5 px-2 pt-1 pb-6">
          <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-[10px] bg-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
              <path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" />
              <path d="M9.5 12h5" />
              <path d="M9.5 15.5h5" />
            </svg>
          </span>
          <span className="text-[15px] font-semibold tracking-tight">InvoiceHub</span>
        </NavLink>

        <nav className="flex flex-col gap-0.5">
          <NavLink to={ROUTES.HOME} end className={navItemClassName}>
            <DashboardIcon />
            Dashboard
          </NavLink>
          <NavLink to={ROUTES.CLIENTS} className={navItemClassName}>
            <ClientsIcon />
            Clients
          </NavLink>
          <NavLink to={ROUTES.INVOICES} className={navItemClassName}>
            <InvoicesIcon />
            Invoices
          </NavLink>
          <NavLink to={ROUTES.RECURRING_INVOICES} className={navItemClassName}>
            <RecurringIcon />
            Recurring
          </NavLink>
          <NavLink to={ROUTES.SETTINGS} className={navItemClassName}>
            <SettingsIcon />
            Settings
          </NavLink>
        </nav>

        <div className="flex-1" />

        <div className="flex items-center gap-2.5 border-t border-border px-2 pt-2.5">
          <div className="flex h-[30px] w-[30px] shrink-0 items-center justify-center rounded-full bg-muted text-[12.5px] font-semibold">
            {initials || 'U'}
          </div>
          <div className="min-w-0 flex-1">
            <p className="truncate text-[13px] font-medium">{user?.name}</p>
            <p className="truncate text-[11.5px] text-muted-foreground">{user?.email}</p>
          </div>
          <button
            type="button"
            onClick={toggleTheme}
            aria-label={theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'}
            className="shrink-0 rounded-lg p-1.5 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
          >
            {theme === 'dark' ? <Sun size={15} /> : <Moon size={15} />}
          </button>
          <button
            type="button"
            onClick={logout}
            aria-label="Log out"
            className="shrink-0 rounded-lg p-1.5 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
          >
            <LogOut size={15} />
          </button>
        </div>
      </aside>

      <main className="min-w-0 flex-1 px-8 py-8">
        <Outlet />
      </main>
    </div>
  )
}
