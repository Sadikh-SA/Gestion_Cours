import './App.css';
import { BrowserRouter as Router, Route, Switch, Routes } from 'react-router-dom';
import LoginRegister from './pages/LoginRegister';
import Cours from './pages/Cours';
import Chapitre from './pages/Chapitre';
//import Projet from './pages/Projet';

function App() {
  return (
    <div className="App">
      <div className="container mt-3">
        <Router>
          <Routes>
            <Route path="/" element={<LoginRegister />}/>
            <Route path="cours" element={<Cours />} />
            <Route path="chapitre/cours/:id" element={<Chapitre />} />
          </Routes>
        </Router>
      </div>
    </div>
  );
}

export default App;