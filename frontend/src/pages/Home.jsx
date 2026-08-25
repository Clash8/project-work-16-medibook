import { Link } from 'react-router-dom'
import { useApi } from '../hooks/useApi'
import { useAuth } from '../context/AuthContext'
import Caricamento from '../components/Caricamento'

/** Vetrina pubblica della clinica con l'elenco delle specialità erogate. */
export default function Home() {
  const { autenticato, isMedico } = useAuth()
  const { elementi, caricamento } = useApi('/specialita')

  return (
    <>
      <section className="eroe">
        <h1>La tua clinica, a portata di clic</h1>
        <p>
          Consulta le disponibilità dei nostri specialisti, prenota una visita in pochi passaggi
          e ritrova i tuoi referti in un unico luogo sicuro.
        </p>
        <div className="eroe__azioni">
          {autenticato ? (
            <Link className="bottone bottone--primario" to={isMedico ? '/agenda' : '/prenota'}>
              {isMedico ? "Vai all'agenda" : 'Prenota una visita'}
            </Link>
          ) : (
            <>
              <Link className="bottone bottone--primario" to="/registrazione">
                Registrati
              </Link>
              <Link className="bottone bottone--chiaro" to="/login">
                Accedi
              </Link>
            </>
          )}
        </div>
      </section>

      <section className="sezione">
        <h2>Le nostre specialità</h2>

        {caricamento ? (
          <Caricamento />
        ) : (
          <div className="griglia griglia--tre">
            {elementi.map((specialita) => (
              <article key={specialita.id} className="cartellino">
                <h3>{specialita.nome}</h3>
                <p>{specialita.descrizione}</p>
                <p className="meta">
                  Durata {specialita.durata_visita_minuti} minuti &middot;{' '}
                  {specialita.costo.toFixed(2)} EUR
                </p>
              </article>
            ))}
          </div>
        )}
      </section>
    </>
  )
}
