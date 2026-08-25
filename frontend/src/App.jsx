import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom'
import { AuthProvider } from './context/AuthContext'
import Layout from './components/Layout'
import RottaProtetta from './components/RottaProtetta'
import Home from './pages/Home'
import Login from './pages/Login'
import Registrazione from './pages/Registrazione'
import Prenotazione from './pages/Prenotazione'
import Appuntamenti from './pages/Appuntamenti'
import Referti from './pages/Referti'
import RefertoDettaglio from './pages/RefertoDettaglio'
import NuovoReferto from './pages/NuovoReferto'

export default function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes>
          <Route element={<Layout />}>
            <Route index element={<Home />} />
            <Route path="login" element={<Login />} />
            <Route path="registrazione" element={<Registrazione />} />

            <Route
              path="prenota"
              element={
                <RottaProtetta ruolo="paziente">
                  <Prenotazione />
                </RottaProtetta>
              }
            />
            <Route
              path="appuntamenti"
              element={
                <RottaProtetta>
                  <Appuntamenti />
                </RottaProtetta>
              }
            />
            <Route
              path="agenda"
              element={
                <RottaProtetta ruolo="medico">
                  <Appuntamenti />
                </RottaProtetta>
              }
            />
            <Route
              path="referti"
              element={
                <RottaProtetta>
                  <Referti />
                </RottaProtetta>
              }
            />
            <Route
              path="referti/nuovo/:appuntamentoId"
              element={
                <RottaProtetta ruolo="medico">
                  <NuovoReferto />
                </RottaProtetta>
              }
            />
            <Route
              path="referti/:id"
              element={
                <RottaProtetta>
                  <RefertoDettaglio />
                </RottaProtetta>
              }
            />

            <Route path="*" element={<Navigate to="/" replace />} />
          </Route>
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  )
}
