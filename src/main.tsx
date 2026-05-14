import React from "react"
import ReactDOM from "react-dom/client"
import { HashRouter, Navigate, Route, Routes } from "react-router-dom"

const routes = [
  { path: "/", src: "pages/index.html", title: "GALA DENT" },
  { path: "/o-klinice", src: "pages/o-klinice.html", title: "GALA DENT - O klinice" },
  { path: "/oferta", src: "pages/oferta.html", title: "GALA DENT - Oferta i cennik" },
  { path: "/sprzet", src: "pages/sprzet.html", title: "GALA DENT - Sprzęt" },
  { path: "/kontakt", src: "pages/kontakt.html", title: "GALA DENT - Kontakt" },
]

function Frame({ src, title }: { src: string; title: string }) {
  return (
    <iframe
      key={src}
      title={title}
      src={src}
      style={{ width: "100%", minHeight: "100vh", height: "100vh", border: 0, display: "block", background: "#fbf9f3" }}
    />
  )
}

function App() {
  return (
    <HashRouter>
      <Routes>
        {routes.map((r) => (
          <Route key={r.path} path={r.path} element={<Frame src={r.src} title={r.title} />} />
        ))}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </HashRouter>
  )
}

ReactDOM.createRoot(document.getElementById("root")!).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>,
)
