export default function Caricamento({ messaggio = 'Caricamento in corso...' }) {
  return (
    <div className="stato stato--caricamento" role="status">
      <span className="spinner" aria-hidden="true" />
      {messaggio}
    </div>
  )
}
