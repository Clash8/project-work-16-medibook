import { NavLink, Outlet, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

/** Intelaiatura comune: intestazione, navigazione per ruolo e area contenuti. */
export default function Layout() {
  const { utente, autenticato, isMedico, isPaziente, logout } = useAuth()
  const navigate = useNavigate()

  async function esci() {
    await logout()
    navigate('/login')
  }

  return (
    <div className="app">
      <header className="intestazione">
        <div className="contenitore intestazione__interno">
          <NavLink to="/" className="marchio">
            <span className="marchio__icona" aria-hidden="true">+</span>
            MediBook
          </NavLink>

          <nav className="navigazione">
            {isPaziente && (
              <>
                <NavLink to="/prenota">Prenota una visita</NavLink>
                <NavLink to="/appuntamenti">I miei appuntamenti</NavLink>
                <NavLink to="/referti">I miei referti</NavLink>
              </>
            )}
            {isMedico && (
              <>
                <NavLink to="/agenda">Agenda</NavLink>
                <NavLink to="/referti">Referti emessi</NavLink>
              </>
            )}
          </nav>

          <div className="intestazione__utente">
            {autenticato ? (
              <>
                <span className="utente__nome">{utente.nome_completo}</span>
                <button type="button" className="bottone bottone--chiaro" onClick={esci}>
                  Esci
                </button>
              </>
            ) : (
              <NavLink to="/login" className="bottone bottone--chiaro">
                Accedi
              </NavLink>
            )}
          </div>
        </div>
      </header>

      <main className="contenitore contenuto">
        <Outlet />
      </main>

      <footer className="pie">
        <div className="contenitore">
          MediBook &middot; Applicazione full-stack API-based per una clinica privata
        </div>
      </footer>
    </div>
  )
}
