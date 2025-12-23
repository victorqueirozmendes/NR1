import { useState, useEffect } from 'react';
import { courseService } from '../services';
import './Dashboard.css';

export function StudentDashboard() {
  const [courses, setCourses] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    loadCourses();
  }, []);

  const loadCourses = async () => {
    try {
      setLoading(true);
      const data = await courseService.getStudentCourses();
      setCourses(data);
    } catch (err) {
      setError('Erro ao carregar cursos');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="container">
      <h1>Meus Cursos</h1>
      {error && <div className="alert alert-error">{error}</div>}
      {loading ? (
        <p>Carregando cursos...</p>
      ) : courses.length === 0 ? (
        <p className="text-muted">Você não tem acesso a nenhum curso ainda.</p>
      ) : (
        <div className="grid">
          {courses.map(course => (
            <div key={course.id} className="card">
              <h3>{course.nome}</h3>
              <p>{course.descricao}</p>
              <a href={`/curso/${course.id}`} className="btn-primary">Acessar Curso</a>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

export function AdminDashboard() {
  const [stats, setStats] = useState({ courses: 0, students: 0 });

  return (
    <div className="container">
      <h1>Painel Administrativo</h1>
      <div className="dashboard-stats">
        <div className="stat-card">
          <h3>Cursos</h3>
          <p className="stat-number">0</p>
          <a href="/admin/cursos">Gerenciar</a>
        </div>
        <div className="stat-card">
          <h3>Alunos</h3>
          <p className="stat-number">0</p>
          <a href="/admin/usuarios">Gerenciar</a>
        </div>
      </div>
    </div>
  );
}
