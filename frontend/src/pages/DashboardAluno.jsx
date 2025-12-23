import { useContext, useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';
import { cursosAPI } from '../api/endpoints';
import '../styles/dashboard.css';

export function DashboardAluno() {
  const { user, logout } = useContext(AuthContext);
  const navigate = useNavigate();
  const [cursos, setCursos] = useState([]);
  const [carregando, setCarregando] = useState(true);

  useEffect(() => {
    carregarCursos();
  }, []);

  const carregarCursos = async () => {
    try {
      const response = await cursosAPI.meusCursos();
      setCursos(response.data);
    } catch (error) {
      console.error('Erro ao carregar cursos:', error);
    } finally {
      setCarregando(false);
    }
  };

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  return (
    <div className="dashboard">
      <header className="dashboard-header">
        <h1>NR1 EAD</h1>
        <div className="user-info">
          <span>{user?.nome}</span>
          <button onClick={handleLogout}>Sair</button>
        </div>
      </header>

      <main className="dashboard-main">
        <h2>Meus Cursos</h2>

        {carregando && <p>Carregando cursos...</p>}

        {!carregando && cursos.length === 0 && (
          <p>Você ainda não tem acesso a nenhum curso.</p>
        )}

        {cursos.length > 0 && (
          <div className="cursos-grid">
            {cursos.map((curso) => (
              <div key={curso.id} className="curso-card">
                <h3>{curso.nome}</h3>
                <p>{curso.descricao}</p>
                <button onClick={() => navigate(`/curso/${curso.id}`)}>
                  Acessar Curso
                </button>
              </div>
            ))}
          </div>
        )}
      </main>
    </div>
  );
}
