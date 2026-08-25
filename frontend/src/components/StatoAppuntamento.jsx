const etichette = {
  prenotato: 'Prenotato',
  completato: 'Completato',
  annullato: 'Annullato',
}

export default function StatoAppuntamento({ stato }) {
  return <span className={`etichetta etichetta--${stato}`}>{etichette[stato] ?? stato}</span>
}
