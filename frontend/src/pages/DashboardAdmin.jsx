import { useState, useContext, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';
import { cursosAPI, usuariosAPI, progressoAPI } from '../api/endpoints';
import '../styles/admin.css';

export function DashboardAdmin() {
  const { user, logout } = useContext(AuthContext);
  const navigate = useNavigate();
  const [aba, setAba] = useState('usuarios');
  const [usuarios, setUsuarios] = useState([]);
  const [cursos, setCursos] = useState([]);
  const [carregando, setCarregando] = useState(false);
  const [novoUsuario, setNovoUsuario] = useState({ nome: '', email: '', senha: '', role: 'aluno' });
  const [novoCurso, setNovoCurso] = useState({ nome: '', descricao: '' });

  useEffect(() => {
    if (aba === 'usuarios') carregarUsuarios();
    if (aba === 'cursos') carregarCursos();
  }, [aba]);

  const carregarUsuarios = async () => {
    setCarregando(true);
    try {
      const response = await usuariosAPI.listar();
      setUsuarios(response.data);
    } catch (error) {
      console.error('Erro ao carregar usuários:', error);
    } finally {
      setCarregando(false);
    }
  };

  const carregarCursos = async () => {
    setCarregando(true);
    try {
      const response = await cursosAPI.listar();
      setCursos(response.data);
    } catch (error) {
      console.error('Erro ao carregar cursos:', error);
    } finally {
      setCarregando(false);
    }
  };

  const criarUsuario = async (e) => {
    e.preventDefault();
    try {
      await usuariosAPI.criar(novoUsuario.nome, novoUsuario.email, novoUsuario.senha, novoUsuario.role);
      setNovoUsuario({ nome: '', email: '', senha: '', role: 'aluno' });
      carregarUsuarios();
      alert('Usuário criado com sucesso!');
    } catch (error) {
      alert(error.response?.data?.message || 'Erro ao criar usuário');
    }
  };

  const criarCurso = async (e) => {
    e.preventDefault();
    try {
      await cursosAPI.criar(novoCurso.nome, novoCurso.descricao);
      setNovoCurso({ nome: '', descricao: '' });
      carregarCursos();
      alert('Curso criado com sucesso!');
    } catch (error) {
      alert(error.response?.data?.message || 'Erro ao criar curso');
    }
  };

  const toggleUsuario = async (id, ativo) => {
    try {
      await usuariosAPI.toggleAtivo(id, !ativo);
      carregarUsuarios();
    } catch (error) {
      alert('Erro ao atualizar usuário');
    }
  };

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  return (
    <div className="admin-dashboard">
      <header className="admin-header">
        <h1>NR1 EAD - Painel Admin</h1>
        <div className="user-info">
          <span>{user?.nome}</span>
          <button onClick={handleLogout}>Sair</button>
        </div>
      </header>

      <div className="admin-container">
        <aside className="admin-sidebar">
          <nav>
            <button className={aba === 'usuarios' ? 'ativo' : ''} onClick={() => setAba('usuarios')}>
              👥 Usuários
            </button>
            <button className={aba === 'cursos' ? 'ativo' : ''} onClick={() => setAba('cursos')}>
              📚 Cursos
            </button>
          </nav>
        </aside>

        <main className="admin-main">
          {aba === 'usuarios' && (
            <section className="admin-section">
              <h2>Gerenciar Usuários</h2>

              <form className="admin-form" onSubmit={criarUsuario}>
                <h3>Criar Novo Usuário</h3>
                <input
                  type="text"
                  placeholder="Nome"
                  value={novoUsuario.nome}
                  onChange={(e) => setNovoUsuario({ ...novoUsuario, nome: e.target.value })}
                  required
                />
                <input
                  type="email"
                  placeholder="Email"
                  value={novoUsuario.email}
                  onChange={(e) => setNovoUsuario({ ...novoUsuario, email: e.target.value })}
                  required
                />
                <input
                  type="password"
                  placeholder="Senha"
                  value={novoUsuario.senha}
                  onChange={(e) => setNovoUsuario({ ...novoUsuario, senha: e.target.value })}
                  required
                />
                <select
                  value={novoUsuario.role}
                  onChange={(e) => setNovoUsuario({ ...novoUsuario, role: e.target.value })}
                >
                  <option value="aluno">Aluno</option>
                  <option value="admin">Admin</option>
                </select>
                <button type="submit">Criar Usuário</button>
              </form>

              {carregando ? (
                <p>Carregando...</p>
              ) : (
                <table className="admin-table">
                  <thead>
                    <tr>
                      <th>Nome</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th>Ativo</th>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    {usuarios.map((u) => (
                      <tr key={u.id}>
                        <td>{u.nome}</td>
                        <td>{u.email}</td>
                        <td>{u.role}</td>
                        <td>{u.ativo ? '✓' : '✗'}</td>
                        <td>
                          <button onClick={() => toggleUsuario(u.id, u.ativo)}>
                            {u.ativo ? 'Bloquear' : 'Ativar'}
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              )}
            </section>
          )}

          {aba === 'cursos' && (
            <section className="admin-section">
              <h2>Gerenciar Cursos</h2>

              <form className="admin-form" onSubmit={criarCurso}>
                <h3>Criar Novo Curso</h3>
                <input
                  type="text"
                  placeholder="Nome do Curso"
                  value={novoCurso.nome}
                  onChange={(e) => setNovoCurso({ ...novoCurso, nome: e.target.value })}
                  required
                />
                <textarea
                  placeholder="Descrição"
                  value={novoCurso.descricao}
                  onChange={(e) => setNovoCurso({ ...novoCurso, descricao: e.target.value })}
                />
                <button type="submit">Criar Curso</button>
              </form>

              {carregando ? (
                <p>Carregando...</p>
              ) : (
                <div className="cursos-admin">
                  {cursos.map((curso) => (
                    <div key={curso.id} className="curso-admin-card">
                      <h3>{curso.nome}</h3>
                      <p>{curso.descricao}</p>
                      <button onClick={() => navigate(`/admin/curso/${curso.id}`)}>
                        Gerenciar
                      </button>
                    </div>
                  ))}
                </div>
              )}
            </section>
          )}
        </main>
      </div>
    </div>
  );
}
