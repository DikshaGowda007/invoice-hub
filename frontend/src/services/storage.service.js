const TOKEN_KEY = 'token'
const USER_KEY = 'user'
const THEME_KEY = 'theme'

export const storage = {
  getToken: () => localStorage.getItem(TOKEN_KEY),
  setToken: (token) => localStorage.setItem(TOKEN_KEY, token),

  getUser: () => {
    try {
      return JSON.parse(localStorage.getItem(USER_KEY))
    } catch {
      return null
    }
  },
  setUser: (user) => localStorage.setItem(USER_KEY, JSON.stringify(user)),

  getTheme: () => localStorage.getItem(THEME_KEY),
  setTheme: (theme) => localStorage.setItem(THEME_KEY, theme),

  clear: () => {
    localStorage.removeItem(TOKEN_KEY)
    localStorage.removeItem(USER_KEY)
  },
}
