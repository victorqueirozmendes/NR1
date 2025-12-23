import { useState, useEffect } from 'react';
import { courseService } from '../services';
import './AdminPages.css';

export function AdminCourses() {
  const [courses, setCourses] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [formData, setFormData] = useState({ nome: '', descricao: '' });

  useEffect(() => {
    loadCourses();
  }, []);

  const loadCourses = async () => {
    try {
      const data = await courseService.getCourses();
      setCourses(data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await courseService.createCourse(formData.nome, formData.descricao);
      setFormData({ nome: '', descricao: '' });
      setShowForm(false);
      loadCourses();
    } catch (err) {
      console.error(err);
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm('Tem certeza que deseja deletar este curso?')) {
      try {
        await courseService.deleteCourse(id);
        loadCourses();
      } catch (err) {
        console.error(err);
      }
    }
  };

  return (
    <div className="container">
      <div className="admin-header">
        <h1>Gerenciar Cursos</h1>
        <button onClick={() => setShowForm(!showForm)}>+ Novo Curso</button>
      </div>

      {showForm && (
        <div className="card">
          <h2>Novo Curso</h2>
          <form onSubmit={handleSubmit}>
            <div className="form-group">
              <label>Nome do Curso</label>
              <input
                type="text"
                value={formData.nome}
                onChange={(e) => setFormData({ ...formData, nome: e.target.value })}
                required
              />
            </div>
            <div className="form-group">
              <label>Descrição</label>
              <textarea
                value={formData.descricao}
                onChange={(e) => setFormData({ ...formData, descricao: e.target.value })}
                rows="4"
              />
            </div>
            <button type="submit">Criar Curso</button>
          </form>
        </div>
      )}

      {loading ? (
        <p>Carregando cursos...</p>
      ) : (
        <div className="admin-table">
          <table>
            <thead>
              <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              {courses.map(course => (
                <tr key={course.id}>
                  <td>{course.nome}</td>
                  <td>{course.descricao || '-'}</td>
                  <td>
                    <a href={`/admin/cursos/${course.id}/editar`} className="btn-edit">Editar</a>
                    <button onClick={() => handleDelete(course.id)} className="btn-delete">Deletar</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}

export function AdminUsers() {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [formData, setFormData] = useState({ nome: '', email: '', senha: '', role: 'aluno' });

  useEffect(() => {
    loadUsers();
  }, []);

  const loadUsers = async () => {
    try {
      // Implementar carregamento de usuários
      setUsers([]);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    // Implementar criação de usuário
  };

  return (
    <div className="container">
      <div className="admin-header">
        <h1>Gerenciar Usuários</h1>
        <button onClick={() => setShowForm(!showForm)}>+ Novo Usuário</button>
      </div>

      {showForm && (
        <div className="card">
          <h2>Novo Usuário</h2>
          <form onSubmit={handleSubmit}>
            <div className="form-group">
              <label>Nome</label>
              <input
                type="text"
                value={formData.nome}
                onChange={(e) => setFormData({ ...formData, nome: e.target.value })}
                required
              />
            </div>
            <div className="form-group">
              <label>Email</label>
              <input
                type="email"
                value={formData.email}
                onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                required
              />
            </div>
            <div className="form-group">
              <label>Senha</label>
              <input
                type="password"
                value={formData.senha}
                onChange={(e) => setFormData({ ...formData, senha: e.target.value })}
                required
              />
            </div>
            <div className="form-group">
              <label>Role</label>
              <select
                value={formData.role}
                onChange={(e) => setFormData({ ...formData, role: e.target.value })}
              >
                <option value="aluno">Aluno</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <button type="submit">Criar Usuário</button>
          </form>
        </div>
      )}

      {loading ? (
        <p>Carregando usuários...</p>
      ) : users.length === 0 ? (
        <p className="text-muted">Nenhum usuário cadastrado.</p>
      ) : (
        <div className="admin-table">
          <table>
            <thead>
              <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              {users.map(user => (
                <tr key={user.id}>
                  <td>{user.nome}</td>
                  <td>{user.email}</td>
                  <td>{user.role}</td>
                  <td>{user.ativo ? 'Ativo' : 'Inativo'}</td>
                  <td>
                    <button className="btn-edit">Editar</button>
                    <button className="btn-delete">Deletar</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
